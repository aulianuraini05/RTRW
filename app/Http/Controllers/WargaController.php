<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $query = User::query()
            ->with('rt')
            ->where('role', 'warga');

        $this->applyRtScope($request, $query);

        if ($request->filled('search')) {
            $search = '%'.$request->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        if ($request->filled('rt_id') && ($request->user()->isRw() || $request->user()->isSuperAdmin() || $request->user()->role === 'admin')) {
            // RW / Superadmin / Admin can filter by RT
            if ($request->rt_id !== 'all') {
                $query->where('rt_id', $request->rt_id);
            }
        }

        $warga = $query->orderBy('name')->paginate(12)->withQueryString();

        // Statistik untuk header
        $base = User::query()->where('role', 'warga');
        $this->applyRtScope($request, $base);
        $totalWarga = (clone $base)->count();
        $totalRt = \App\Models\Rt::count();

        $rts = \App\Models\Rt::orderBy('name')->get();

        return view('warga.index', compact('warga', 'totalWarga', 'totalRt', 'rts'));
    }

    public function show(Request $request, User $user)
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_if($user->role !== 'warga', 404);

        $this->ensureRtAccess($request, $user);

        $user->load(['rt', 'cashTransactions', 'contributions', 'letters', 'aspirations']);

        return view('warga.show', compact('user'));
    }

    /**
     * Batasi query warga untuk Ketua RT: hanya warga RT-nya sendiri.
     * Ketua RW / Superadmin / Admin melihat semua RT.
     */
    private function applyRtScope(Request $request, $query): void
    {
        $user = $request->user();

        if ($user->isSuperAdmin() || $user->role === 'admin' || $user->isRw()) {
            return;
        }

        if (! $user->isRt()) {
            return;
        }

        if (empty($user->rt_id)) {
            $query->whereRaw('0 = 1');

            return;
        }

        $query->where('rt_id', $user->rt_id);
    }

    /**
     * Pastikan Ketua RT tidak bisa membuka warga RT lain.
     */
    private function ensureRtAccess(Request $request, User $warga): void
    {
        $user = $request->user();

        if ($user->isSuperAdmin() || $user->role === 'admin' || $user->isRw()) {
            return;
        }

        if (! $user->isRt()) {
            return;
        }

        abort_unless(! empty($user->rt_id) && (int) $warga->rt_id === (int) $user->rt_id, 404);
    }
}
