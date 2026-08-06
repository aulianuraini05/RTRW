<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::query()
            ->withCount(['loans' => function ($query) {
                $query->whereIn('loan_status', ['disetujui', 'dipinjam']);
            }])
            ->latest()
            ->paginate(10);

        $user = request()->user();
        if ($user->isAdmin()) {
            $loans = \App\Models\AssetLoan::with(['asset', 'user'])
                ->latest()
                ->take(15)
                ->get();
        } else {
            $loans = \App\Models\AssetLoan::with('asset')
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get();
        }

        return view('assets.index', compact('assets', 'loans'));
    }

    public function create()
    {
        abort_unless(request()->user()->isAdmin(), 403);

        return view('assets.create');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'asset_name' => ['required', 'string', 'max:255'],
            'asset_type' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'in:baik,rusak ringan,rusak berat,perlu perbaikan'],
            'description' => ['nullable', 'string'],
        ]);

        Asset::create($validated);

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['loans.user' => function ($query) {
            $query->latest();
        }]);

        if (request()->user()->isWarga()) {
            $asset->load(['loans' => function ($query) {
                $query->where('user_id', request()->user()->id)->latest();
            }]);
        }

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'asset_name' => ['required', 'string', 'max:255'],
            'asset_type' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'in:baik,rusak ringan,rusak berat,perlu perbaikan'],
            'description' => ['nullable', 'string'],
        ]);

        $asset->update($validated);

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil dihapus.');
    }
}
