<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\KasSchedule;
use App\Models\User;
use App\Services\ActivityLog;
use App\Services\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CashTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = CashTransaction::query()
            ->with(['user', 'schedule'])
            ->when(
                ! $request->user()->isAdmin(),
                fn ($q) => $q->where('user_id', $request->user()->id),
            );

        // Privasi per RT: Ketua RT hanya melihat kas RT-nya sendiri.
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

        $cashTransactions = $query->latest('id')->paginate(10)->withQueryString();

        $base = $request->user()->isAdmin()
            ? CashTransaction::query()
            : CashTransaction::query()->where('user_id', $request->user()->id);

        $this->applyRtScope($request, $base);

        $totalPending = (clone $base)->where('payment_status', 'pending')->count();
        $totalPaid = (clone $base)->where('payment_status', 'lunas')->count();

        // Jadwal kas aktif untuk RT warga + tagihan pending miliknya (banner info).
        $activeSchedule = null;
        $myPendingBill = null;
        $schedulePaid = false;
        if ($request->user()->isWarga()) {
            $activeSchedule = KasSchedule::with('rt')
                ->where('rt_id', $request->user()->rt_id)
                ->orderByDesc('year')->orderByDesc('month')
                ->first();
            $myPendingBill = (clone $base)->where('payment_status', 'pending')->latest('id')->first();
            $schedulePaid = $activeSchedule
                ? (clone $base)->where('kas_schedule_id', $activeSchedule->id)->where('payment_status', 'lunas')->exists()
                : false;
        }

        return view('cash_transactions.index', compact(
            'cashTransactions',
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
            $pending = $user->cashTransactions()->where('payment_status', 'pending')->latest('id')->first();
            if ($pending) {
                return redirect()->route('cash_transactions.show', $pending)
                    ->with('info', 'Anda masih memiliki tagihan kas yang belum dibayar. Silakan selesaikan di bawah ini.');
            }

            // Nominal dikunci dari jadwal kas RT. Belum ada jadwal = belum ada tagihan.
            $schedule = KasSchedule::where('rt_id', $user->rt_id)
                ->orderByDesc('year')->orderByDesc('month')
                ->first();

            if (! $schedule) {
                return redirect()->route('cash_transactions.index')
                    ->with('info', 'Belum ada kas bulan ini dari Ketua RT. Silakan menunggu pengumuman.');
            }

            // Kas periode ini sudah lunas — tidak perlu bayar lagi.
            $alreadyPaid = $user->cashTransactions()
                ->where('kas_schedule_id', $schedule->id)
                ->where('payment_status', 'lunas')
                ->exists();
            if ($alreadyPaid) {
                return redirect()->route('cash_transactions.index')
                    ->with('info', 'Kas '.$schedule->period_label.' Anda sudah lunas.');
            }

            return view('cash_transactions.create', compact('schedule'));
        }

        abort_unless(request()->user()->isAdmin(), 403);

        $warga = User::query()->where('role', 'warga')->orderBy('name')->get();

        return view('cash_transactions.create', compact('warga'));
    }

    public function store(Request $request)
    {
        if ($request->user()->isWarga()) {
            $user = $request->user();

            // Nominal kas ditentukan Ketua RT — abaikan nominal dari input.
            $schedule = KasSchedule::where('rt_id', $user->rt_id)
                ->orderByDesc('year')->orderByDesc('month')
                ->first();

            $validated = $request->validate([
                'amount' => [$schedule ? 'nullable' : 'required', 'numeric', 'min:0'],
                'payment_method' => ['required', Rule::in(['virtual_account', 'qris', 'transfer'])],
                'proof_of_payment' => ['nullable', 'string', 'max:255'],
            ]);

            if ($schedule) {
                // Jangan gandakan tagihan untuk jadwal yang sama.
                $existing = $user->cashTransactions()
                    ->where('kas_schedule_id', $schedule->id)
                    ->where('payment_status', 'pending')
                    ->first();
                if ($existing) {
                    return redirect()->route('cash_transactions.show', $existing)
                        ->with('info', 'Tagihan kas '.$schedule->period_label.' Anda sudah ada. Silakan selesaikan di bawah ini.');
                }

                $alreadyPaid = $user->cashTransactions()
                    ->where('kas_schedule_id', $schedule->id)
                    ->where('payment_status', 'lunas')
                    ->exists();
                if ($alreadyPaid) {
                    return redirect()->route('cash_transactions.index')
                        ->with('info', 'Kas '.$schedule->period_label.' Anda sudah lunas.');
                }
            }

            $txn = $user->cashTransactions()->create([
                'kas_schedule_id' => $schedule?->id,
                'rt_id' => $user->rt_id,
                'payer_name' => $user->name,
                'amount' => $schedule ? $schedule->amount : $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_code' => $this->generatePaymentCode(),
                'payment_status' => 'pending',
                'proof_of_payment' => $validated['proof_of_payment'] ?? null,
            ]);

            // Notifikasi ke pengurus RT (ada tagihan baru masuk)
            $managers = Notifier::managersForRt($user->rt_id);
            Notifier::sendMany(
                $managers,
                'Pembayaran kas baru',
                $user->name.' mengajukan pembayaran kas '.number_format($txn->amount, 0, ',', '.'),
                route('cash_transactions.show', $txn),
                $user,
            );

            ActivityLog::record($user, 'kas', 'mengajukan pembayaran kas', $txn->payment_code.' (Rp '.number_format($txn->amount, 0, ',', '.').')');

            return redirect()->route('cash_transactions.index')
                ->with('success', 'Pembayaran kas Anda berhasil diajukan. Silakan selesaikan pembayaran online untuk melunasi.');
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
        // Jika isi manual (misal nenek-nenek bayar cash tanpa HP/akun), pakai nama manual.
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

        CashTransaction::create([
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

        ActivityLog::record($request->user(), 'kas', 'mencatat pembayaran kas ('.ucfirst($status).')', ($payerName ?? 'Warga').' Rp '.number_format($amount, 0, ',', '.'));

        return redirect()->route('cash_transactions.index')
            ->with('success', 'Catatan pembayaran kas warga berhasil disimpan.');
    }

    public function show(CashTransaction $cashTransaction)
    {
        if (! request()->user()->isAdmin() && $cashTransaction->user_id !== request()->user()->id) {
            abort(404);
        }

        $this->ensureRtAccess(request(), $cashTransaction);

        return view('cash_transactions.show', compact('cashTransaction'));
    }

    public function payOnline(Request $request, CashTransaction $cashTransaction)
    {
        abort_unless($request->user()->isWarga(), 403);

        if ($cashTransaction->user_id !== $request->user()->id) {
            abort(404);
        }

        abort_if($cashTransaction->payment_status !== 'pending', 403, 'Pembayaran ini sudah diselesaikan.');

        // Warga memilih metode pembayaran saat membayar; nominal sudah dikunci.
        $validated = $request->validate([
            'payment_method' => ['nullable', Rule::in(['virtual_account', 'qris', 'transfer'])],
        ]);

        $method = $validated['payment_method'] ?? $cashTransaction->payment_method ?? 'qris';

        $cashTransaction->update([
            'payment_method' => $method,
            'payment_status' => 'lunas',
            'paid_at' => now(),
            'proof_of_payment' => $cashTransaction->proof_of_payment ?? 'Pembayaran online via '.$this->paymentMethodLabel($method),
        ]);

        ActivityLog::record($request->user(), 'kas', 'membayar kas online (Lunas)', $cashTransaction->payment_code);

        return back()->with('success', 'Pembayaran kas online berhasil. Status kini Lunas.');
    }

    public function edit(CashTransaction $cashTransaction)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $this->ensureRtAccess(request(), $cashTransaction);

        $warga = User::query()->where('role', 'warga')->orderBy('name')->get();

        return view('cash_transactions.edit', compact('cashTransaction', 'warga'));
    }

    public function update(Request $request, CashTransaction $cashTransaction)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $this->ensureRtAccess($request, $cashTransaction);

        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'payer_name' => ['nullable', 'string', 'max:100'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', Rule::in(['cash', 'virtual_account', 'qris', 'transfer'])],
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
            'proof_of_payment' => ['nullable', 'string', 'max:255'],
        ]);

        $status = $validated['payment_status'];
        $paidAt = $cashTransaction->paid_at;

        if ($status === 'lunas' && ! $paidAt) {
            $paidAt = now();
        } elseif ($status !== 'lunas') {
            $paidAt = null;
        }

        $userId = $validated['user_id'] ?? $cashTransaction->user_id;
        // Kosongkan tautan akun jika admin menghapus pilihan warga (untuk pembayar manual).
        // Form mengirim hidden "user_id_clear" saat dropdown dikosongkan? Fallback: hormati input kosong.
        if ($request->has('user_id') && empty($validated['user_id'])) {
            $userId = null;
        }
        $payerName = $validated['payer_name'] ?? $cashTransaction->payer_name;
        if (empty($payerName) && ! empty($userId)) {
            $payerName = User::whereKey($userId)->value('name') ?? $payerName;
        }

        $cashTransaction->update([
            'user_id' => $userId,
            'payer_name' => $payerName,
            'amount' => $validated['amount'] ?? $cashTransaction->amount ?? 50000,
            'payment_method' => $validated['payment_method'] ?? $cashTransaction->payment_method ?? 'cash',
            'payment_status' => $status,
            'paid_at' => $paidAt,
            'proof_of_payment' => $validated['proof_of_payment'],
        ]);

        return redirect()->route('cash_transactions.index')
            ->with('success', 'Status pembayaran kas warga berhasil diperbarui.');
    }

    public function updateStatus(Request $request, CashTransaction $cashTransaction)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $this->ensureRtAccess($request, $cashTransaction);

        $status = $request->validate([
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
        ])['payment_status'];

        $paidAt = $status === 'lunas' ? ($cashTransaction->paid_at ?? now()) : null;

        $cashTransaction->update([
            'payment_status' => $status,
            'paid_at' => $paidAt,
        ]);

        ActivityLog::record($request->user(), 'kas', 'mengubah status menjadi '.ucfirst($status), $cashTransaction->payment_code);

        if ($cashTransaction->user) {
            $cashTransaction->loadMissing('user');
            Notifier::send(
                $cashTransaction->user,
                'Status pembayaran kas diperbarui',
                'Pembayaran kas '.$cashTransaction->payment_code.' kini '.ucfirst($status).'.',
                route('cash_transactions.show', $cashTransaction),
                $request->user(),
            );
        }

        return back()->with('success', 'Status pembayaran kas warga diubah menjadi '.ucfirst($status).'.');
    }

    public function destroy(CashTransaction $cashTransaction)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $this->ensureRtAccess(request(), $cashTransaction);

        $code = $cashTransaction->payment_code;
        $cashTransaction->delete();

        ActivityLog::record(request()->user(), 'kas', 'menghapus catatan kas', $code);

        return redirect()->route('cash_transactions.index')
            ->with('success', 'Catatan kas warga berhasil dihapus.');
    }

    private function generatePaymentCode(): string
    {
        return 'KAS-'.now()->format('ymd').'-'.strtoupper(Str::random(6));
    }

    /**
     * Batasi query kas untuk Ketua RT: hanya catatan milik RT-nya sendiri.
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
            $q->where('cash_transactions.rt_id', $rtId)
                ->orWhere(function ($q2) use ($rtId) {
                    $q2->whereNull('cash_transactions.rt_id')
                        ->whereHas('user', fn ($uq) => $uq->where('rt_id', $rtId));
                });
        });
    }

    /**
     * Pastikan Ketua RT tidak bisa membuka/mengubah kas RT lain.
     */
    private function ensureRtAccess(Request $request, CashTransaction $cashTransaction): void
    {
        $user = $request->user();

        if (! $user->isRt()) {
            return;
        }

        $cashTransaction->loadMissing('user');

        $inScope = ! empty($user->rt_id)
            && (! empty($cashTransaction->rt_id)
                ? (int) $cashTransaction->rt_id === (int) $user->rt_id
                : ($cashTransaction->user && (int) $cashTransaction->user->rt_id === (int) $user->rt_id));

        abort_unless($inScope, 404);
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
}
