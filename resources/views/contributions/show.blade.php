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
                    @php
                        $amount = (float) $contribution->amount;
                        $code = $contribution->payment_code ?? 'IURAN-001';
                        $rtName = Auth::user()->rt?->name ?? 'RT 01';
                        $qrPayload = '00020101021226680016ID.CO.QRIS.WWW01189360091800000000005204581253033605406' . (int)$amount . '5802ID5915IURAN+' . str_replace(' ', '+', $rtName) . '+RW056007JAKARTA';
                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($qrPayload);
                    @endphp

                    <div class="mt-6 rounded-2xl border border-red-200 bg-white overflow-hidden shadow-md">
                        <div class="bg-gradient-to-r from-red-700 via-red-600 to-red-500 px-6 py-4 text-white flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-lg tracking-wider">QRIS</span>
                                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded font-mono">NATIONAL QR CODE</span>
                                </div>
                                <p class="text-xs text-red-100 mt-0.5">Satu QR untuk Semua Pembayaran E-Wallet & M-Banking</p>
                            </div>
                            <span class="text-xs font-semibold bg-red-900/60 border border-white/20 px-3 py-1 rounded-full">
                                {{ $rtName }} / RW 05
                            </span>
                        </div>

                        <div class="p-6 text-center">
                            @if ($contribution->payment_method === 'qris' || empty($contribution->payment_method))
                                <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Merchant: IURAN {{ strtoupper($rtName) }} RW 05</p>
                                
                                <div class="my-4 inline-block rounded-xl border-2 border-red-500 p-3 bg-white shadow-sm">
                                    <img src="{{ $qrUrl }}" alt="QRIS Code Pembayaran Iuran" class="mx-auto h-48 w-48 object-contain">
                                </div>

                                <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200 shadow-sm">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    🔒 Nominal Otomatis Terkunci: Rp {{ number_format($amount, 0, ',', '.') }}
                                </div>
                            @elseif ($contribution->payment_method === 'virtual_account')
                                <div class="my-2 rounded-xl bg-blue-50 border border-blue-200 p-4 text-left">
                                    <p class="text-xs font-semibold text-blue-600 uppercase">Virtual Account (BCA / Mandiri)</p>
                                    <p class="mt-1 font-mono text-xl font-bold text-blue-900">88002{{ str_pad((string)$contribution->user_id, 4, '0', STR_PAD_LEFT) }}99</p>
                                    <p class="mt-1 text-xs text-blue-700">Nominal Transfer: <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong></p>
                                </div>
                            @else
                                <div class="my-2 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-left">
                                    <p class="text-xs font-semibold text-emerald-600 uppercase">Transfer Bank Manual</p>
                                    <p class="mt-1 text-sm font-bold text-emerald-900">BCA: 123-456-7890 (a.n. Bendahara {{ $rtName }})</p>
                                    <p class="mt-1 text-xs text-emerald-700">Nominal Transfer: <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong></p>
                                </div>
                            @endif

                            <div class="mt-4 rounded-lg bg-gray-50 p-4 text-left text-xs text-gray-600 space-y-1.5 border border-gray-200">
                                <p class="font-bold text-gray-800">Petunjuk Pembayaran QRIS:</p>
                                <ol class="list-decimal list-inside space-y-1 text-gray-700">
                                    <li>Buka GoPay, OVO, ShopeePay, DANA, LinkAja, BCA Mobile, atau Bank Anda.</li>
                                    <li>Pilih menu <strong>Scan / QRIS</strong> dan arahkan kamera ke kode QR di atas.</li>
                                    <li>Nominal sebesar <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong> akan langsung terkunci secara otomatis.</li>
                                    <li>Periksa nama penerima <strong>IURAN {{ strtoupper($rtName) }}</strong>, lalu selesaikan transaksi dengan PIN Anda.</li>
                                </ol>
                            </div>

                            <form method="POST" action="{{ route('contributions.pay', $contribution) }}" class="mt-5">
                                @csrf
                                <x-primary-button type="submit" class="w-full justify-center bg-red-600 py-3 hover:bg-red-700 font-bold text-sm">
                                    Simulasi Bayar QRIS Sekarang (Lunas)
                                </x-primary-button>
                            </form>
                        </div>
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

            <a href="{{ route('contributions.index') }}" class="inline-block text-sm font-medium text-gray-600 hover:text-gray-900">Kembali ke rekap iuran</a>
        </div>
    </div>
</x-app-layout>
