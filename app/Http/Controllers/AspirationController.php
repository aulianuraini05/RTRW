<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Services\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AspirationController extends Controller
{
    public function index(Request $request)
    {
        $aspirations = Aspiration::query()
            ->with('user')
            ->when(
                ! request()->user()->isAdmin(),
                fn ($query) => $query->where('user_id', request()->user()->id),
            );

        // Privasi per RT: Ketua RT hanya melihat aspirasi warga RT-nya sendiri.
        $this->applyRtScope(request(), $aspirations);

        // Cari: judul / isi aspirasi.
        $aspirations->when($request->filled('search'), function ($query) use ($request) {
            $search = '%'.trim($request->string('search')).'%';

            return $query->where(function ($sub) use ($search) {
                $sub->where('aspiration_title', 'like', $search)
                    ->orWhere('aspiration_content', 'like', $search);
            });
        });

        // Filter status.
        $aspirations->when($request->filled('status'), function ($query) use ($request) {
            $statuses = ['dikirim', 'diterima', 'diproses', 'selesai', 'ditolak', 'diteruskan'];
            $status = $request->string('status')->toString();

            return in_array($status, $statuses, true)
                ? $query->where('aspiration_status', $status)
                : $query;
        });

        // Urutkan: terbaru (default) / terlama.
        $sort = $request->string('sort')->toString();
        $aspirations = $sort === 'terlama'
            ? $aspirations->oldest('submission_date')->paginate(10)->withQueryString()
            : $aspirations->latest('submission_date')->paginate(10)->withQueryString();

        return view('aspirations.index', compact('aspirations'));
    }

    public function create()
    {
        abort_unless(request()->user()->isWarga(), 403);

        return view('aspirations.create');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isWarga(), 403);

        $validated = $request->validate([
            'aspiration_title' => ['required', 'string', 'max:255'],
            'aspiration_content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'submission_date' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'photo.max' => 'Ukuran foto maksimal 10 MB.',
            'photo.image' => 'File yang diunggah harus berupa foto.',
            'photo.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            try {
                $photoPath = $this->storePhoto($request);
            } catch (\Throwable $e) {
                report($e);

                return back()->withInput()->withErrors([
                    'photo' => 'Gagal mengunggah foto. Coba lagi tanpa foto atau gunakan file JPG/PNG/WebP maksimal 10 MB.',
                ]);
            }
        }

        $aspiration = $request->user()->aspirations()->create([
            'aspiration_title' => $validated['aspiration_title'],
            'aspiration_content' => $validated['aspiration_content'],
            'category' => $validated['category'],
            'submission_date' => $validated['submission_date'],
            'photo_path' => $photoPath,
            'aspiration_status' => 'dikirim',
            // Catat RT saat pengajuan agar tetap terlihat oleh RT walau akun warga dihapus.
            'rt_id' => $request->user()->rt_id,
        ]);

        // Notifikasi ke pengurus RT setempat
        $managers = Notifier::managersForRt($request->user()->rt_id);
        Notifier::sendMany(
            $managers,
            'Aspirasi baru masuk',
            $request->user()->name.' mengajukan aspirasi: '.$aspiration->aspiration_title,
            route('aspirations.show', $aspiration),
            $request->user(),
        );

        return redirect()->route('aspirations.index')
            ->with('success', 'Aspirasi berhasil dikirim dan menunggu tindak lanjut RT/RW.');
    }

    public function show(Aspiration $aspiration)
    {
        if (! request()->user()->isAdmin() && $aspiration->user_id !== request()->user()->id) {
            abort(404);
        }

        $this->ensureRtAccess(request(), $aspiration);

        return view('aspirations.show', compact('aspiration'));
    }

    public function edit(Aspiration $aspiration)
    {
        $this->ensureRtAccess(request(), $aspiration);

        return view('aspirations.edit', compact('aspiration'));
    }

    public function update(Request $request, Aspiration $aspiration)
    {
        $this->ensureRtAccess($request, $aspiration);

        $request->validate([
            'aspiration_title' => 'required',
            'aspiration_content' => 'required',
            'category' => 'required',
            'submission_date' => 'required|date',
            'aspiration_status' => 'required',
        ]);

        $aspiration->update($request->all());

        return redirect()->route('aspirations.index');
    }

    public function updateStatus(Request $request, Aspiration $aspiration)
    {
        $this->ensureRtAccess($request, $aspiration);

        $status = $request->validate([
            'aspiration_status' => [
                'required',
                Rule::in(['dikirim', 'diterima', 'diproses', 'selesai', 'ditolak', 'diteruskan']),
            ],
        ])['aspiration_status'];

        // Hanya RT yang boleh meneruskan ke RW
        if ($status === 'diteruskan') {
            abort_unless($request->user()->isRt(), 403, 'Hanya Ketua RT yang bisa meneruskan ke RW.');
            abort_if($aspiration->aspiration_status === 'diteruskan', 400, 'Sudah diteruskan ke RW.');

            $aspiration->update([
                'aspiration_status' => 'diteruskan',
                'forwarded_to' => 'rw',
                'forwarded_by' => $request->user()->id,
                'forwarded_at' => now(),
            ]);

            $aspiration->loadMissing('user');
            Notifier::send(
                $aspiration->user,
                'Aspirasi diteruskan ke RW',
                'Aspirasi "'.$aspiration->aspiration_title.'" diteruskan Ketua RT ke RW untuk ditindaklanjuti.',
                route('aspirations.show', $aspiration),
                $request->user(),
            );
            // Notifikasi ke RW/Admin agar segera ditindaklanjuti
            $rwManagers = \App\Models\User::whereIn('role', ['rw', 'admin', 'superadmin'])->get();
            Notifier::sendMany(
                $rwManagers,
                'Aspirasi diteruskan ke RW',
                'Aspirasi "'.$aspiration->aspiration_title.'" diteruskan Ketua RT dan menunggu tindak lanjut RW.',
                route('aspirations.show', $aspiration),
                $request->user(),
            );

            return back()->with('success', 'Aspirasi berhasil diteruskan ke RW untuk ditindaklanjuti.');
        }

        // Jika diteruskan, hanya RW/Superadmin yang boleh finalisasi (selesai/ditolak/diproses)
        if ($aspiration->aspiration_status === 'diteruskan') {
            abort_unless($request->user()->isRw() || $request->user()->isSuperAdmin() || $request->user()->role === 'admin', 403, 'Aspirasi yang diteruskan hanya bisa diproses oleh RW/Admin.');
        }

        $aspiration->update(['aspiration_status' => $status]);

        $aspiration->loadMissing('user');
        Notifier::send(
            $aspiration->user,
            'Status aspirasi diperbarui',
            'Aspirasi "'.$aspiration->aspiration_title.'" kini '.ucfirst($status).'.',
            route('aspirations.show', $aspiration),
            $request->user(),
        );

        return back()->with('success', 'Status aspirasi berhasil diubah menjadi '.ucfirst($status).'.');
    }

    /**
     * Simpan tanggapan pengurus (RT/RW/Admin) untuk sebuah aspirasi.
     * Aturan kewenangan sama dengan ubah status: jika sudah diteruskan
     * ke RW, hanya RW/Admin yang boleh menanggapi.
     */
    public function storeTanggapan(Request $request, Aspiration $aspiration)
    {
        $this->ensureRtAccess($request, $aspiration);

        if ($aspiration->aspiration_status === 'diteruskan') {
            abort_unless($request->user()->isRw() || $request->user()->isSuperAdmin() || $request->user()->role === 'admin', 403, 'Aspirasi yang diteruskan hanya bisa ditanggapi oleh RW/Admin.');
        }

        $validated = $request->validate([
            'tanggapan' => ['required', 'string', 'max:5000'],
        ]);

        $aspiration->update([
            'tanggapan' => $validated['tanggapan'],
            'tanggapan_by' => $request->user()->id,
            'tanggapan_at' => now(),
        ]);

        $aspiration->loadMissing('user');
        Notifier::send(
            $aspiration->user,
            'Aspirasi mendapat tanggapan',
            'Aspirasi "'.$aspiration->aspiration_title.'" mendapat tanggapan dari pengurus.',
            route('aspirations.show', $aspiration),
            $request->user(),
        );

        return back()->with('success', 'Tanggapan berhasil disimpan.');
    }

    public function destroy(Aspiration $aspiration)
    {
        $this->ensureRtAccess(request(), $aspiration);

        if (! empty($aspiration->photo_path)) {
            Storage::disk('public')->delete($aspiration->photo_path);
        }

        $aspiration->delete();

        return redirect()->route('aspirations.index');
    }

    /**
     * Simpan foto aspirasi tanpa memakai UploadedFile::store().
     * store() memakai getRealPath() + fopen() yang di Laragon/Windows
     * bisa mengembalikan path kosong -> ValueError "Path must not be empty".
     * Cara ini memakai getPathname() + Storage::put() seperti modul lain.
     */
    private function storePhoto(Request $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        $file = $request->file('photo');

        if (! $file || ! $file->isValid() || ! is_file($file->getPathname())) {
            return null;
        }

        $contents = @file_get_contents($file->getPathname());
        if ($contents === false) {
            return null;
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $name = Str::random(40).'.'.$extension;

        Storage::disk('public')->put("aspirations/{$name}", $contents);

        return "aspirations/{$name}";
    }

    /**
     * Batasi query aspirasi untuk Ketua RT: hanya aspirasi RT-nya sendiri.
     * Memakai kolom rt_id (tetap ada walau akun warga dihapus),
     * dengan fallback ke relasi user untuk data lama yang rt_id-nya masih kosong.
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
            $q->where('aspirations.rt_id', $rtId)
                ->orWhere(function ($sub) use ($rtId) {
                    $sub->whereNull('aspirations.rt_id')
                        ->whereHas('user', fn ($uq) => $uq->where('rt_id', $rtId));
                });
        });
    }

    /**
     * Pastikan Ketua RT tidak bisa membuka/mengubah aspirasi RT lain.
     */
    private function ensureRtAccess(Request $request, Aspiration $aspiration): void
    {
        $user = $request->user();

        if (! $user->isRt()) {
            return;
        }

        $aspiration->loadMissing('user');

        $ownerRtId = $aspiration->rt_id ?? $aspiration->user?->rt_id;

        abort_unless(! empty($user->rt_id) && (int) $ownerRtId === (int) $user->rt_id, 404);
    }
}
