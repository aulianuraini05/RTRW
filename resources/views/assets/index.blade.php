<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">Daftar Aset Lingkungan</h2>
            @if (Auth::user()->isAdmin())
                <a href="{{ route('assets.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Tambah aset</a>
            @endif
        </div>
    </x-slot>

    <div>
        <div class="space-y-4">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            @if ($loans->isNotEmpty())
                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">
                        {{ Auth::user()->isAdmin() ? 'Daftar Pengajuan Peminjaman Aset (Warga)' : 'Riwayat Peminjaman Saya' }}
                    </h3>
                    <div class="space-y-3">
                        @foreach ($loans as $loan)
                            @php
                                $statusClasses = [
                                    'diajukan' => 'bg-blue-100 text-blue-700',
                                    'diproses' => 'bg-yellow-100 text-yellow-700',
                                    'disetujui' => 'bg-indigo-100 text-indigo-700',
                                    'dipinjam' => 'bg-purple-100 text-purple-700',
                                    'dikembalikan' => 'bg-green-100 text-green-700',
                                    'ditolak' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <div class="flex flex-wrap items-center justify-between gap-2 rounded-md border border-gray-200 p-4">
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-900">
                                        <a href="{{ route('assets.show', $loan->asset_id) }}" class="hover:text-indigo-600">
                                            {{ $loan->asset?->asset_name ?? 'Aset' }}
                                        </a>
                                        @if (Auth::user()->isAdmin())
                                            <span class="font-normal text-gray-600"> — dipinjam oleh {{ $loan->user?->name ?? 'Warga' }}</span>
                                        @endif
                                    </p>
                                    <p class="text-gray-600 mt-1">Jumlah: {{ $loan->quantity }} • Pinjam: {{ $loan->borrow_date?->translatedFormat('d F Y') }} • Kembali: {{ $loan->return_date?->translatedFormat('d F Y') ?? '-' }}</p>
                                    @if ($loan->notes)
                                        <p class="mt-1 text-gray-500">Catatan: {{ $loan->notes }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClasses[$loan->loan_status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($loan->loan_status) }}</span>
                                    @if (Auth::user()->isAdmin())
                                        @include('assets._loan-actions', ['loan' => $loan])
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @forelse ($assets as $asset)
                @php
                    $conditionClasses = [
                        'baik' => 'bg-green-100 text-green-700',
                        'rusak ringan' => 'bg-yellow-100 text-yellow-700',
                        'perlu perbaikan' => 'bg-orange-100 text-orange-700',
                        'rusak berat' => 'bg-red-100 text-red-700',
                    ];
                    $available = $asset->availableQuantity();
                @endphp
                <article class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                        <div class="flex-1">
                            <div class="mb-2 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                                <span>{{ $asset->asset_type }}</span>
                                <span>•</span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $conditionClasses[$asset->condition] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($asset->condition) }}</span>
                                <span>•</span>
                                <span>Tersedia: {{ $available }}/{{ $asset->quantity }}</span>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900"><a href="{{ route('assets.show', $asset) }}" class="hover:text-indigo-600">{{ $asset->asset_name }}</a></h3>
                            @if ($asset->description)
                                <p class="mt-2 text-gray-600">{{ Str::limit($asset->description, 180) }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-3 sm:flex-col sm:items-end">
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('assets.edit', $asset) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Edit</a>
                                <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Hapus aset ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-medium text-red-600 hover:text-red-800" type="submit">Hapus</button>
                                </form>
                            @endif
                            <a href="{{ route('assets.show', $asset) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat detail</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                    Belum ada aset yang terdaftar.
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('assets.create') }}" class="mt-4 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Tambah aset pertama</a>
                    @endif
                </div>
            @endforelse

            {{ $assets->links() }}
        </div>
    </div>
</x-app-layout>
