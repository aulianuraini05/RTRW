<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Produk Terjual</h2>
            <a href="{{ route('marketplaces.index') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Lihat Katalog</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Produk</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Pembeli</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Total</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($sales as $sale)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        @if ($sale->marketplace)
                                            <a href="{{ route('marketplaces.show', $sale->marketplace) }}" class="hover:text-indigo-600">{{ $sale->product_name }}</a>
                                        @else
                                            {{ $sale->product_name }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $sale->buyer?->name ?? 'Warga' }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">Rp {{ number_format((float) $sale->total_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sale->status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : ($sale->status === 'diproses' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $sale->created_at->translatedFormat('d M Y') }}</td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="{{ route('marketplace-purchases.status.update', $sale) }}" class="flex items-center gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="rounded-md border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="menunggu" @selected($sale->status === 'menunggu')>Menunggu</option>
                                                <option value="diproses" @selected($sale->status === 'diproses')>Diproses</option>
                                                <option value="selesai" @selected($sale->status === 'selesai')>Selesai</option>
                                            </select>
                                            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-600">
                                        Belum ada produk kamu yang terjual.
                                        <a href="{{ route('marketplaces.index') }}" class="mt-2 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-500">Lihat katalog →</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $sales->links() }}
        </div>
    </div>
</x-app-layout>