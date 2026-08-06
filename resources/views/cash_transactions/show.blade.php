<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Pembayaran Kas</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            <article class="rounded-lg bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $cashTransaction->payment_status === 'lunas' ? 'bg-green-100 text-green-800' : ($cashTransaction->payment_status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($cashTransaction->payment_status) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $cashTransaction->created_at->translatedFormat('d F Y') }}</span>
                </div>

                <div class="mt-4">
                    <p class="text-sm text-gray-500">Warga</p>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $cashTransaction->user?->name ?? 'Warga lama' }}</h1>
                    @if ($cashTransaction->proof_of_payment)
                        <p class="mt-4 text-sm text-gray-500">Bukti Pembayaran</p>
                        <p class="whitespace-pre-line text-gray-700 leading-relaxed">{{ $cashTransaction->proof_of_payment }}</p>
                    @endif
                </div>

                @if (Auth::user()->isAdmin())
                    <div class="mt-8 flex items-center gap-4 border-t pt-6">
                        <a href="{{ route('cash_transactions.edit', $cashTransaction) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Edit Catatan</a>
                        <form method="POST" action="{{ route('cash_transactions.destroy', $cashTransaction) }}" onsubmit="return confirm('Hapus catatan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">Hapus Catatan</button>
                        </form>
                    </div>
                @endif
            </article>

            <a href="{{ route('cash_transactions.index') }}" class="inline-block text-sm font-medium text-gray-600 hover:text-gray-900">← Kembali ke rekap kas</a>
        </div>
    </div>
</x-app-layout>
