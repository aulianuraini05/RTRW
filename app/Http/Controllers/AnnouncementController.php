<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            ->orderByDesc('is_pinned')
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
        $validated['image'] = $this->storeImage($request);

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

        if ($request->hasFile('image')) {
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            $validated['image'] = $this->storeImage($request);
        } else {
            unset($validated['image']);
        }

        $announcement->update($validated);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image);
        }

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
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'publication_date' => ['required', 'date'],
            'status' => ['required', 'in:active,archived'],
            'category' => ['required', 'string', 'in:umum,kegiatan,kesehatan,keamanan,lingkungan,agenda'],
            'priority' => ['required', 'string', 'in:biasa,penting,mendesak'],
            'is_pinned' => ['sometimes', 'boolean'],
        ], [
            'image.max' => 'Ukuran foto maksimal 10 MB.',
            'image.image' => 'File yang diunggah harus berupa foto.',
            'image.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
        ]);
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

        $maxSize = 250;
        $source = @imagecreatefromstring(file_get_contents($file->getPathname()));

        if (! $source) {
            return null;
        }

        $origW = imagesx($source);
        $origH = imagesy($source);

        if ($origW <= $maxSize && $origH <= $maxSize) {
            $newW = $origW;
            $newH = $origH;
        } elseif ($origW >= $origH) {
            $newW = $maxSize;
            $newH = (int) round($origH * ($maxSize / $origW));
        } else {
            $newH = $maxSize;
            $newW = (int) round($origW * ($maxSize / $origH));
        }

        $resized = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($source);

        $name = Str::random(40) . '.jpg';

        ob_start();
        imagejpeg($resized, null, 85);
        $buffer = ob_get_clean();
        imagedestroy($resized);

        Storage::disk('public')->put("announcements/{$name}", $buffer);

        return "announcements/{$name}";
    }
}
