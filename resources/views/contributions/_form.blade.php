<div class="space-y-6">
    @if (Auth::user()->isWarga())
        <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-700">
            Ajukan pembayaran iuran Anda di sini. Setelah diajukan, Anda dapat menyelesaikan pembayaran online pada halaman detail untuk melunasi.
        </div>
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
        <input type="hidden" name="payment_status" value="pending">

        <div>
            <x-input-label for="amount" value="Jumlah Pembayaran (Rp)" />
            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('amount', $contribution->amount ?? '')" placeholder="Contoh: 50000" required />
            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
        </div>

        <div>
            <x-input-label for="payment_method" value="Metode Pembayaran Online" />
            <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="">-- Pilih metode --</option>
                <option value="virtual_account" @selected(old('payment_method', $contribution->payment_method ?? '') === 'virtual_account')>Virtual Account</option>
                <option value="qris" @selected(old('payment_method', $contribution->payment_method ?? '') === 'qris')>QRIS</option>
                <option value="transfer" @selected(old('payment_method', $contribution->payment_method ?? '') === 'transfer')>Transfer Bank</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
        </div>
    @else
        <div>
            <x-input-label for="user_id" value="Warga" />
            <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="">-- Pilih warga --</option>
                @foreach ($warga as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id', $contribution->user_id ?? '') == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
        </div>

        <div>
            <x-input-label for="payment_status" value="Status Pembayaran" />
            <select id="payment_status" name="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="pending" @selected(old('payment_status', $contribution->payment_status ?? '') === 'pending')>Pending (Belum Bayar)</option>
                <option value="lunas" @selected(old('payment_status', $contribution->payment_status ?? '') === 'lunas')>Lunas</option>
                <option value="ditolak" @selected(old('payment_status', $contribution->payment_status ?? '') === 'ditolak')>Ditolak</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('payment_status')" />
        </div>
    @endif

    <div>
        <x-input-label for="proof_of_payment" value="Bukti Pembayaran (opsional)" />
        <x-text-input id="proof_of_payment" name="proof_of_payment" type="text" class="mt-1 block w-full" :value="old('proof_of_payment', $contribution->proof_of_payment ?? '')" placeholder="Contoh: Transfer BCA a.n. Budi, No. 123456" />
        <x-input-error class="mt-2" :messages="$errors->get('proof_of_payment')" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('contributions.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</div>
