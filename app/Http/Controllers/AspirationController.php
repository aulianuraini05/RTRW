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
            );

        // Privasi per RT: Ketua RT hanya melihat aspirasi warga RT-nya sendiri.
        $this->applyRtScope(request(), $aspirations);

        $aspirations = $aspirations->latest('submission_date')->paginate(10);

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
                'submission_date' => ['required', 'date'],
            ]),
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

            return back()->with('success', 'Aspirasi berhasil diteruskan ke RW untuk ditindaklanjuti.');
        }

        // Jika diteruskan, hanya RW/Superadmin yang boleh finalisasi (selesai/ditolak/diproses)
        if ($aspiration->aspiration_status === 'diteruskan') {
            abort_unless($request->user()->isRw() || $request->user()->isSuperAdmin() || $request->user()->role === 'admin', 403, 'Aspirasi yang diteruskan hanya bisa diproses oleh RW/Admin.');
        }

        $aspiration->update(['aspiration_status' => $status]);

        return back()->with('success', 'Status aspirasi berhasil diubah menjadi '.ucfirst($status).'.');
    }

    public function destroy(Aspiration $aspiration)
    {
        $this->ensureRtAccess(request(), $aspiration);

        $aspiration->delete();

        return redirect()->route('aspirations.index');
    }

    /**
     * Batasi query aspirasi untuk Ketua RT: hanya aspirasi warga RT-nya sendiri.
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
     * Pastikan Ketua RT tidak bisa membuka/mengubah aspirasi RT lain.
     */
    private function ensureRtAccess(Request $request, Aspiration $aspiration): void
    {
        $user = $request->user();

        if (! $user->isRt()) {
            return;
        }

        $aspiration->loadMissing('user');

        $inScope = ! empty($user->rt_id)
            && $aspiration->user
            && (int) $aspiration->user->rt_id === (int) $user->rt_id;

        abort_unless($inScope, 404);
    }
}
