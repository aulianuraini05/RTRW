<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Produk</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <article class="overflow-hidden rounded-lg bg-white shadow-sm">
                @if ($marketplace->image)
                    <img src="{{ Storage::url($marketplace->image) }}" alt="{{ $marketplace->product_name }}" class="h-64 w-full object-cover">
                @else
                    <div class="flex h-64 w-full items-center justify-center bg-gray-100 text-6xl">🛍️</div>
                @endif
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $marketplace->product_status === 'tersedia' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                            {{ ucfirst($marketplace->product_status) }}
                        </span>
                        <span class="text-sm text-gray-500">Terdaftar {{ $marketplace->created_at->translatedFormat('d F Y') }}</span>
                    </div>

                    <h1 class="mt-4 text-2xl font-bold text-gray-900">{{ $marketplace->product_name }}</h1>
                    <p class="mt-2 text-lg font-bold text-indigo-600">Rp {{ number_format((float) $marketplace->price, 0, ',', '.') }}</p>
                    <p class="mt-4 whitespace-pre-line text-gray-700 leading-relaxed">{{ $marketplace->description }}</p>

                    <dl class="mt-6 grid gap-4 rounded-md bg-gray-50 p-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-gray-500">Penjual</dt>
                            <dd class="font-medium text-gray-900">{{ $marketplace->user?->name ?? 'Pedagang lama' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Stok</dt>
                            <dd class="font-medium text-gray-900">{{ $marketplace->stock }} {{ $marketplace->stock === 1 ? 'buah' : 'buah' }}</dd>
                        </div>
                        @if ($marketplace->seller_phone)
                            <div>
                                <dt class="text-gray-500">Kontak Penjual</dt>
                                <dd class="font-medium text-gray-900">{{ $marketplace->seller_phone }}</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-8 flex flex-wrap items-center gap-3 border-t pt-6">
                        @if ($marketplace->product_status === 'tersedia' && $marketplace->stock > 0)
                            <form method="POST" action="{{ route('marketplaces.buy', $marketplace) }}">
                                @csrf
                                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Beli Produk</button>
                            </form>
                        @else
                            <span class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-600">Produk habis</span>
                        @endif

                        @if (Auth::user()->isAdmin() || Auth::id() === $marketplace->user_id)
                            <a href="{{ route('marketplaces.edit', $marketplace) }}" class="rounded-md border border-indigo-600 px-4 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">Edit Produk</a>
                        @endif

                        @if (Auth::user()->isAdmin())
                            <form method="POST" action="{{ route('marketplaces.destroy', $marketplace) }}" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">Hapus Produk</button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>

            <a href="{{ route('marketplaces.index') }}" class="inline-block text-sm font-medium text-gray-600 hover:text-gray-900">← Kembali ke katalog</a>
        </div>
    </div>
</x-app-layout>
