<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\User;
use Illuminate\Http\Request;
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
                'proof_of_payment' => ['nullable', 'string', 'max:255'],
            ]);

            $request->user()->contributions()->create([
                'payment_status' => 'pending',
                'proof_of_payment' => $validated['proof_of_payment'] ?? null,
            ]);

            return redirect()->route('contributions.index')
                ->with('success', 'Pembayaran iuran Anda berhasil diajukan dan menunggu verifikasi RT/RW.');
        }

        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
            'proof_of_payment' => ['nullable', 'string', 'max:255'],
        ]);

        Contribution::create($validated);

        return redirect()->route('contributions.index')
            ->with('success', 'Status pembayaran iuran warga berhasil dicatat.');
    }

    public function show(Contribution $contribution)
    {
        if (! request()->user()->isAdmin() && $contribution->user_id !== request()->user()->id) {
            abort(404);
        }

        return view('contributions.show', compact('contribution'));
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
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
            'proof_of_payment' => ['nullable', 'string', 'max:255'],
        ]);

        $contribution->update($validated);

        return redirect()->route('contributions.index')
            ->with('success', 'Status pembayaran iuran warga berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Contribution $contribution)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $status = $request->validate([
            'payment_status' => ['required', Rule::in(['pending', 'lunas', 'ditolak'])],
        ])['payment_status'];

        $contribution->update(['payment_status' => $status]);

        return back()->with('success', 'Status pembayaran iuran warga diubah menjadi '.ucfirst($status).'.');
    }

    public function destroy(Contribution $contribution)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        $contribution->delete();

        return redirect()->route('contributions.index')
            ->with('success', 'Catatan iuran warga berhasil dihapus.');
    }
}
