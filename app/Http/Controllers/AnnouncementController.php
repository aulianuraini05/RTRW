<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::query()
            ->when(! request()->user()->isAdmin(), fn ($query) => $query->where('status', 'active'))
            ->latest('publication_date')
            ->paginate(10);

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);

        Announcement::create($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function show(Announcement $announcement)
    {
        if ($announcement->status !== 'active' && ! request()->user()->isAdmin()) {
            abort(404);
        }

        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $this->validatedData($request);

        $announcement->update($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * @return array<string, string>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'announcement_title' => ['required', 'string', 'max:255'],
            'announcement_content' => ['required', 'string'],
            'publication_date' => ['required', 'date'],
            'status' => ['required', 'in:active,archived'],
        ]);
    }
}
