<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AspirationController extends Controller
{
    public function index()
    {
        $aspirations = Aspiration::query()
            ->with('user')
            ->when(
                ! request()->user()->isAdmin(),
                fn ($query) => $query->where('user_id', request()->user()->id),
            )
            ->latest('submission_date')
            ->paginate(10);

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

        $request->user()->aspirations()->create([
            ...$request->validate([
                'aspiration_title' => ['required', 'string', 'max:255'],
                'aspiration_content' => ['required', 'string'],
                'category' => ['required', 'string', 'max:100'],
            ]),
            'submission_date' => today(),
            'aspiration_status' => 'dikirim',
        ]);

        return redirect()->route('aspirations.index')
            ->with('success', 'Aspirasi berhasil dikirim dan menunggu tindak lanjut RT/RW.');
    }

    public function show(Aspiration $aspiration)
    {
        if (! request()->user()->isAdmin() && $aspiration->user_id !== request()->user()->id) {
            abort(404);
        }

        return view('aspirations.show', compact('aspiration'));
    }

    public function edit(Aspiration $aspiration)
    {
        return view('aspirations.edit', compact('aspiration'));
    }

    public function update(Request $request, Aspiration $aspiration)
    {
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
        $status = $request->validate([
            'aspiration_status' => [
                'required',
                Rule::in(['dikirim', 'diterima', 'diproses', 'selesai', 'ditolak']),
            ],
        ])['aspiration_status'];

        $aspiration->update(['aspiration_status' => $status]);

        return back()->with('success', 'Status aspirasi berhasil diubah menjadi '.ucfirst($status).'.');
    }

    public function destroy(Aspiration $aspiration)
    {
        $aspiration->delete();

        return redirect()->route('aspirations.index');
    }
}
