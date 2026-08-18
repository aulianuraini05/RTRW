<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">Marketplace UMKM</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('marketplaces.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">+ Daftarkan Produk</a>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <!-- Search Bar -->
            <div class="rounded-lg bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('marketplaces.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <x-text-input type="text" name="search" placeholder="Cari produk..." :value="$search" class="w-full sm:max-w-xs" />
                    <x-primary-button type="submit">Cari</x-primary-button>
                    @if ($search)
                        <a href="{{ route('marketplaces.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Product Grid -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($marketplaces as $marketplace)
                    <article class="group flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200 transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                        <a href="{{ route('marketplaces.show', $marketplace) }}" class="relative block overflow-hidden">
                            @if ($marketplace->image)
                                <img src="{{ Storage::url($marketplace->image) }}" alt="{{ $marketplace->product_name }}" class="aspect-[4/3] w-full object-cover object-center transition duration-300 group-hover:scale-105">
                            @else
                                <div class="flex aspect-[4/3] w-full items-center justify-center bg-gradient-to-br from-indigo-50 to-purple-50 text-5xl">
                                    <span class="grayscale">🛍️</span>
                                </div>
                            @endif
                            <span class="absolute left-3 top-3 rounded-full px-2.5 py-0.5 text-xs font-semibold shadow-sm {{ $marketplace->product_status === 'tersedia' ? 'bg-emerald-500 text-white' : 'bg-gray-400 text-white' }}">
                                {{ $marketplace->product_status === 'tersedia' ? 'Tersedia' : 'Habis' }}
                            </span>
                        </a>
                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex items-center gap-1 text-xs text-gray-400">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $marketplace->user?->name ?? 'Pedagang lama' }}
                            </div>
                            <h3 class="mt-2 text-base font-semibold leading-snug text-gray-900">
                                <a href="{{ route('marketplaces.show', $marketplace) }}" class="transition-colors hover:text-indigo-600">{{ $marketplace->product_name }}</a>
                            </h3>
                            <p class="mt-1 line-clamp-2 flex-1 text-sm text-gray-500">{{ $marketplace->description }}</p>
                            <div class="mt-4 flex items-end justify-between gap-2">
                                <p class="text-base font-bold text-indigo-600">Rp {{ number_format((float) $marketplace->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="mt-4 flex items-center gap-2">
                                <a href="{{ route('marketplaces.show', $marketplace) }}" class="flex-1 text-center rounded-lg border border-indigo-600 px-4 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50">Lihat Detail</a>
                                @if ($marketplace->product_status === 'tersedia')
                                    @php($waLink = $marketplace->whatsappLink($marketplace->whatsappMessage()))
                                    @if ($waLink)
                                        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-500">Beli via WA</a>
                                    @endif
                                @else
                                    <span class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-400">Tidak Tersedia</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                        @if ($search)
                            Tidak ada produk yang cocok dengan pencarian.
                        @else
                            Belum ada produk terdaftar di marketplace.
                            <a href="{{ route('marketplaces.create') }}" class="mt-4 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Daftarkan Produk Pertama</a>
                        @endif
                    </div>
                @endforelse
            </div>

            {{ $marketplaces->links() }}
        </div>
    </div>
</x-app-layout>
