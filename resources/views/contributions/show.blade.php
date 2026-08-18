<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">Detail Pembayaran Iuran</h2>
    </x-slot>

    <div>
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            <article class="rounded-lg bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $contribution->payment_status === 'lunas' ? 'bg-green-100 text-green-800' : ($contribution->payment_status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($contribution->payment_status) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $contribution->created_at->translatedFormat('d F Y') }}</span>
                </div>

                <div class="mt-4">
                    <p class="text-sm text-gray-500">Warga</p>
                    <h1 class="text-lg font-bold text-gray-900">{{ $contribution->user?->name ?? 'Warga lama' }}</h1>

                    @if ($contribution->amount)
                        <div class="mt-3 rounded-md bg-gray-50 p-4">
                            <p class="text-sm text-gray-500">Jumlah Pembayaran</p>
                            <p class="text-lg font-extrabold text-gray-900">Rp {{ number_format((float) $contribution->amount, 0, ',', '.') }}</p>
                        </div>
                    @endif

                    @if ($contribution->payment_code)
                        <p class="mt-3 text-sm text-gray-500">Kode Pembayaran</p>
                        <p class="font-mono text-sm font-semibold text-gray-800">{{ $contribution->payment_code }}</p>
                    @endif

                    @if ($contribution->payment_method)
                        <p class="mt-2 text-sm text-gray-500">Metode Pembayaran</p>
                        <p class="text-sm font-medium text-gray-800 capitalize">{{ str_replace('_', ' ', $contribution->payment_method) }}</p>
                    @endif

                    @if ($contribution->paid_at)
                        <p class="mt-2 text-sm text-gray-500">Dibayar pada</p>
                        <p class="text-sm font-medium text-gray-800">{{ $contribution->paid_at->translatedFormat('d F Y H:i') }}</p>
                    @endif

                    @if ($contribution->proof_of_payment)
                        <p class="mt-3 text-sm text-gray-500">Bukti Pembayaran</p>
                        <p class="whitespace-pre-line text-gray-700 leading-relaxed">{{ $contribution->proof_of_payment }}</p>
                    @endif
                </div>

                @if (Auth::user()->isWarga() && $contribution->user_id === Auth::id() && $contribution->payment_status === 'pending')
                    <div class="mt-6 rounded-md border-2 border-dashed border-indigo-300 bg-indigo-50 p-6">
                        <h2 class="text-base font-semibold text-indigo-900">Selesaikan Pembayaran Online</h2>
                        <p class="mt-1 text-sm text-indigo-700">Simulasi pembayaran online. Klik tombol di bawah untuk menganggap pembayaran telah diterima.</p>
                        <form method="POST" action="{{ route('contributions.pay', $contribution) }}" class="mt-4">
                            @csrf
                            <x-primary-button type="submit">Bayar Sekarang (Simulasi)</x-primary-button>
                        </form>
                    </div>
                @endif

                @if (Auth::user()->isAdmin())
                    <div class="mt-6 flex items-center gap-4 border-t pt-5">
                        <a href="{{ route('contributions.edit', $contribution) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Edit Catatan</a>
                        <form method="POST" action="{{ route('contributions.destroy', $contribution) }}" onsubmit="return confirm('Hapus catatan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">Hapus Catatan</button>
                        </form>
                    </div>
                @endif
            </article>

            <a href="{{ route('contributions.index') }}" class="inline-block text-sm font-medium text-gray-600 hover:text-gray-900">← Kembali ke rekap iuran</a>
        </div>
    </div>
</x-app-layout>
