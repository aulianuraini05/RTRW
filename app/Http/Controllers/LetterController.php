<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Services\ActivityLog;
use App\Services\Notifier;
use App\Support\LetterRequirements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LetterController extends Controller
{
    public function index(Request $request)
    {
        $letters = Letter::query()
            ->with('user')
            ->when(
                ! request()->user()->isAdmin(),
                fn ($query) => $query->where('user_id', request()->user()->id),
            );

        // Privasi per RT: Ketua RT hanya melihat surat warga RT-nya sendiri.
        $this->applyRtScope(request(), $letters);

        // Cari: nomor surat / jenis / keperluan.
        $letters->when($request->filled('search'), function ($query) use ($request) {
            $search = '%'.trim($request->string('search')).'%';

            return $query->where(function ($sub) use ($search) {
                $sub->where('letter_number', 'like', $search)
                    ->orWhere('letter_type', 'like', $search)
                    ->orWhere('purpose', 'like', $search);
            });
        });

        // Filter status.
        $letters->when($request->filled('status'), function ($query) use ($request) {
            $statuses = ['diajukan', 'diproses', 'disetujui', 'selesai', 'ditolak'];
            $status = $request->string('status')->toString();

            return in_array($status, $statuses, true)
                ? $query->where('letter_status', $status)
                : $query;
        });

        // Filter jenis surat.
        $letters->when($request->filled('type'), function ($query) use ($request) {
            $type = $request->string('type')->toString();

            return in_array($type, LetterRequirements::types(), true)
                ? $query->where('letter_type', $type)
                : $query;
        });

        // Urutkan: terbaru (default) / terlama.
        $sort = $request->string('sort')->toString();
        $letters = $sort === 'terlama'
            ? $letters->oldest('submission_date')->paginate(10)->withQueryString()
            : $letters->latest('submission_date')->paginate(10)->withQueryString();

        $letterTypes = LetterRequirements::types();

        return view('letters.index', compact('letters', 'letterTypes'));
    }

    public function create()
    {
        abort_unless(request()->user()->isWarga(), 403);

        $requirements = LetterRequirements::all();

        return view('letters.create', compact('requirements'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isWarga(), 403);

        $validated = $request->validate([
            'letter_type' => ['required', 'string', Rule::in(LetterRequirements::types())],
            'purpose' => ['required', 'string'],
            'submission_date' => ['required', 'date'],
        ]);

        // Validasi lampiran dinamis sesuai jenis surat.
        $requirements = LetterRequirements::for($validated['letter_type']);
        $fileRules = [];
        foreach ($requirements as $req) {
            $fileRules['attachments.'.$req['key']] = [
                'required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240',
            ];
        }
        $request->validate($fileRules, [
            'attachments.*.required' => 'Lampiran wajib diunggah.',
            'attachments.*.mimes' => 'Lampiran harus JPG, PNG, WEBP, atau PDF.',
            'attachments.*.max' => 'Ukuran tiap lampiran maksimal 10 MB.',
        ]);

        $letter = $request->user()->letters()->create([
            ...$validated,
            // Nomor surat selalu digenerate otomatis — tidak bisa diisi manual.
            'letter_number' => $this->generateLetterNumber(),
            'letter_status' => 'diajukan',
        ]);

        // Simpan tiap lampiran sesuai syarat jenis suratnya.
        foreach ($requirements as $req) {
            $file = $request->file('attachments.'.$req['key']);
            if (! $file) {
                continue;
            }
            $path = $this->storeAttachment($file, $letter->id, $req['key']);
            if ($path) {
                $letter->attachments()->create([
                    'doc_key' => $req['key'],
                    'label' => $req['label'],
                    'file_path' => $path,
                ]);
            }
        }

        $managers = Notifier::managersForRt($request->user()->rt_id);
        Notifier::sendMany(
            $managers,
            'Pengajuan surat baru',
            $request->user()->name.' mengajukan surat: '.$letter->letter_type.' ('.$letter->letter_number.')',
            route('letters.show', $letter),
            $request->user(),
        );

        ActivityLog::record($request->user(), 'surat', 'mengajukan surat', $letter->letter_type.' ('.$letter->letter_number.')');

        return redirect()->route('letters.index')
            ->with('success', 'Permohonan surat berhasil dikirim dan menunggu persetujuan RT/RW.');
    }

    public function show(Letter $letter)
    {
        if (! request()->user()->isAdmin() && $letter->user_id !== request()->user()->id) {
            abort(404);
        }

        $this->ensureRtAccess(request(), $letter);

        $letter->load(['user', 'attachments']);

        return view('letters.show', compact('letter'));
    }

    public function cetak(Letter $letter)
    {
        if (! request()->user()->isAdmin() && $letter->user_id !== request()->user()->id) {
            abort(404);
        }

        $this->ensureRtAccess(request(), $letter);

        abort_unless(
            in_array($letter->letter_status, ['disetujui', 'selesai'], true),
            403,
            'Surat resmi hanya bisa dicetak setelah disetujui.'
        );

        $letter->load('user.rt');

        return view('letters.print', compact('letter'));
    }

    public function edit(Letter $letter)
    {
        $this->ensureRtAccess(request(), $letter);

        $letter->load('attachments');

        return view('letters.edit', compact('letter'));
    }

    public function update(Request $request, Letter $letter)
    {
        $this->ensureRtAccess($request, $letter);

        $validated = $request->validate([
            'letter_type' => ['required', 'string', Rule::in(LetterRequirements::types())],
            'purpose' => ['required', 'string'],
            'submission_date' => ['required', 'date'],
            'letter_status' => ['required', Rule::in(['diajukan', 'diproses', 'disetujui', 'ditolak', 'selesai'])],
        ]);

        // Lampiran tidak diubah saat edit — tetap jadi bukti pengajuan awal
        // walau jenis surat diganti admin.

        $letter->update($validated);

        return redirect()->route('letters.index')
            ->with('success', 'Data surat berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Letter $letter)
    {
        $this->ensureRtAccess($request, $letter);

        $status = $request->validate([
            'letter_status' => [
                'required',
                Rule::in(['diajukan', 'diproses', 'disetujui', 'ditolak', 'selesai']),
            ],
        ])['letter_status'];

        $letter->update([
            'letter_status' => $status,
        ]);

        ActivityLog::record($request->user(), 'surat', 'mengubah status menjadi '.ucfirst($status), $letter->letter_number.' ('.$letter->letter_type.')');

        $letter->loadMissing('user');
        Notifier::send(
            $letter->user,
            'Status surat diperbarui',
            'Surat '.$letter->letter_number.' kini '.ucfirst($status).'.',
            route('letters.show', $letter),
            $request->user(),
        );

        return back()->with('success', 'Status surat berhasil diubah menjadi '.ucfirst($status).'.');
    }

    public function destroy(Letter $letter)
    {
        $this->ensureRtAccess(request(), $letter);

        $subject = $letter->letter_number.' ('.$letter->letter_type.')';
        foreach ($letter->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $letter->delete();

        ActivityLog::record(request()->user(), 'surat', 'menghapus surat', $subject);

        return redirect()->route('letters.index')
            ->with('success', 'Surat berhasil dihapus.');
    }

    private function generateLetterNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Letter::whereDate('created_at', today())->count() + 1;

        return 'SURAT/'.$date.'/'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Simpan 1 file lampiran tanpa UploadedFile::store() agar aman di Laragon/Windows.
     */
    private function storeAttachment($file, int $letterId, string $docKey): ?string
    {
        if (! $file || ! $file->isValid() || ! is_file($file->getPathname())) {
            return null;
        }

        $contents = @file_get_contents($file->getPathname());
        if ($contents === false) {
            return null;
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $name = Str::random(40).'.'.$extension;

        Storage::disk('public')->put("letters/{$letterId}/{$docKey}_{$name}", $contents);

        return "letters/{$letterId}/{$docKey}_{$name}";
    }

    /**
     * Batasi query surat untuk Ketua RT: hanya surat warga RT-nya sendiri.
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

        $query->whereHas('user', fn ($q) => $q->where('rt_id', $rtId));
    }

    /**
     * Pastikan Ketua RT tidak bisa membuka/mengubah surat RT lain.
     */
    private function ensureRtAccess(Request $request, Letter $letter): void
    {
        $user = $request->user();

        if (! $user->isRt()) {
            return;
        }

        $letter->loadMissing('user');

        $inScope = ! empty($user->rt_id)
            && $letter->user
            && (int) $letter->user->rt_id === (int) $user->rt_id;

        abort_unless($inScope, 404);
    }
}
