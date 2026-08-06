<div class="space-y-6">
    <div>
        <x-input-label for="user_id" value="Warga" />
        <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">-- Pilih warga --</option>
            @foreach ($warga as $user)
                <option value="{{ $user->id }}" @selected(old('user_id', $cashTransaction->user_id ?? '') == $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
    </div>

    <div>
        <x-input-label for="payment_status" value="Status Pembayaran" />
        <select id="payment_status" name="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="pending" @selected(old('payment_status', $cashTransaction->payment_status ?? '') === 'pending')>Pending (Belum Bayar)</option>
            <option value="lunas" @selected(old('payment_status', $cashTransaction->payment_status ?? '') === 'lunas')>Lunas</option>
            <option value="ditolak" @selected(old('payment_status', $cashTransaction->payment_status ?? '') === 'ditolak')>Ditolak</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('payment_status')" />
    </div>

    <div>
        <x-input-label for="proof_of_payment" value="Bukti Pembayaran (opsional)" />
        <x-text-input id="proof_of_payment" name="proof_of_payment" type="text" class="mt-1 block w-full" :value="old('proof_of_payment', $cashTransaction->proof_of_payment ?? '')" placeholder="Contoh: Transfer BCA a.n. Budi, No. 123456" />
        <x-input-error class="mt-2" :messages="$errors->get('proof_of_payment')" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('cash_transactions.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</div>
