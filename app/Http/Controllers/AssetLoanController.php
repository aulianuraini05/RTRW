<?php

namespace App\Http\Controllers;

use App\Models\AssetLoan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetLoanController extends Controller
{
    public function store(Request $request, \App\Models\Asset $asset)
    {
        abort_unless($request->user()->isWarga(), 403);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'borrow_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after_or_equal:borrow_date'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['quantity'] > $asset->availableQuantity()) {
            return back()->withErrors(['quantity' => 'Jumlah melebihi stok yang tersedia ('.$asset->availableQuantity().').'])->withInput();
        }

        $request->user()->assetLoans()->create([
            'asset_id' => $asset->id,
            'quantity' => $validated['quantity'],
            'borrow_date' => $validated['borrow_date'],
            'return_date' => $validated['return_date'],
            'notes' => $validated['notes'] ?? null,
            'loan_status' => 'diajukan',
        ]);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Permohonan peminjaman berhasil dikirim dan menunggu persetujuan RT/RW.');
    }

    public function updateStatus(Request $request, AssetLoan $loan)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $status = $request->validate([
            'loan_status' => [
                'required',
                Rule::in(['diajukan', 'diproses', 'disetujui', 'ditolak', 'dikembalikan']),
            ],
        ])['loan_status'];

        $loan->update([
            'loan_status' => $status,
            'actual_return_date' => $status === 'dikembalikan' ? today() : $loan->actual_return_date,
        ]);

        return back()->with('success', 'Status peminjaman berhasil diubah menjadi '.ucfirst($status).'.');
    }
}
