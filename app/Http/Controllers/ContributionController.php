<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\IuranSchedule;
use App\Models\User;
use App\Services\ActivityLog;
use App\Services\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContributionController extends Controller
{
    public function index(Request $request)
    {
        $query = Contribution::query()
            ->with(['user', 'schedule'])
            ->when(
                ! $request->user()->isAdmin(),
                fn ($q) => $q->where('user_id', $request->user()->id),
            );

        // Privasi per RT: Ketua RT hanya melihat iuran RT-nya sendiri.
        $this->applyRtScope($request, $query);

        if ($request->filled('status') && in_array($request->status, ['pending', 'lunas', 'ditolak'])) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('search') && $request->user()->isAdmin()) {
            $search = '%'.$request->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('payer_name', 'like', $search)
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $search));
            });
        }

        $contributions = $query->latest('id')->paginate(10)->withQueryString();

        $base = $request->user()->isAdmin()
            ? Contribution::query()
            : Contribution::query()->where('user_id', $request->user()->id);

        $this->applyRtScope($request, $base);

        $totalPending = (clone $base)->where('payment_status', 'pending')->count();
        $totalPaid = (clone $base)->where('payment_status', 'lunas')->count();

        // Jadwal iuran aktif untuk RT warga + tagihan pending miliknya (banner info).
        $activeSchedule = null;
        $myPendingBill = null;
        $schedulePaid = false;
        if ($request->user()->isWarga()) {
            $activeSchedule = IuranSchedule::with('rt')
                ->where('rt_id', $request->user()->rt_id)
                ->orderByDesc('year')->orderByDesc('month')
                ->first();
            $myPendingBill = (clone $base)->where('payment_status', 'pending')->latest('id')->first();
            $schedulePaid = $activeSchedule
                ? (clone $base)->where('iuran_schedule_id', $activeSchedule->id)->where('payment_status', 'lunas')->exists()
                : false;
        }

        return view('contributions.index', compact(
            'contributions',
            'totalPending',
            'totalPaid',
            'activeSchedule',
            'myPendingBill',
            'schedulePaid',
        ));
    }

    public function create()
    {
        if (request()->user()->isWarga()) {
            $user = request()->user();

            // Warga yang masih punya tagihan langsung diarahkan untuk membayarnya.
            $pending = $user->contributions()->where('payment_status', 'pending')->latest('id')->first();
            if ($pending) {
                return redirect()->route('contributions.show', $pending)
                    ->with('info', 'Anda masih memiliki tagihan iuran yang belum dibayar. Silakan selesaikan di bawah ini.');
            }

            // Nominal dikunci dari jadwal iuran RT. Belum ada jadwal = belum ada tagihan.
            $schedule = IuranSchedule::where('rt_id', $user->rt_id)
                ->orderByDesc('year')->orderByDesc('month')
                ->first();

            if (! $schedule) {
                return redirect()->route('contributions.index')
                    ->with('info', 'Belum ada iuran bulan ini dari Ketua RT. Silakan menunggu pengumuman.');
            }

            // Iuran periode ini sudah lunas — tidak perlu bayar lagi.
            $alreadyPaid = $user->contributions()
                ->where('iuran_schedule_id', $schedule->id)
                ->where('payment_status', 'lunas')
                ->exists();
            if ($alreadyPaid) {
                return redirect()->route('contributions.index')
                    ->with('info', $schedule->full_label.' Anda sudah lunas.');
            }

            return view('contributions.create', compact('schedule'));
        }

        abort_unless(request()->user()->isAdmin(), 403);

        $warga = User::query()->where('role', 'warga')->orderBy('name')->get();

        return view('contributions.create', compact('warga'));
    }

    public function store(Request $request)
    {
        if ($request->user()->isWarga()) {
            $user = $request->user();

            // Nominal iuran ditentukan Ketua RT — abaikan nominal dari input.
            $schedule = IuranSchedule::where('rt_id', $user->rt_id)
                ->orderByDesc('year')->orderByDesc('month')
                ->first();

            $validated = $request->validate([
                'amount' => [$schedule ? 'nullable' : 'required', 'numeric', 'min:0'],
                'payment_method' => ['required', Rule::in(['virtual_account', 'qris', 'transfer'])],
                'proof_of_payment' => ['nullable', 'string', 'max:255'],
            ]);

            if ($schedule) {
                // Jangan gandakan tagihan untuk jadwal yang sama.
                $existing = $user->contributions()
                    ->where('iuran_schedule_id', $schedule->id)
                    ->where('payment_status', 'pending')
                    ->first();
                if ($existing) {
                    return redirect()->route('contributions.show', $existing)
                        ->with('info', 'Tagihan '.$schedule->full_label.' Anda sudah ada. Silakan selesaikan di bawah ini.');
                }

                $alreadyPaid = $user->contributions()
                    ->where('iuran_schedule_id', $schedule->id)
                    ->where('payment_status', 'lunas')
                    ->exists();
                if ($alreadyPaid) {
                    return redirect()->route('contributions.index')
                        ->with('info', $schedule->full_label.' Anda sudah lunas.');
                }
            }

            $contrib = $user->contributions()->create([
                'iuran_schedule_id' => $schedule?->id,
                'rt_id' => $user->rt_id,
                'payer_name' => $user->name,
                'amount' => $schedule ? $schedule->amount : $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_code' => $this->generatePaymentCode(),
                'payment_status' => 'pending',
                'proof_of_payment' => $validated['proof_of_payment'] ?? null,
            ]);

            $managers = Notifier::managersForRt($user->rt_id);
            Notifier::sendMany(
                $managers,
                'Pembayaran iuran baru',
                $user->name.' mengajukan pembayaran iuran '.number_format($contrib->amount, 0, ',', '.'),
                route('contributions.show', $contrib),
                $user,
            );

            ActivityLog::record($user, 'iuran', 'mengajukan pembayaran iuran', $contrib->payment_code.' (Rp '.number_format($contrib->amount, 0, ',', '.').')');

            return redirect()->route('contributions.index')
                ->with('success', 'Pembayaran iuran Anda berhasil diajukan. Silakan selesaikan pembayaran online untuk melunasi.');
        }

        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'user_id' => ['nullable', 'required_without:payer_name', 'exists:users,id'],
            'payer_name' => ['nullable', 'required_without:user_id', 'string', 'max:100'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', Rule::in(['cash', 'virtual_account', 'qris', 'transfer'])],
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
            'proof_of_payment' => ['nullable', 'string', 'max:255'],
        ]);

        $status = $validated['payment_status'];
        $paidAt = $status === 'lunas' ? now() : null;
        $proof = $validated['proof_of_payment'] ?? null;
        $method = $validated['payment_method'] ?? 'cash';
        $amount = $validated['amount'] ?? 50000;

        // Jika admin pilih akun warga tapi tidak isi nama manual, pakai nama akun.
        // Jika isi manual (pembayar tanpa HP/akun), pakai nama manual.
        $payerName = $validated['payer_name'] ?? null;
        $userId = $validated['user_id'] ?? null;
        if (empty($payerName) && ! empty($userId)) {
            $payerName = User::whereKey($userId)->value('name');
        }

        if ($status === 'lunas' && empty($proof)) {
            if ($method === 'cash') {
                $proof = 'Diterima tunai secara langsung oleh Pengurus RT';
            }
        }

        Contribution::create([
            'user_id' => $userId,
            'payer_name' => $payerName,
            // Catatan manual Ketua RT otomatis milik RT-nya (privasi per RT).
            'rt_id' => $request->user()->isRt() ? $request->user()->rt_id : null,
            'amount' => $amount,
            'payment_method' => $method,
            'payment_code' => $this->generatePaymentCode(),
            'payment_status' => $status,
            'paid_at' => $paidAt,
            'proof_of_payment' => $proof,
        ]);

        ActivityLog::record($request->user(), 'iuran', 'mencatat pembayaran iuran ('.ucfirst($status).')', ($payerName ?? 'Warga').' Rp '.number_format($amount, 0, ',', '.'));

        return redirect()->route('contributions.index')
            ->with('success', 'Catatan pembayaran iuran warga berhasil disimpan.');
    }

    public function show(Contribution $contribution)
    {
        if (! request()->user()->isAdmin() && $contribution->user_id !== request()->user()->id) {
            abort(404);
        }

        $this->ensureRtAccess(request(), $contribution);

        return view('contributions.show', compact('contribution'));
    }

    public function payOnline(Request $request, Contribution $contribution)
    {
        abort_unless($request->user()->isWarga(), 403);

        if ($contribution->user_id !== $request->user()->id) {
            abort(404);
        }

        abort_if($contribution->payment_status !== 'pending', 403, 'Pembayaran ini sudah diselesaikan.');

        // Warga memilih metode pembayaran saat membayar; nominal sudah dikunci.
        $validated = $request->validate([
            'payment_method' => ['nullable', Rule::in(['virtual_account', 'qris', 'transfer'])],
        ]);

        $method = $validated['payment_method'] ?? $contribution->payment_method ?? 'qris';

        $contribution->update([
            'payment_method' => $method,
            'payment_status' => 'lunas',
            'paid_at' => now(),
            'proof_of_payment' => $contribution->proof_of_payment ?? 'Pembayaran online via '.$this->paymentMethodLabel($method),
        ]);

        ActivityLog::record($request->user(), 'iuran', 'membayar iuran online (Lunas)', $contribution->payment_code);

        return back()->with('success', 'Pembayaran iuran online berhasil. Status kini Lunas.');
    }

    public function edit(Contribution $contribution)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $this->ensureRtAccess(request(), $contribution);

        $warga = User::query()->where('role', 'warga')->orderBy('name')->get();

        return view('contributions.edit', compact('contribution', 'warga'));
    }

    public function update(Request $request, Contribution $contribution)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $this->ensureRtAccess($request, $contribution);

        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'payer_name' => ['nullable', 'string', 'max:100'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', Rule::in(['cash', 'virtual_account', 'qris', 'transfer'])],
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
            'proof_of_payment' => ['nullable', 'string', 'max:255'],
        ]);

        $status = $validated['payment_status'];
        $paidAt = $contribution->paid_at;

        if ($status === 'lunas' && ! $paidAt) {
            $paidAt = now();
        } elseif ($status !== 'lunas') {
            $paidAt = null;
        }

        $userId = $validated['user_id'] ?? $contribution->user_id;
        if ($request->has('user_id') && empty($validated['user_id'])) {
            $userId = null;
        }
        $payerName = $validated['payer_name'] ?? $contribution->payer_name;
        if (empty($payerName) && ! empty($userId)) {
            $payerName = User::whereKey($userId)->value('name') ?? $payerName;
        }

        $contribution->update([
            'user_id' => $userId,
            'payer_name' => $payerName,
            'amount' => $validated['amount'] ?? $contribution->amount ?? 50000,
            'payment_method' => $validated['payment_method'] ?? $contribution->payment_method ?? 'cash',
            'payment_status' => $status,
            'paid_at' => $paidAt,
            'proof_of_payment' => $validated['proof_of_payment'],
        ]);

        return redirect()->route('contributions.index')
            ->with('success', 'Catatan pembayaran iuran warga berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Contribution $contribution)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $this->ensureRtAccess($request, $contribution);

        $status = $request->validate([
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
        ])['payment_status'];

        $paidAt = $status === 'lunas' ? ($contribution->paid_at ?? now()) : null;

        $contribution->update([
            'payment_status' => $status,
            'paid_at' => $paidAt,
        ]);

        ActivityLog::record($request->user(), 'iuran', 'mengubah status menjadi '.ucfirst($status), $contribution->payment_code);

        if ($contribution->user) {
            $contribution->loadMissing('user');
            Notifier::send(
                $contribution->user,
                'Status pembayaran iuran diperbarui',
                'Pembayaran iuran '.$contribution->payment_code.' kini '.ucfirst($status).'.',
                route('contributions.show', $contribution),
                $request->user(),
            );
        }

        return back()->with('success', 'Status pembayaran iuran warga diubah menjadi '.ucfirst($status).'.');
    }

    public function destroy(Contribution $contribution)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $this->ensureRtAccess(request(), $contribution);

        $code = $contribution->payment_code;
        $contribution->delete();

        ActivityLog::record(request()->user(), 'iuran', 'menghapus catatan iuran', $code);

        return redirect()->route('contributions.index')
            ->with('success', 'Catatan iuran warga berhasil dihapus.');
    }

    private function generatePaymentCode(): string
    {
        return 'IUR-'.now()->format('ymd').'-'.strtoupper(Str::random(6));
    }

    private function paymentMethodLabel(?string $method): string
    {
        return match ($method) {
            'cash' => 'Tunai',
            'virtual_account' => 'Virtual Account',
            'qris' => 'QRIS',
            'transfer' => 'Transfer Bank',
            default => 'Tunai / Online',
        };
    }

    /**
     * Batasi query iuran untuk Ketua RT: hanya catatan milik RT-nya sendiri.
     * Catatan lama tanpa rt_id tetap terlihat jika akun tertautnya warga RT tersebut.
     */
    private function applyRtScope(Request $request, $query): void
    {
        $user = $request->user();

        if (! $user->isRt()) {
            return;
        }

        if (empty($user->rt_id)) {
            $query->whereRaw('0 = 1');

            return;
        }

        $rtId = $user->rt_id;

        $query->where(function ($q) use ($rtId) {
            $q->where('contributions.rt_id', $rtId)
                ->orWhere(function ($q2) use ($rtId) {
                    $q2->whereNull('contributions.rt_id')
                        ->whereHas('user', fn ($uq) => $uq->where('rt_id', $rtId));
                });
        });
    }

    /**
     * Pastikan Ketua RT tidak bisa membuka/mengubah iuran RT lain.
     */
    private function ensureRtAccess(Request $request, Contribution $contribution): void
    {
        $user = $request->user();

        if (! $user->isRt()) {
            return;
        }

        $contribution->loadMissing('user');

        $inScope = ! empty($user->rt_id)
            && (! empty($contribution->rt_id)
                ? (int) $contribution->rt_id === (int) $user->rt_id
                : ($contribution->user && (int) $contribution->user->rt_id === (int) $user->rt_id));

        abort_unless($inScope, 404);
    }
}
