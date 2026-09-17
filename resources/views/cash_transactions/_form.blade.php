<div class="space-y-6">
    @if (Auth::user()->isWarga())
        <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-700">
            Ajukan pembayaran kas Anda di sini. Setelah diajukan, Anda dapat menyelesaikan pembayaran online pada halaman detail untuk melunasi.
        </div>
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
        <input type="hidden" name="payment_status" value="pending">

        @if (! empty($schedule))
            <div class="rounded-md bg-gray-50 p-4">
                <p class="text-sm text-gray-500">Nominal Kas {{ $schedule->period_label }} (ditentukan Ketua RT)</p>
                <p class="text-lg font-extrabold text-gray-900">Rp {{ number_format((float) $schedule->amount, 0, ',', '.') }}</p>
            </div>
            <p class="text-xs text-gray-500">Nominal sudah dikunci — Anda tinggal memilih metode pembayaran di bawah ini.</p>
        @else
            <div>
                <x-input-label for="amount" value="Jumlah Pembayaran (Rp)" />
                <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('amount', $cashTransaction->amount ?? '')" placeholder="Contoh: 50000" required />
                <x-input-error class="mt-2" :messages="$errors->get('amount')" />
            </div>
        @endif

        <div>
            <x-input-label for="payment_method" value="Metode Pembayaran Online" />
            <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="">-- Pilih metode --</option>
                <option value="virtual_account" @selected(old('payment_method', $cashTransaction->payment_method ?? '') === 'virtual_account')>Virtual Account</option>
                <option value="qris" @selected(old('payment_method', $cashTransaction->payment_method ?? '') === 'qris')>QRIS</option>
                <option value="transfer" @selected(old('payment_method', $cashTransaction->payment_method ?? '') === 'transfer')>Transfer Bank</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
        </div>
    @else
        <div>
            <x-input-label for="payer_name" value="Nama Pembayar" />
            <x-text-input id="payer_name" name="payer_name" type="text" class="mt-1 block w-full" :value="old('payer_name', $cashTransaction->payer_name ?? $cashTransaction->user?->name ?? '')" maxlength="100" />
            <p class="mt-1 text-xs text-gray-500">Isi nama pembayar secara manual.</p>
            <x-input-error class="mt-2" :messages="$errors->get('payer_name')" />
        </div>

        <div>
            <x-input-label for="amount" value="Jumlah Pembayaran (Rp)" />
            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('amount', $cashTransaction->amount ?? 50000)" placeholder="Contoh: 50000" required />
            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
        </div>

        <div>
            <x-input-label for="payment_method" value="Metode Pembayaran" />
            <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="cash" @selected(old('payment_method', $cashTransaction->payment_method ?? 'cash') === 'cash')>💵 Tunai</option>
                <option value="qris" @selected(old('payment_method', $cashTransaction->payment_method ?? '') === 'qris')>📲 QRIS</option>
                <option value="virtual_account" @selected(old('payment_method', $cashTransaction->payment_method ?? '') === 'virtual_account')>🏦 Virtual Account</option>
                <option value="transfer" @selected(old('payment_method', $cashTransaction->payment_method ?? '') === 'transfer')>💳 Transfer Bank</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
        </div>

        <div>
            <x-input-label for="payment_status" value="Status Pembayaran" />
            <select id="payment_status" name="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="lunas" @selected(old('payment_status', $cashTransaction->payment_status ?? 'lunas') === 'lunas')>✅ Lunas</option>
                <option value="pending" @selected(old('payment_status', $cashTransaction->payment_status ?? '') === 'pending')>⏳ Pending</option>
                <option value="ditolak" @selected(old('payment_status', $cashTransaction->payment_status ?? '') === 'ditolak')>❌ Ditolak</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('payment_status')" />
        </div>
    @endif

    @if (Auth::user()->isWarga())
    <div>
        <x-input-label for="proof_of_payment" value="Bukti Pembayaran (opsional)" />
        <x-text-input id="proof_of_payment" name="proof_of_payment" type="text" class="mt-1 block w-full" :value="old('proof_of_payment', $cashTransaction->proof_of_payment ?? '')" placeholder="Contoh: Transfer BCA a.n. Budi, No. 123456" />
        <x-input-error class="mt-2" :messages="$errors->get('proof_of_payment')" />
    </div>
    @endif

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('cash_transactions.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</div>
