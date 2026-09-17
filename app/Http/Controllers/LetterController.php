<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LetterController extends Controller
{
    public function index()
    {
        $letters = Letter::query()
            ->with('user')
            ->when(
                ! request()->user()->isAdmin(),
                fn ($query) => $query->where('user_id', request()->user()->id),
            );

        // Privasi per RT: Ketua RT hanya melihat surat warga RT-nya sendiri.
        $this->applyRtScope(request(), $letters);

        $letters = $letters->latest('submission_date')
            ->paginate(10);

        return view('letters.index', compact('letters'));
    }

    public function create()
    {
        abort_unless(request()->user()->isWarga(), 403);

        return view('letters.create');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isWarga(), 403);

        $validated = $request->validate([
            'letter_type' => ['required', 'string', 'max:100'],
            'purpose' => ['required', 'string'],
            'submission_date' => ['required', 'date'],
        ]);

        $request->user()->letters()->create([
            ...$validated,
            // Nomor surat selalu digenerate otomatis — tidak bisa diisi manual.
            'letter_number' => $this->generateLetterNumber(),
            'letter_status' => 'diajukan',
        ]);

        return redirect()->route('letters.index')
            ->with('success', 'Permohonan surat berhasil dikirim dan menunggu persetujuan RT/RW.');
    }

    public function show(Letter $letter)
    {
        if (! request()->user()->isAdmin() && $letter->user_id !== request()->user()->id) {
            abort(404);
        }

        $this->ensureRtAccess(request(), $letter);

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

        return view('letters.edit', compact('letter'));
    }

    public function update(Request $request, Letter $letter)
    {
        $this->ensureRtAccess($request, $letter);

        $validated = $request->validate([
            'letter_type' => ['required', 'string', 'max:100'],
            'purpose' => ['required', 'string'],
            'submission_date' => ['required', 'date'],
            'letter_status' => ['required', Rule::in(['diajukan', 'diproses', 'disetujui', 'ditolak', 'selesai'])],
        ]);

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

        return back()->with('success', 'Status surat berhasil diubah menjadi '.ucfirst($status).'.');
    }

    public function destroy(Letter $letter)
    {
        $this->ensureRtAccess(request(), $letter);

        $letter->delete();

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
