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
            )
            ->latest('submission_date')
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
            'letter_number' => ['nullable', 'string', 'max:100'],
            'letter_type' => ['required', 'string', 'max:100'],
            'purpose' => ['required', 'string'],
            'submission_date' => ['required', 'date'],
        ]);

        $request->user()->letters()->create([
            ...$validated,
            'letter_number' => $validated['letter_number'] ?: $this->generateLetterNumber(),
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

        return view('letters.show', compact('letter'));
    }

    public function edit(Letter $letter)
    {
        return view('letters.edit', compact('letter'));
    }

    public function update(Request $request, Letter $letter)
    {
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
}
