<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-ink-800 sm:text-2xl">
            {{ __('Dashboard Smart RT/RW') }}
        </h2>
        <p class="mt-1 text-sm text-ink-500">
            {{ __('Halo, ') }}{{ Auth::user()->name }} — semoga harimu menyenangkan!
        </p>
    </x-slot>

    <div class="space-y-8">
        {{-- ============ 1. HERO / INFORMASI UTAMA ============ --}}
        <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-brand-600 via-brand-500 to-brand-400 text-white shadow-lg">
            <div class="flex flex-col gap-6 p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold text-brand-50">
                        {{ __('Tanggal hari ini: ') }}{{ now()->translatedFormat('l, d F Y') }}
                    </p>
                    <h1 class="mt-2 text-2xl font-bold leading-tight sm:text-3xl">
                        {{ __('Selamat datang di Smart RT/RW!') }}
                    </h1>
                    <p class="mt-3 text-sm leading-relaxed text-brand-50">
                        {{ __('Temukan informasi lingkungan, ajukan layanan, dan pantau iuran — semuanya di satu tempat yang mudah digunakan.') }}
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('announcements.index') }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-bold text-brand-700 shadow-sm transition hover:bg-brand-50">
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                            </svg>
                            {{ __('Lihat Pengumuman') }}
                        </a>

                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('announcements.create') }}"
                               class="inline-flex items-center gap-2 rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-bold text-white ring-1 ring-white/30 transition hover:bg-brand-700">
                                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('Buat Pengumuman') }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="hidden shrink-0 lg:block">
                    <svg class="h-32 w-32 text-white/20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                        <path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                    </svg>
                </div>
            </div>
        </section>

        {{-- ============ 2. LAYANAN WARGA (AKSI BESAR) ============ --}}
        <section>
            <h3 class="text-xl font-bold text-ink-800">{{ __('Layanan Warga') }}</h3>
            <p class="mt-1 text-sm text-ink-500">{{ __('Pilih layanan yang ingin Anda gunakan:') }}</p>

            <div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @php
                    $services = [
                        [
                            'route' => 'announcements.index',
                            'icon' => 'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46',
                            'title' => 'Pengumuman',
                            'desc' => 'Baca informasi dan kabar terbaru dari pengurus RT/RW.',
                            'tint' => 'bg-brand-50 text-brand-700',
                        ],
                        [
                            'route' => Auth::user()->isWarga() ? 'aspirations.create' : 'aspirations.index',
                            'icon' => 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z',
                            'title' => 'Aspirasi & Pengaduan',
                            'desc' => 'Sampaikan masukan atau keluhan agar ditindaklanjuti.',
                            'tint' => 'bg-amber-50 text-amber-700',
                        ],
                        [
                            'route' => Auth::user()->isWarga() ? 'letters.create' : 'letters.index',
                            'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75',
                            'title' => 'Persuratan',
                            'desc' => 'Ajukan surat keterangan secara mudah dan cepat.',
                            'tint' => 'bg-sky-50 text-sky-700',
                        ],
                        [
                            'route' => 'assets.index',
                            'icon' => 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9',
                            'title' => Auth::user()->isAdmin() ? 'Administrasi Aset' : 'Aset & Peminjaman',
                            'desc' => Auth::user()->isAdmin()
                                ? 'Kelola inventaris aset milik RT/RW.'
                                : 'Pinjam perlengkapan dan fasilitas lingkungan.',
                            'tint' => 'bg-violet-50 text-violet-700',
                        ],
                        [
                            'route' => 'cash_transactions.index',
                            'icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z',
                            'title' => 'Kas RT/RW',
                            'desc' => 'Lihat dan catat transaksi keuangan kas lingkungan.',
                            'tint' => 'bg-emerald-50 text-emerald-700',
                        ],
                        [
                            'route' => 'contributions.index',
                            'icon' => 'M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3',
                            'title' => 'Iuran Warga',
                            'desc' => 'Cek tagihan dan bayar iuran lingkungan dengan mudah.',
                            'tint' => 'bg-rose-50 text-rose-700',
                        ],
                        [
                            'route' => 'marketplaces.index',
                            'icon' => 'M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
                            'title' => 'Marketplace UMKM',
                            'desc' => 'Jelajahi produk usaha warga sekitar.',
                            'tint' => 'bg-orange-50 text-orange-700',
                        ],
                    ];
                @endphp

                @foreach ($services as $service)
                    <a href="{{ route($service['route']) }}"
                       class="card group flex flex-col h-full transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $service['tint'] }}">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $service['icon'] }}" />
                                </svg>
                            </div>
                            <h4 class="text-base font-bold text-ink-800 group-hover:text-brand-700">{{ $service['title'] }}</h4>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-ink-500">{{ $service['desc'] }}</p>
                        <span class="mt-auto pt-4 block text-center text-sm font-bold text-brand-600 group-hover:text-brand-700">
                            Buka
                        </span>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ============ 3. PRODUK UMKM (HORIZONTAL SCROLL) ============ --}}
        <section>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-xl font-bold text-ink-800">{{ __('Produk UMKM Terpopuler') }}</h3>
                    <p class="mt-1 text-sm text-ink-500">{{ __('Dukung usaha tetangga dengan berbelanja produk lokal warga.') }}</p>
                </div>
                <a href="{{ route('marketplaces.index') }}" class="hidden text-sm font-semibold text-brand-600 transition hover:text-brand-700 sm:block">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="hide-scrollbar mt-5 flex gap-5 overflow-x-auto scroll-smooth pb-4 snap-x snap-mandatory">
                @php
                    $products = \App\Models\Marketplace::available()->with('user')->latest()->take(10)->get();
                @endphp

                @forelse ($products as $product)
                    <a href="{{ route('marketplaces.show', $product) }}" class="group flex w-40 shrink-0 snap-start flex-col overflow-hidden rounded-xl border border-cream-200 bg-white shadow-sm cursor-pointer transition hover:-translate-y-1 hover:shadow-md">
                        <div class="h-32 w-full overflow-hidden bg-cream-200">
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->product_name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-50 to-brand-100 text-3xl">
                                    <span class="grayscale">🛍️</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-3">
                            <h4 class="line-clamp-1 text-sm font-bold text-ink-800 transition group-hover:text-brand-600">{{ $product->product_name }}</h4>
                            <p class="mt-0.5 line-clamp-1 text-xs text-ink-500">{{ $product->user?->name ?? 'Pedagang' }}</p>
                            <div class="mt-auto pt-2">
                                <span class="text-sm font-bold text-brand-700">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="w-full text-center text-sm text-ink-500 py-8">
                        Belum ada produk yang tersedia. <br>
                        <a href="{{ route('marketplaces.create') }}" class="mt-2 inline-block font-semibold text-brand-600 hover:text-brand-700">Daftarkan Produk Pertama</a>
                    </div>
                @endforelse
            </div>
            <a href="{{ route('marketplaces.index') }}" class="mt-3 block text-center text-sm font-semibold text-brand-600 transition hover:text-brand-700 sm:hidden">
                Lihat Semua &rarr;
            </a>
        </section>

        {{-- ============ 4. PANEL ADMIN (jika admin) ============ --}}
        @if (Auth::user()->isAdmin())
            <section>
                <h3 class="text-xl font-bold text-ink-800">{{ __('Panel Pengurus') }}</h3>
                <p class="mt-1 text-sm text-ink-500">{{ __('Tindakan cepat untuk mengelola lingkungan.') }}</p>

                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <div class="card">
                        <h4 class="text-base font-bold text-ink-800">{{ __('Informasi & Pengumuman') }}</h4>
                        <p class="mt-1.5 text-sm text-ink-500">{{ __('Sampaikan kabar terbaru ke seluruh warga.') }}</p>
                        <div class="mt-4 flex flex-wrap gap-2.5">
                            <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Buat Pengumuman
                            </a>
                            <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Kelola Pengumuman</a>
                        </div>
                    </div>

                    <div class="card">
                        <h4 class="text-base font-bold text-ink-800">{{ __('Layanan & Administrasi') }}</h4>
                        <p class="mt-1.5 text-sm text-ink-500">{{ __('Proses aspirasi, surat, dan administrasi lainnya.') }}</p>
                        <div class="mt-4 flex flex-wrap gap-2.5">
                            <a href="{{ route('aspirations.index') }}" class="btn btn-secondary">Aspirasi</a>
                            <a href="{{ route('letters.index') }}" class="btn btn-secondary">Persuratan</a>
                            <a href="{{ route('assets.index') }}" class="btn btn-secondary">Aset</a>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
</x-app-layout>