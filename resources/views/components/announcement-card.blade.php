@props(['announcement'])

@php
    $title = $announcement->announcement_title ?? $announcement->title ?? '';
    $content = $announcement->announcement_content ?? $announcement->content ?? '';
    $date = isset($announcement->publication_date)
        ? $announcement->publication_date->translatedFormat('d M Y')
        : ($announcement->created_at ? $announcement->created_at->translatedFormat('d M Y') : now()->translatedFormat('d M Y'));
    $category = strtolower($announcement->category ?? 'umum');
    $priority = strtolower($announcement->priority ?? 'biasa');
    $isPinned = $announcement->is_pinned ?? false;

    $sentences = preg_split('/(?<=[.?!])\s+/', trim(strip_tags($content)));
    $shortDescription = implode(' ', array_slice($sentences, 0, 2));
    if (empty($shortDescription)) {
        $shortDescription = Str::limit(strip_tags($content), 120);
    }

    $palettes = [
        ['card' => 'bg-sky-50', 'accent' => 'bg-sky-100 text-sky-700', 'img_bg' => 'bg-sky-100', 'img_icon' => 'text-sky-400'],
        ['card' => 'bg-emerald-50', 'accent' => 'bg-emerald-100 text-emerald-700', 'img_bg' => 'bg-emerald-100', 'img_icon' => 'text-emerald-400'],
        ['card' => 'bg-purple-50', 'accent' => 'bg-purple-100 text-purple-700', 'img_bg' => 'bg-purple-100', 'img_icon' => 'text-purple-400'],
        ['card' => 'bg-amber-50', 'accent' => 'bg-amber-100 text-amber-700', 'img_bg' => 'bg-amber-100', 'img_icon' => 'text-amber-400'],
        ['card' => 'bg-teal-50', 'accent' => 'bg-teal-100 text-teal-700', 'img_bg' => 'bg-teal-100', 'img_icon' => 'text-teal-400'],
    ];

    $paletteIndex = ($announcement->id ?? 0) % count($palettes);
    $palette = $palettes[$paletteIndex];

    $categoryLabels = [
        'umum' => 'Umum',
        'kegiatan' => 'Kegiatan',
        'kesehatan' => 'Kesehatan',
        'keamanan' => 'Keamanan',
        'lingkungan' => 'Lingkungan',
        'agenda' => 'Agenda',
    ];
@endphp

<a href="{{ route('announcements.show', $announcement) }}"
   class="group flex flex-col items-center sm:flex-row sm:items-center rounded-[36px] {{ $palette['card'] }} border border-white/60 p-4 sm:p-5 shadow-sm hover:shadow-md transition-all duration-200 gap-3 sm:gap-5"
   aria-label="Baca pengumuman: {{ $title }}">

    {{-- Gambar / Placeholder --}}
    <div class="shrink-0 overflow-hidden rounded-full border-2 border-white shadow-sm" style="width: 80px; height: 80px; min-width: 80px; min-height: 80px;">
        @if ($announcement->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($announcement->image))
            <img src="{{ Storage::url($announcement->image) }}" alt="{{ $title }}" class="block w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center {{ $palette['img_bg'] }}">
                <svg class="w-8 h-8 {{ $palette['img_icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V5.25a1.5 1.5 0 00-1.5-1.5H3.75a1.5 1.5 0 00-1.5 1.5v14.25a1.5 1.5 0 001.5 1.5z" />
                </svg>
            </div>
        @endif
    </div>

    {{-- Konten Tengah --}}
    <div class="flex-1 min-w-0 flex flex-col justify-between gap-2">

        {{-- Badge --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $palette['accent'] }}">
                {{ $categoryLabels[$category] ?? ucfirst($category) }}
            </span>

            @if ($priority === 'mendesak')
                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 text-red-700 px-2.5 py-0.5 text-[11px] font-semibold">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Mendesak
                </span>
            @elseif ($priority === 'penting')
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-700 px-2.5 py-0.5 text-[11px] font-semibold">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    Penting
                </span>
            @else
                <span class="inline-flex items-center rounded-full bg-gray-100 text-gray-600 px-2.5 py-0.5 text-[11px] font-medium">
                    Biasa
                </span>
            @endif

            @if ($isPinned)
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-700 px-2.5 py-0.5 text-[11px] font-semibold">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    Disematkan
                </span>
            @endif

            @if (isset($announcement->is_read) && !$announcement->is_read)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-500 text-white px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    Baru
                </span>
            @endif
        </div>

        {{-- Judul --}}
        <h3 class="text-[17px] sm:text-lg font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-gray-700 transition-colors">
            {{ $title }}
        </h3>

        {{-- Deskripsi --}}
        <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">
            {{ $shortDescription }}
        </p>

        {{-- Metadata --}}
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-400 mt-auto pt-1">
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $date }}
            </span>

            @if (isset($announcement->read_count))
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ number_format($announcement->read_count) }} dibaca
                </span>
            @endif
        </div>
    </div>

    {{-- Tombol Detail --}}
    <div class="shrink-0 flex items-center self-center">
        <div class="w-11 h-11 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center transition-all duration-200 group-hover:shadow-md group-hover:border-gray-200 group-hover:scale-105">
            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 group-hover:translate-x-0.5 group-hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </div>
    </div>

</a>
