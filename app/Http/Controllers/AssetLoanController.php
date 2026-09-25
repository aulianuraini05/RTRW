<?php

namespace App\Http\Controllers;

use App\Models\AssetLoan;
use App\Services\ActivityLog;
use App\Services\Notifier;
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
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        if ($validated['quantity'] > $asset->availableQuantity()) {
            return back()->withErrors(['quantity' => 'Jumlah melebihi stok yang tersedia ('.$asset->availableQuantity().').'])->withInput();
        }

        $loan = $request->user()->assetLoans()->create([
            'asset_id' => $asset->id,
            'quantity' => $validated['quantity'],
            'borrow_date' => $validated['borrow_date'],
            'return_date' => $validated['return_date'],
            'notes' => $validated['notes'] ?? null,
            'loan_status' => 'diajukan',
        ]);

        // Notifikasi ke pengurus pemilik aset
        $asset->loadMissing('rt');
        $managers = Notifier::managersForRt($asset->rt_id);
        // Jika aset umum (rt_id null), kirim ke semua RW/Admin
        if (empty($asset->rt_id)) {
            $managers = \App\Models\User::whereIn('role', ['rw', 'admin', 'superadmin'])->get();
        }
        Notifier::sendMany(
            $managers,
            'Pengajuan peminjaman aset',
            $request->user()->name.' mengajukan peminjaman '.$asset->asset_name.' ('.$loan->quantity.' unit)',
            route('assets.show', $asset),
            $request->user(),
        );

        ActivityLog::record($request->user(), 'aset', 'mengajukan peminjaman', $asset->asset_name.' ('.$loan->quantity.' unit)');

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Permohonan peminjaman berhasil dikirim dan menunggu persetujuan RT/RW.');
    }

    public function updateStatus(Request $request, AssetLoan $loan)
    {
        abort_unless($request->user()->isAdmin(), 403);

        // Konfirmasi hanya oleh Ketua RT pemilik aset (atau admin/RW untuk aset umum).
        $loan->loadMissing('asset');
        abort_unless($loan->asset && $loan->asset->userCanConfirmLoan($request->user()), 403);

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

        $loan->loadMissing(['user', 'asset']);
        ActivityLog::record($request->user(), 'aset', 'mengubah status peminjaman menjadi '.ucfirst($status), ($loan->asset?->asset_name ?? 'Aset').' ('.$loan->quantity.' unit)');
        if ($loan->user) {
            Notifier::send(
                $loan->user,
                'Status peminjaman diperbarui',
                'Peminjaman '.$loan->asset->asset_name.' kini '.ucfirst($status).'.',
                route('assets.show', $loan->asset),
                $request->user(),
            );
        }

        return back()->with('success', 'Status peminjaman berhasil diubah menjadi '.ucfirst($status).'.');
    }
}
