<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Services\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $assets = Asset::query()
            ->with('rt')
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                $rtId = $user->rt_id;

                return $query->where(function ($q) use ($rtId) {
                    $q->whereNull('rt_id');
                    if ($rtId) {
                        $q->orWhere('rt_id', $rtId);
                    }
                });
            })
            ->when($user->isRt(), function ($query) use ($user) {
                // Ketua RT hanya melihat aset RT-nya sendiri + aset umum.
                if (empty($user->rt_id)) {
                    return $query->whereRaw('0 = 1');
                }

                return $query->where(function ($q) use ($user) {
                    $q->whereNull('rt_id')->orWhere('rt_id', $user->rt_id);
                });
            })
            ->withCount(['loans' => function ($query) {
                $query->whereIn('loan_status', ['disetujui', 'dipinjam']);
            }])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.trim($request->string('search')).'%';

                return $query->where(function ($sub) use ($search) {
                    $sub->where('asset_name', 'like', $search)
                        ->orWhere('asset_type', 'like', $search)
                        ->orWhere('description', 'like', $search);
                });
            })
            ->when($request->filled('condition'), function ($query) use ($request) {
                $conditions = ['baik', 'rusak ringan', 'perlu perbaikan', 'rusak berat'];
                $condition = $request->string('condition')->toString();

                return in_array($condition, $conditions, true)
                    ? $query->where('condition', $condition)
                    : $query;
            })
            ->when($request->string('sort')->toString() === 'nama', fn ($query) => $query->orderBy('asset_name'), fn ($query) => $query->latest())
            ->paginate(10)
            ->withQueryString();

        if ($user->isAdmin()) {
            $loansQuery = \App\Models\AssetLoan::with(['asset', 'user'])->latest();

            // Ketua RT hanya melihat pengajuan untuk aset RT-nya sendiri.
            if ($user->isRt()) {
                if (empty($user->rt_id)) {
                    $loansQuery->whereRaw('0 = 1');
                } else {
                    $loansQuery->whereHas('asset', fn ($q) => $q->where('rt_id', $user->rt_id));
                }
            }

            $loans = $loansQuery->take(15)->get();
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
            // Ketua RT wajib mengunggah foto aset; peran lain tetap opsional.
            'image' => $request->user()->isRt()
                ? ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240']
                : ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'rt_id' => ['nullable'],
        ], [
            'image.required' => 'Wajib diisi.',
            'image.max' => 'Ukuran foto maksimal 10 MB.',
            'image.image' => 'File yang diunggah harus berupa foto.',
            'image.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
        ]);

        $user = $request->user();
        $rawRt = $request->input('rt_id');

        if ($rawRt === 'rw' || empty($rawRt)) {
            $validated['rt_id'] = null;
        } elseif (is_numeric($rawRt)) {
            $validated['rt_id'] = (int) $rawRt;
        } elseif ($user->rt_id) {
            $validated['rt_id'] = $user->rt_id;
        } else {
            $validated['rt_id'] = null;
        }

        $validated['image'] = $this->storeImage($request);

        $asset = Asset::create($validated);

        ActivityLog::record($request->user(), 'aset', 'menambah aset', $asset->asset_name.' ('.$asset->quantity.' unit)');

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $user = request()->user();

        if ($user->isWarga() && $asset->rt_id !== null && $asset->rt_id !== $user->rt_id) {
            abort(404);
        }

        $asset->load(['rt', 'loans.user' => function ($query) {
            $query->latest();
        }]);

        if ($user->isWarga()) {
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
            // Ketua RT wajib punya foto: saat edit, wajib isi jika aset belum punya foto.
            'image' => ($request->user()->isRt() && empty($asset->image))
                ? ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240']
                : ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'rt_id' => ['nullable'],
        ], [
            'image.required' => 'Wajib diisi.',
            'image.max' => 'Ukuran foto maksimal 10 MB.',
            'image.image' => 'File yang diunggah harus berupa foto.',
            'image.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
        ]);

        $user = $request->user();
        $rawRt = $request->input('rt_id');

        if ($rawRt === 'rw' || empty($rawRt)) {
            $validated['rt_id'] = null;
        } elseif (is_numeric($rawRt)) {
            $validated['rt_id'] = (int) $rawRt;
        } elseif ($user->rt_id) {
            $validated['rt_id'] = $user->rt_id;
        } else {
            $validated['rt_id'] = null;
        }

        if ($request->hasFile('image')) {
            if ($asset->image) {
                Storage::disk('public')->delete($asset->image);
            }
            $validated['image'] = $this->storeImage($request);
        } else {
            unset($validated['image']);
        }

        $asset->update($validated);

        ActivityLog::record($request->user(), 'aset', 'memperbarui aset', $asset->asset_name);

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        abort_unless(request()->user()->isAdmin(), 403);

        if ($asset->image) {
            Storage::disk('public')->delete($asset->image);
        }

        $name = $asset->asset_name;
        $asset->delete();

        ActivityLog::record(request()->user(), 'aset', 'menghapus aset', $name);

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil dihapus.');
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');

        if (! $file->isValid() || ! is_file($file->getPathname())) {
            return null;
        }

        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $name = Str::random(40).'.'.$extension;

        Storage::disk('public')->put(
            "assets/{$name}",
            file_get_contents($file->getPathname())
        );

        return "assets/{$name}";
    }
}
