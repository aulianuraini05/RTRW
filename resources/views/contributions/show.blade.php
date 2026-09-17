<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">Detail Pembayaran Iuran</h2>
    </x-slot>

    <div>
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('info'))
                <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-700">{{ session('info') }}</div>
            @endif
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif
            <article class="rounded-lg bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $contribution->payment_status === 'lunas' ? 'bg-green-100 text-green-800' : ($contribution->payment_status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($contribution->payment_status) }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $contribution->created_at->translatedFormat('d F Y') }}</span>
                </div>

                <div class="mt-4">
                    <p class="text-sm text-gray-500">Pembayar</p>
                    <h1 class="text-lg font-bold text-gray-900">{{ $contribution->display_name }}</h1>
                    @if ($contribution->schedule)
                        <p class="mt-1 inline-block rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-700">Iuran {{ $contribution->schedule->jenis }} {{ $contribution->schedule->period_label }}</p>
                    @endif

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
                        $qrPayload = '00020101021226680016ID.CO.QRIS.WWW01189360091800000000005204581253033605406' . (int)$amount . '5802ID5915IURAN+' . str_replace(' ', '+', $rtName) . '+RW106007JAKARTA';
                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($qrPayload);
                    @endphp

                    <div class="mx-auto mt-6 max-w-md overflow-hidden rounded-2xl border border-red-200 bg-white shadow-md">
                        <div class="px-5 pb-5 pt-2 text-center">
                            @php
                                $activeMethod = in_array($contribution->payment_method, ['qris', 'virtual_account', 'transfer'])
                                    ? $contribution->payment_method
                                    : 'qris';
                            @endphp
                            <div id="preview-qris" @if ($activeMethod !== 'qris') class="hidden" @endif>
                                <div class="flex justify-center">
                                    <div class="w-56">
                                        <div class="rounded-xl border-2 border-red-500 bg-white p-2 shadow-sm">
                                            <img src="{{ $qrUrl }}" alt="QRIS Code Pembayaran Iuran" class="h-52 w-52 object-contain">
                                        </div>
                                        <div class="mt-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1.5 text-center text-xs font-bold leading-snug text-emerald-700">
                                            🔒 Nominal Terkunci: Rp {{ number_format($amount, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2 rounded-lg bg-gray-50 p-4 text-left text-xs text-gray-600 space-y-1.5 border border-gray-200">
                                    <p class="font-bold text-gray-800">Petunjuk Pembayaran QRIS:</p>
                                    <ol class="list-decimal list-inside space-y-1 text-gray-700">
                                        <li>Buka GoPay, OVO, ShopeePay, DANA, LinkAja, BCA Mobile, atau Bank Anda.</li>
                                        <li>Pilih menu <strong>Scan / QRIS</strong> dan arahkan kamera ke kode QR di atas.</li>
                                        <li>Nominal sebesar <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong> akan langsung terkunci secara otomatis.</li>
                                        <li>Periksa nama penerima <strong>IURAN {{ strtoupper($rtName) }}</strong>, lalu selesaikan transaksi dengan PIN Anda.</li>
                                    </ol>
                                </div>
                            </div>
                            <div id="preview-virtual_account" @if ($activeMethod !== 'virtual_account') class="hidden" @endif>
                                <div class="rounded-xl bg-blue-50 border border-blue-200 p-4">
                                    <p class="text-xs font-semibold text-blue-600 uppercase">Virtual Account (BCA / Mandiri)</p>
                                    <p class="mt-1 font-mono text-xl font-bold text-blue-900">88002{{ str_pad((string)$contribution->user_id, 4, '0', STR_PAD_LEFT) }}99</p>
                                    <p class="mt-1 text-xs text-blue-700">Nominal Transfer: <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong></p>
                                </div>

                                <div class="mt-2 rounded-lg bg-gray-50 p-4 text-left text-xs text-gray-600 space-y-1.5 border border-gray-200">
                                    <p class="font-bold text-gray-800">Petunjuk Pembayaran VA:</p>
                                    <ol class="list-decimal list-inside space-y-1 text-gray-700">
                                        <li>Salin nomor Virtual Account di atas.</li>
                                        <li>Transfer tepat <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong> via ATM / M-Banking / Internet Banking.</li>
                                        <li>Simpan bukti transfer, lalu tekan tombol bayar di bawah.</li>
                                    </ol>
                                </div>
                            </div>
                            <div id="preview-transfer" @if ($activeMethod !== 'transfer') class="hidden" @endif>
                                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4">
                                    <p class="text-xs font-semibold text-emerald-600 uppercase">Transfer Bank Manual</p>
                                    <p class="mt-1 text-sm font-bold text-emerald-900">BCA: 123-456-7890 (a.n. Bendahara {{ $rtName }})</p>
                                    <p class="mt-1 text-xs text-emerald-700">Nominal Transfer: <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong></p>
                                </div>

                                <div class="mt-2 rounded-lg bg-gray-50 p-4 text-left text-xs text-gray-600 space-y-1.5 border border-gray-200">
                                    <p class="font-bold text-gray-800">Petunjuk Transfer Bank:</p>
                                    <ol class="list-decimal list-inside space-y-1 text-gray-700">
                                        <li>Transfer tepat <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong> ke rekening di atas.</li>
                                        <li>Simpan bukti transfer, lalu tekan tombol bayar di bawah.</li>
                                    </ol>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('contributions.pay', $contribution) }}" class="mt-5 space-y-3">
                                @csrf
                                <div class="text-left">
                                    <x-input-label for="payment_method" value="Pilih Metode Pembayaran" />
                                    <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="qris" @selected(old('payment_method', $contribution->payment_method ?? 'qris') === 'qris')>QRIS</option>
                                        <option value="virtual_account" @selected(old('payment_method', $contribution->payment_method) === 'virtual_account')>Virtual Account</option>
                                        <option value="transfer" @selected(old('payment_method', $contribution->payment_method) === 'transfer')>Transfer Bank</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
                                </div>
                                <x-primary-button type="submit" class="w-full justify-center bg-red-600 py-3 hover:bg-red-700 font-bold text-sm">
                                    Bayar Sekarang
                                </x-primary-button>
                            </form>
                        </div>
                    </div>
                    <script>
                        (function () {
                            var select = document.getElementById('payment_method');
                            if (!select) return;
                            var previews = {
                                qris: document.getElementById('preview-qris'),
                                virtual_account: document.getElementById('preview-virtual_account'),
                                transfer: document.getElementById('preview-transfer'),
                            };
                            function syncPreview() {
                                Object.keys(previews).forEach(function (key) {
                                    if (previews[key]) previews[key].classList.toggle('hidden', key !== select.value);
                                });
                            }
                            select.addEventListener('change', syncPreview);
                        })();
                    </script>
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
