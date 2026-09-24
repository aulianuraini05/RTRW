<?php

namespace App\Http\Controllers;

use App\Models\Rt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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

        $rts = \App\Models\Rt::orderByRaw("CAST(substr(name, 4) AS INTEGER)")->get();

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

    public function create(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $rts = $this->getAvailableRts($request);

        return view('warga.create', compact('rts'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $rts = $this->getAvailableRts($request);
        $rtIds = $rts->pluck('id')->toArray();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'no_whatsapp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'nik' => ['required', 'digits:16', 'unique:'.User::class],
            'no_kk' => ['required', 'digits:16'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'rt_id' => ['required', Rule::in($rtIds)],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'status_perkawinan' => ['nullable', Rule::in(['belum_kawin', 'kawin', 'cerai_hidup', 'cerai_mati'])],
            'agama' => ['nullable', 'string', 'max:50'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
            'alamat_rumah' => ['nullable', 'string', 'max:500'],
            'no_rumah' => ['nullable', 'string', 'max:20'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'warga';

        $user = User::create($validated);

        return redirect()->route('warga.index')->with('success', 'Warga '.$user->name.' berhasil ditambahkan di '.$user->rt->name.'.');
    }

    public function edit(Request $request, User $user)
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_if($user->role !== 'warga', 404);

        $this->ensureRtAccess($request, $user);

        $rts = $this->getAvailableRts($request);

        return view('warga.edit', compact('user', 'rts'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_if($user->role !== 'warga', 404);

        $this->ensureRtAccess($request, $user);

        $rts = $this->getAvailableRts($request);
        $rtIds = $rts->pluck('id')->toArray();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'no_whatsapp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'nik' => ['required', 'digits:16', Rule::unique(User::class)->ignore($user->id)],
            'no_kk' => ['required', 'digits:16'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'rt_id' => ['required', Rule::in($rtIds)],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'status_perkawinan' => ['nullable', Rule::in(['belum_kawin', 'kawin', 'cerai_hidup', 'cerai_mati'])],
            'agama' => ['nullable', 'string', 'max:50'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
            'alamat_rumah' => ['nullable', 'string', 'max:500'],
            'no_rumah' => ['nullable', 'string', 'max:20'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Ketua RT tidak boleh memindahkan warga ke RT lain di luar scope-nya (sudah dibatasi Rule::in)
        // Tapi pastikan RT yang isRt() tetap hanya bisa simpan di RT sendiri
        if ($request->user()->isRt() && (int) $validated['rt_id'] !== (int) $request->user()->rt_id) {
            abort(403, 'Ketua RT hanya bisa mengelola warga RT sendiri.');
        }

        $user->update($validated);

        return redirect()->route('warga.show', $user)->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_if($user->role !== 'warga', 404);

        $this->ensureRtAccess($request, $user);

        $name = $user->name;
        $user->delete();

        return redirect()->route('warga.index')->with('success', 'Warga '.$name.' berhasil dihapus.');
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

    private function getAvailableRts(Request $request)
    {
        $user = $request->user();

        if ($user->isRt() && ! empty($user->rt_id)) {
            return Rt::where('id', $user->rt_id)->get();
        }

        return Rt::orderByRaw("CAST(substr(name, 4) AS INTEGER)")->get();
    }
}
