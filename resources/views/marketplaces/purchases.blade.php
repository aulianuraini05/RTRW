<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pembelian Saya</h2>
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
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Penjual</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Harga</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Total</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($purchases as $purchase)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        @if ($purchase->marketplace)
                                            <a href="{{ route('marketplaces.show', $purchase->marketplace) }}" class="hover:text-indigo-600">{{ $purchase->product_name }}</a>
                                        @else
                                            {{ $purchase->product_name }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $purchase->seller?->name ?? 'Pedagang lama' }}</td>
                                    <td class="px-6 py-4 text-gray-600">Rp {{ number_format((float) $purchase->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">Rp {{ number_format((float) $purchase->total_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $purchase->status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : ($purchase->status === 'diproses' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $purchase->created_at->translatedFormat('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-600">
                                        Belum ada transaksi pembelian dari kamu.
                                        <a href="{{ route('marketplaces.index') }}" class="mt-2 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-500">Cari produk di katalog →</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $purchases->links() }}
        </div>
    </div>
</x-app-layout>