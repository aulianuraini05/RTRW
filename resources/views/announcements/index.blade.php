<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800 leading-tight">
                    {{ Auth::user()->isAdmin() ? 'Kelola Pengumuman' : 'Pengumuman' }}
                </h2>
                <p class="mt-0.5 text-sm text-gray-500">
                    {{ Auth::user()->isAdmin()
                        ? 'Pantau, filter, dan kelola seluruh pengumuman RT/RW dalam satu tempat.'
                        : 'Informasi dan pengumuman terbaru dari pengurus RT/RW.' }}
                </p>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="space-y-6">
            @if (session('success'))
                <div class="flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (Auth::user()->isAdmin())
                {{-- ============================================================
                    1. BAGIAN STATISTIK (4 KARTU)
                    ============================================================ --}}
                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {{-- Total Pengumuman --}}
                    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Pengumuman</p>
                                <p class="mt-2 text-xl font-extrabold text-gray-900">{{ number_format($totalAnnouncements) }}</p>
                            </div>
                            <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-1.5 text-xs font-medium {{ $trendIsUp ? 'text-emerald-600' : 'text-red-600' }}">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                @if ($trendIsUp)
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                                @endif
                            </svg>
                            <span>{{ $trendIsUp ? '+' : '' }}{{ $trend }}%</span>
                            <span class="font-normal text-gray-400">vs bulan lalu</span>
                        </div>
                    </div>

                    {{-- Aktif & Tayang --}}
                    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Aktif & Tayang</p>
                                <p class="mt-2 text-xl font-extrabold text-gray-900">{{ number_format($activeAnnouncements) }}</p>
                            </div>
                            <div class="rounded-xl bg-sky-50 p-2.5 text-sky-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-sky-500" style="width: {{ $activePercentage }}%"></div>
                            </div>
                            <p class="mt-2 text-xs font-medium text-gray-500">{{ $activePercentage }}% dari total pengumuman</p>
                        </div>
                    </div>

                    {{-- Mendesak / Perlu Perhatian --}}
                    <div class="rounded-2xl border border-red-100 bg-red-50/60 p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-red-600">Mendesak / Perlu Perhatian</p>
                                <p class="mt-2 text-xl font-extrabold text-red-700">{{ number_format($urgentAnnouncements) }}</p>
                            </div>
                            <div class="rounded-xl bg-red-100 p-2.5 text-red-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-4 flex items-center gap-1.5 text-xs font-medium text-red-600">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10A8 8 0 11 2 10a8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Prioritas tertinggi, perlu tindakan segera
                        </p>
                    </div>

                    {{-- Total Dibaca --}}
                    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Dibaca</p>
                                <p class="mt-2 text-xl font-extrabold text-gray-900">{{ number_format($totalRead) }}</p>
                            </div>
                            <div class="rounded-xl bg-violet-50 p-2.5 text-violet-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-4 text-xs font-medium text-gray-500">Jumlah pembacaan oleh warga</p>
                    </div>
                </section>

                {{-- ============================================================
                    2. FILTER & PENCARIAN
                    ============================================================ --}}
                <section class="flex flex-col gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" action="{{ route('announcements.index') }}" class="flex flex-1 flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        {{-- Tombol filter kategori --}}
                        <div class="flex flex-wrap items-center gap-2">
                            @php
                                $filters = [
                                    '' => 'Semua',
                                    'mendesak' => 'Mendesak',
                                    'penting' => 'Penting',
                                    'biasa' => 'Biasa',
                                    'nonaktif' => 'Nonaktif',
                                ];
                            @endphp
                            @foreach ($filters as $value => $label)
                                <a href="{{ route('announcements.index', array_merge(request()->except(['filter', 'page']), ['filter' => $value])) }}"
                                    class="rounded-full px-3.5 py-1.5 text-sm font-medium transition {{ (request('filter', '') === $value) ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Kolom pencarian --}}
                            <div class="relative flex-1">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengumuman..."
                                    class="w-full rounded-lg border-gray-200 bg-gray-50 py-2 pl-9 pr-9 text-sm text-gray-800 placeholder-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500" />
                                @if (request('filter'))
                                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                                @endif
                                @if (request('search'))
                                    <a href="{{ route('announcements.index', request()->except(['search', 'page'])) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 rounded-full p-0.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Cari</button>
                        </div>
                    </form>

                    <a href="{{ route('announcements.create') }}" class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Baru
                    </a>
                </section>

                {{-- ============================================================
                    3. TABEL DATA PENGUMUMAN
                    ============================================================ --}}
                <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                            <thead class="bg-gray-50">
                                <tr class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <th class="px-6 py-3.5">Judul Pengumuman</th>
                                    <th class="px-4 py-3.5">Kategori</th>
                                    <th class="px-4 py-3.5">Prioritas</th>
                                    <th class="px-4 py-3.5">Status</th>
                                    <th class="px-4 py-3.5">Dibaca</th>
                                    <th class="px-4 py-3.5">Tanggal</th>
                                    <th class="px-4 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @php
                                    $categoryStyles = [
                                        'umum' => 'bg-slate-100 text-slate-700',
                                        'kegiatan' => 'bg-sky-100 text-sky-700',
                                        'kesehatan' => 'bg-emerald-100 text-emerald-700',
                                        'keamanan' => 'bg-amber-100 text-amber-700',
                                        'lingkungan' => 'bg-lime-100 text-lime-700',
                                        'agenda' => 'bg-indigo-100 text-indigo-700',
                                    ];
                                    $priorityStyles = [
                                        'biasa' => 'bg-slate-100 text-slate-600',
                                        'penting' => 'bg-amber-100 text-amber-700',
                                        'mendesak' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                @forelse ($announcements as $announcement)
                                    <tr class="transition hover:bg-gray-50/70">
                                        {{-- Judul + sub-teks + ikon pinned --}}
                                        <td class="max-w-sm px-6 py-4">
                                            <div class="flex items-start gap-2">
                                                @if ($announcement->is_pinned)
                                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2.93 17.07A10 10 0 1117.07 2.93 10 10 0 012.93 17.07zm12.73-1.41A8 8 0 104.34 4.34a8 8 0 0011.32 11.32zM10 5a1 1 0 011 1v4a1 1 0 01-2 0V6a1 1 0 011-1zm0 8a1 1 0 100 2 1 1 0 000-2z" />
                                                    </svg>
                                                @endif
                                                <div class="min-w-0">
                                                    <a href="{{ route('announcements.show', $announcement) }}" class="block truncate font-semibold text-gray-900 hover:text-emerald-600">
                                                        {{ $announcement->announcement_title }}
                                                    </a>
                                                    <p class="mt-0.5 truncate text-xs text-gray-500">{{ Str::limit($announcement->announcement_content, 60) }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Kategori --}}
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $categoryStyles[$announcement->category] ?? $categoryStyles['umum'] }}">
                                                {{ ucfirst($announcement->category) }}
                                            </span>
                                        </td>

                                        {{-- Prioritas --}}
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold {{ $priorityStyles[$announcement->priority] ?? $priorityStyles['biasa'] }}">
                                                @if ($announcement->priority === 'mendesak')
                                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                                {{ ucfirst($announcement->priority) }}
                                            </span>
                                        </td>

                                        {{-- Status toggle --}}
                                        <td class="px-4 py-4">
                                            <form method="POST" action="{{ route('announcements.toggle', $announcement) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" aria-label="Ubah status" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full transition-colors {{ $announcement->status === 'active' ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                                    <span class="pointer-events-none absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition {{ $announcement->status === 'active' ? 'translate-x-5' : '' }}"></span>
                                                </button>
                                            </form>
                                            <p class="mt-1 text-xs {{ $announcement->status === 'active' ? 'text-emerald-600' : 'text-gray-400' }}">
                                                {{ $announcement->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                            </p>
                                        </td>

                                        {{-- Dibaca --}}
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center gap-1.5 text-gray-600">
                                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                {{ number_format($announcement->read_count) }}
                                            </span>
                                        </td>

                                        {{-- Tanggal --}}
                                        <td class="px-4 py-4 text-gray-600">
                                            <span class="whitespace-nowrap">{{ $announcement->publication_date->translatedFormat('d M Y') }}</span>
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="px-4 py-4">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('announcements.show', $announcement) }}" title="Lihat"
                                                    class="rounded-lg p-2 text-gray-400 transition hover:bg-sky-50 hover:text-sky-600">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('announcements.edit', $announcement) }}" title="Edit"
                                                    class="rounded-lg p-2 text-gray-400 transition hover:bg-amber-50 hover:text-amber-600">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Hapus pengumuman ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Hapus"
                                                        class="rounded-lg p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-600">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center">
                                            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                            </svg>
                                            <p class="mt-3 font-semibold text-gray-700">Belum ada pengumuman</p>
                                            <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci pencarian atau buat pengumuman baru.</p>
                                            <a href="{{ route('announcements.create') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Buat Pengumuman Pertama
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($announcements->hasPages())
                        <div class="border-t border-gray-100 px-6 py-4">
                            {{ $announcements->links() }}
                        </div>
                    @endif
                </section>
            @else
                {{-- ============================================================
                    Tampilan untuk warga (filter kategori + kartu pengumuman)
                    ============================================================ --}}
                @php
                    $filters = ['' => 'Semua', 'mendesak' => 'Mendesak', 'penting' => 'Penting', 'biasa' => 'Biasa'];
                    $categoryStyles = [
                        'umum' => 'bg-slate-100 text-slate-600',
                        'kegiatan' => 'bg-sky-100 text-sky-700',
                        'kesehatan' => 'bg-emerald-100 text-emerald-700',
                        'keamanan' => 'bg-amber-100 text-amber-700',
                        'lingkungan' => 'bg-lime-100 text-lime-700',
                        'agenda' => 'bg-indigo-100 text-indigo-700',
                    ];
                    $priorityStyles = [
                        'biasa' => 'bg-slate-100 text-slate-600',
                        'penting' => 'bg-amber-100 text-amber-700',
                        'mendesak' => 'bg-red-100 text-red-700',
                    ];
                @endphp

                <div class="space-y-5">
                    {{-- Filter kategori pengumuman --}}
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($filters as $value => $label)
                            <a href="{{ route('announcements.index', array_merge(request()->except(['filter', 'page']), ['filter' => $value])) }}"
                                class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request('filter', '') === $value ? 'bg-brand-600 text-white shadow-sm' : 'bg-white text-ink-600 ring-1 ring-cream-300 hover:bg-cream-50' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    @forelse ($announcements as $announcement)
                        <a href="{{ route('announcements.show', $announcement) }}"
                           class="group relative flex flex-col gap-1.5 rounded-2xl border p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-6 {{ $announcement->is_read ? 'border-cream-200 bg-white' : 'border-brand-300 bg-brand-50/60' }}"
                           aria-label="Baca pengumuman: {{ $announcement->announcement_title }}">
                            {{-- Penanda belum dibaca --}}
                            @unless ($announcement->is_read)
                                <span class="absolute -top-2.5 right-5 inline-flex items-center gap-1.5 rounded-full bg-brand-600 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white shadow-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                    Belum dibaca
                                </span>
                            @endunless

                            {{-- Tanggal, kategori, prioritas --}}
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs">
                                <time datetime="{{ $announcement->publication_date->toDateString() }}" class="inline-flex items-center gap-1.5 font-medium text-ink-500">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $announcement->publication_date->translatedFormat('d F Y') }}
                                </time>
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 font-semibold {{ $priorityStyles[$announcement->priority] ?? $priorityStyles['biasa'] }}">
                                    @if ($announcement->priority === 'mendesak')
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    {{ ucfirst($announcement->priority) }}
                                </span>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 font-semibold {{ $categoryStyles[$announcement->category] ?? $categoryStyles['umum'] }}">
                                    {{ ucfirst($announcement->category) }}
                                </span>
                                @if ($announcement->is_pinned)
                                    <span class="inline-flex items-center gap-1 font-semibold text-brand-600">
                                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Disematkan
                                    </span>
                                @endif
                            </div>

                            {{-- Judul --}}
                            <h3 class="text-base font-bold leading-snug text-ink-800 transition group-hover:text-brand-700">
                                {{ $announcement->announcement_title }}
                            </h3>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white p-10 text-center shadow-sm">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <p class="mt-3 font-semibold text-gray-700">Belum ada pengumuman</p>
                            <p class="mt-1 text-sm text-gray-500">Tidak ada pengumuman yang cocok dengan filter ini.</p>
                        </div>
                    @endforelse

                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
