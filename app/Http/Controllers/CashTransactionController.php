<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CashTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = CashTransaction::query()
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

        $cashTransactions = $query->latest('id')->paginate(10)->withQueryString();

        $base = $request->user()->isAdmin()
            ? CashTransaction::query()
            : CashTransaction::query()->where('user_id', $request->user()->id);

        $totalPending = (clone $base)->where('payment_status', 'pending')->count();
        $totalPaid = (clone $base)->where('payment_status', 'lunas')->count();

        return view('cash_transactions.index', compact(
            'cashTransactions',
            'totalPending',
            'totalPaid',
        ));
    }

    public function create()
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $warga = User::query()->where('role', 'warga')->orderBy('name')->get();

        return view('cash_transactions.create', compact('warga'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
            'proof_of_payment' => ['nullable', 'string', 'max:255'],
        ]);

        CashTransaction::create($validated);

        return redirect()->route('cash_transactions.index')
            ->with('success', 'Status pembayaran kas warga berhasil dicatat.');
    }

    public function show(CashTransaction $cashTransaction)
    {
        if (! request()->user()->isAdmin() && $cashTransaction->user_id !== request()->user()->id) {
            abort(404);
        }

        return view('cash_transactions.show', compact('cashTransaction'));
    }

    public function edit(CashTransaction $cashTransaction)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $warga = User::query()->where('role', 'warga')->orderBy('name')->get();

        return view('cash_transactions.edit', compact('cashTransaction', 'warga'));
    }

    public function update(Request $request, CashTransaction $cashTransaction)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
            'proof_of_payment' => ['nullable', 'string', 'max:255'],
        ]);

        $cashTransaction->update($validated);

        return redirect()->route('cash_transactions.index')
            ->with('success', 'Status pembayaran kas warga berhasil diperbarui.');
    }

    public function updateStatus(Request $request, CashTransaction $cashTransaction)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $status = $request->validate([
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
        ])['payment_status'];

        $cashTransaction->update(['payment_status' => $status]);

        return back()->with('success', 'Status pembayaran kas warga diubah menjadi '.ucfirst($status).'.');
    }

    public function destroy(CashTransaction $cashTransaction)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $cashTransaction->delete();

        return redirect()->route('cash_transactions.index')
            ->with('success', 'Catatan kas warga berhasil dihapus.');
    }
}
