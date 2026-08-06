<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail aset</h2></x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <article class="rounded-lg bg-white p-6 shadow-sm sm:p-8">
                @php
                    $conditionClasses = [
                        'baik' => 'bg-green-100 text-green-700',
                        'rusak ringan' => 'bg-yellow-100 text-yellow-700',
                        'perlu perbaikan' => 'bg-orange-100 text-orange-700',
                        'rusak berat' => 'bg-red-100 text-red-700',
                    ];
                    $available = $asset->availableQuantity();
                @endphp
                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                    <span>{{ $asset->asset_type }}</span>
                    <span>•</span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $conditionClasses[$asset->condition] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($asset->condition) }}</span>
                    <span>•</span>
                    <span>Tersedia: {{ $available }}/{{ $asset->quantity }}</span>
                </div>
                <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $asset->asset_name }}</h1>
                @if ($asset->description)
                    <div class="mt-6 whitespace-pre-line leading-7 text-gray-700">{{ $asset->description }}</div>
                @endif

                @if (Auth::user()->isAdmin())
                    <div class="mt-8 flex items-center gap-4 border-t pt-6">
                        <a href="{{ route('assets.edit', $asset) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Edit aset</a>
                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('Hapus aset ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm font-medium text-red-600 hover:text-red-800" type="submit">Hapus</button>
                        </form>
                    </div>
                @endif
            </article>

            @if (Auth::user()->isWarga() && $available > 0)
                <section class="rounded-lg bg-white p-6 shadow-sm sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900">Ajukan peminjaman</h3>
                    @include('assets._loan-form', ['asset' => $asset])
                </section>
            @elseif (Auth::user()->isWarga() && $available <= 0)
                <section class="rounded-lg bg-yellow-50 p-6 text-sm text-yellow-700 shadow-sm">
                    Aset ini sedang tidak tersedia untuk dipinjam.
                </section>
            @endif

            @if ($asset->loans->isNotEmpty())
                <section class="rounded-lg bg-white p-6 shadow-sm sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-900">{{ Auth::user()->isAdmin() ? 'Riwayat peminjaman' : 'Peminjaman saya' }}</h3>
                    <div class="mt-4 space-y-3">
                        @foreach ($asset->loans as $loan)
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
                                    @if (Auth::user()->isAdmin())
                                        <p class="font-medium text-gray-900">{{ $loan->user?->name ?? 'Warga lama' }}</p>
                                    @endif
                                    <p class="text-gray-600">Jumlah: {{ $loan->quantity }} • Pinjam: {{ $loan->borrow_date->translatedFormat('d F Y') }} • Kembali: {{ $loan->return_date?->translatedFormat('d F Y') ?? '-' }}</p>
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

            <a href="{{ route('assets.index') }}" class="inline-block text-sm font-medium text-gray-600 hover:text-gray-900">← Kembali ke daftar</a>
        </div>
    </div>
</x-app-layout>
