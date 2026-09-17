<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContributionController extends Controller
{
    public function index(Request $request)
    {
        $query = Contribution::query()
            ->with('user')
            ->when(
                ! $request->user()->isAdmin(),
                fn ($q) => $q->where('user_id', $request->user()->id),
            );

        if ($request->filled('status') && in_array($request->status, ['pending', 'lunas', 'ditolak'])) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('search') && $request->user()->isAdmin()) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }

        $contributions = $query->latest('id')->paginate(10)->withQueryString();

        $base = $request->user()->isAdmin()
            ? Contribution::query()
            : Contribution::query()->where('user_id', $request->user()->id);

        $totalPending = (clone $base)->where('payment_status', 'pending')->count();
        $totalPaid = (clone $base)->where('payment_status', 'lunas')->count();

        return view('contributions.index', compact(
            'contributions',
            'totalPending',
            'totalPaid',
        ));
    }

    public function create()
    {
        if (request()->user()->isWarga()) {
            return view('contributions.create');
        }

        abort_unless(request()->user()->isAdmin(), 403);

        $warga = User::query()->where('role', 'warga')->orderBy('name')->get();

        return view('contributions.create', compact('warga'));
    }

    public function store(Request $request)
    {
        if ($request->user()->isWarga()) {
            $validated = $request->validate([
                'amount' => ['required', 'numeric', 'min:0'],
                'payment_method' => ['required', Rule::in(['virtual_account', 'qris', 'transfer'])],
                'proof_of_payment' => ['nullable', 'string', 'max:255'],
            ]);

            $request->user()->contributions()->create([
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_code' => $this->generatePaymentCode(),
                'payment_status' => 'pending',
                'proof_of_payment' => $validated['proof_of_payment'] ?? null,
            ]);

            return redirect()->route('contributions.index')
                ->with('success', 'Pembayaran iuran Anda berhasil diajukan. Silakan selesaikan pembayaran online untuk melunasi.');
        }

        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', Rule::in(['cash', 'virtual_account', 'qris', 'transfer'])],
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
            'proof_of_payment' => ['nullable', 'string', 'max:255'],
        ]);

        $status = $validated['payment_status'];
        $paidAt = $status === 'lunas' ? now() : null;
        $proof = $validated['proof_of_payment'];
        $method = $validated['payment_method'] ?? 'cash';
        $amount = $validated['amount'] ?? 50000;

        if ($status === 'lunas' && empty($proof)) {
            if ($method === 'cash') {
                $proof = 'Diterima tunai secara langsung oleh Pengurus RT';
            }
        }

        Contribution::create([
            'user_id' => $validated['user_id'],
            'amount' => $amount,
            'payment_method' => $method,
            'payment_code' => $this->generatePaymentCode(),
            'payment_status' => $status,
            'paid_at' => $paidAt,
            'proof_of_payment' => $proof,
        ]);

        return redirect()->route('contributions.index')
            ->with('success', 'Catatan pembayaran iuran warga berhasil disimpan.');
    }

    public function show(Contribution $contribution)
    {
        if (! request()->user()->isAdmin() && $contribution->user_id !== request()->user()->id) {
            abort(404);
        }

        return view('contributions.show', compact('contribution'));
    }

    public function payOnline(Request $request, Contribution $contribution)
    {
        abort_unless($request->user()->isWarga(), 403);

        if ($contribution->user_id !== $request->user()->id) {
            abort(404);
        }

        abort_if($contribution->payment_status !== 'pending', 403, 'Pembayaran ini sudah diselesaikan.');

        $contribution->update([
            'payment_status' => 'lunas',
            'paid_at' => now(),
            'proof_of_payment' => $contribution->proof_of_payment ?? 'Pembayaran online via '.$this->paymentMethodLabel($contribution->payment_method),
        ]);

        return back()->with('success', 'Pembayaran iuran online berhasil. Status kini Lunas.');
    }

    public function edit(Contribution $contribution)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $warga = User::query()->where('role', 'warga')->orderBy('name')->get();

        return view('contributions.edit', compact('contribution', 'warga'));
    }

    public function update(Request $request, Contribution $contribution)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
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

        $contribution->update([
            'user_id' => $validated['user_id'],
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

        $status = $request->validate([
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
        ])['payment_status'];

        $paidAt = $status === 'lunas' ? ($contribution->paid_at ?? now()) : null;

        $contribution->update([
            'payment_status' => $status,
            'paid_at' => $paidAt,
        ]);

        return back()->with('success', 'Status pembayaran iuran warga diubah menjadi '.ucfirst($status).'.');
    }

    public function destroy(Contribution $contribution)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $contribution->delete();

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
            'cash' => 'Tunai / Cash',
            'virtual_account' => 'Virtual Account',
            'qris' => 'QRIS',
            'transfer' => 'Transfer Bank',
            default => 'Tunai / Online',
        };
    }
}
