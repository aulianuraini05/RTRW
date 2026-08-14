<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $announcements = Announcement::query()
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                return $query->where('status', 'active')
                    ->withExists(['readBy as is_read' => fn ($q) => $q->where('user_id', $user->id)]);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim();

                return $query->where(function ($sub) use ($search) {
                    $sub->where('announcement_title', 'like', "%{$search}%")
                        ->orWhere('announcement_content', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('filter'), function ($query) use ($request, $user) {
                $filter = $request->string('filter')->toString();

                if ($user->isAdmin() && $filter === 'nonaktif') {
                    return $query->where('status', 'archived');
                }

                return match ($filter) {
                    'mendesak' => $query->where('priority', 'mendesak'),
                    'penting' => $query->where('priority', 'penting'),
                    'biasa' => $query->where('priority', 'biasa'),
                    default => $query,
                };
            })
            ->latest('publication_date')
            ->paginate(10)
            ->withQueryString();

        $stats = $user->isAdmin() ? $this->stats() : [];

        return view('announcements.index', array_merge(compact('announcements'), $stats));
    }

    /**
     * Ringkasan statistik untuk panel administrasi.
     *
     * @return array<string, int>
     */
    private function stats(): array
    {
        $total = Announcement::count();
        $active = Announcement::where('status', 'active')->count();
        $urgent = Announcement::where('priority', 'mendesak')->count();
        $totalRead = (int) Announcement::sum('read_count');

        $thisMonth = Announcement::whereBetween('publication_date', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $lastMonth = Announcement::whereBetween('publication_date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();

        $trend = $lastMonth > 0
            ? (int) round((($thisMonth - $lastMonth) / $lastMonth) * 100)
            : ($thisMonth > 0 ? 100 : 0);

        return [
            'totalAnnouncements' => $total,
            'activeAnnouncements' => $active,
            'urgentAnnouncements' => $urgent,
            'totalRead' => $totalRead,
            'activePercentage' => $total > 0 ? (int) round(($active / $total) * 100) : 0,
            'trend' => $trend,
            'trendIsUp' => $trend >= 0,
        ];
    }

    public function toggleStatus(Announcement $announcement)
    {
        $announcement->update([
            'status' => $announcement->status === 'active' ? 'archived' : 'active',
        ]);

        return back()->with('success', 'Status pengumuman berhasil diperbarui.');
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
        $user = request()->user();

        if ($announcement->status !== 'active' && ! $user->isAdmin()) {
            abort(404);
        }

        if ($user->isWarga() && ! $announcement->readBy()->whereKey($user->id)->exists()) {
            $announcement->readBy()->attach($user->id, ['read_at' => now()]);
            $announcement->increment('read_count');
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
            'category' => ['required', 'string', 'in:umum,kegiatan,kesehatan,keamanan,lingkungan,agenda'],
            'priority' => ['required', 'string', 'in:biasa,penting,mendesak'],
            'is_pinned' => ['sometimes', 'boolean'],
        ]);
    }
}
