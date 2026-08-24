@props(['announcement'])

@php
    $title = $announcement->announcement_title ?? $announcement->title ?? '';
    $content = $announcement->announcement_content ?? $announcement->content ?? '';
    $date = isset($announcement->publication_date) 
        ? $announcement->publication_date->translatedFormat('d F Y') 
        : ($announcement->created_at ? $announcement->created_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y'));
    $category = strtolower($announcement->category ?? 'umum');
    $priority = strtolower($announcement->priority ?? 'biasa');
    $isPinned = $announcement->is_pinned ?? false;

    // Snippet Deskripsi Singkat 2-3 Baris
    $sentences = preg_split('/(?<=[.?!])\s+/', trim(strip_tags($content)));
    $shortDescription = implode(' ', array_slice($sentences, 0, 2));
    if (empty($shortDescription)) {
        $shortDescription = $content;
    }

    // Variasi Warna Pastel Lembut (Rotasi Berdasarkan ID Pengumuman untuk Tampilan Bersih & Beragam)
    $palettes = [
        [ // Pastel Hijau
            'card' => 'bg-emerald-50/80 border-emerald-200/60 hover:bg-emerald-50 hover:border-emerald-300',
            'category_badge' => 'bg-emerald-100/90 text-emerald-800 border-emerald-200',
            'btn' => 'bg-white text-emerald-800 hover:bg-emerald-700 hover:text-white border-emerald-200/80',
        ],
        [ // Pastel Biru
            'card' => 'bg-sky-50/80 border-sky-200/60 hover:bg-sky-50 hover:border-sky-300',
            'category_badge' => 'bg-sky-100/90 text-sky-800 border-sky-200',
            'btn' => 'bg-white text-sky-800 hover:bg-sky-700 hover:text-white border-sky-200/80',
        ],
        [ // Pastel Ungu
            'card' => 'bg-purple-50/80 border-purple-200/60 hover:bg-purple-50 hover:border-purple-300',
            'category_badge' => 'bg-purple-100/90 text-purple-800 border-purple-200',
            'btn' => 'bg-white text-purple-800 hover:bg-purple-700 hover:text-white border-purple-200/80',
        ],
        [ // Pastel Orange
            'card' => 'bg-amber-50/80 border-amber-200/60 hover:bg-amber-50 hover:border-amber-300',
            'category_badge' => 'bg-amber-100/90 text-amber-900 border-amber-200',
            'btn' => 'bg-white text-amber-800 hover:bg-amber-700 hover:text-white border-amber-200/80',
        ],
        [ // Pastel Teal
            'card' => 'bg-teal-50/80 border-teal-200/60 hover:bg-teal-50 hover:border-teal-300',
            'category_badge' => 'bg-teal-100/90 text-teal-800 border-teal-200',
            'btn' => 'bg-white text-teal-800 hover:bg-teal-700 hover:text-white border-teal-200/80',
        ],
    ];

    $paletteIndex = ($announcement->id ?? 0) % count($palettes);
    $palette = $palettes[$paletteIndex];
@endphp

{{-- KARTU PENGUMUMAN MODERN, BERSIH, LEGA, & NYAMAN DIBACA WARGA --}}
<a href="{{ route('announcements.show', $announcement) }}"
   class="group relative flex flex-col sm:flex-row sm:items-center justify-between rounded-2xl {{ $palette['card'] }} border p-5 sm:p-6 shadow-xs hover:shadow-md transition-all duration-200 gap-4 sm:gap-6"
   aria-label="Baca pengumuman: {{ $title }}">

    {{-- KONTEN UTAMA (KIRI - TENGAH) --}}
    <div class="flex-1 min-w-0 space-y-2.5">
        
        {{-- BAGIAN ATAS: Badge Kategori & Sifat Pengumuman --}}
        <div class="flex flex-wrap items-center gap-2">
            {{-- Badge Kategori --}}
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full {{ $palette['category_badge'] }} text-xs font-bold border shadow-2xs">
                ⭐ {{ ucfirst($category) }}
            </span>

            {{-- Sifat Pengumuman (Mendesak / Penting / Biasa) --}}
            @if ($priority === 'mendesak')
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-600 text-white text-xs font-extrabold shadow-2xs">
                    🚨 Mendesak
                </span>
            @elseif ($priority === 'penting')
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-600 text-white text-xs font-bold shadow-2xs">
                    📌 Penting
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-200/90 text-slate-700 text-xs font-medium border border-slate-300/60">
                    💬 Biasa
                </span>
            @endif

            {{-- Disematkan --}}
            @if ($isPinned)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-600 text-white text-xs font-bold shadow-2xs">
                    📌 Disematkan
                </span>
            @endif

            {{-- Penanda Belum Dibaca --}}
            @if (isset($announcement->is_read) && !$announcement->is_read)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-600 text-white text-xs font-bold uppercase tracking-wider shadow-2xs">
                    <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                    Belum dibaca
                </span>
            @endif
        </div>

        {{-- BAGIAN UTAMA: Judul Utama & Deskripsi Ringkas --}}
        <div>
            <h3 class="text-base sm:text-lg md:text-xl font-bold text-slate-900 leading-snug group-hover:text-blue-700 transition-colors line-clamp-2">
                {{ $title }}
            </h3>
            
            <p class="mt-1 text-sm text-slate-600 leading-relaxed line-clamp-2 sm:line-clamp-3">
                {{ $shortDescription }}
            </p>
        </div>

        {{-- BAGIAN BAWAH: Metadata (Tanggal, Kategori, Dibaca) --}}
        <div class="flex flex-wrap items-center gap-3 pt-1 text-xs text-slate-500 font-medium">
            <span class="inline-flex items-center gap-1.5 text-slate-600">
                <span class="text-sm">🗓️</span>
                <span>{{ $date }}</span>
            </span>

            <span class="text-slate-300">•</span>

            <span class="inline-flex items-center gap-1.5 text-slate-600 capitalize">
                <span class="text-sm">🏷️</span>
                <span>{{ $category }}</span>
            </span>

            @if (isset($announcement->read_count))
                <span class="text-slate-300">•</span>
                <span class="inline-flex items-center gap-1 text-slate-500">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{ number_format($announcement->read_count) }} dibaca</span>
                </span>
            @endif
        </div>

    </div>

    {{-- BAGIAN KANAN: Tombol Arrow (→) Lingkaran Kecil --}}
    <div class="shrink-0 self-end sm:self-center">
        <div class="w-10 h-10 rounded-full {{ $palette['btn'] }} shadow-2xs border flex items-center justify-center transition-all duration-200 group-hover:scale-110">
            <svg class="w-5 h-5 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </div>
    </div>

</a>
