<form method="POST" action="{{ route('asset-loans.store', $asset) }}" class="mt-4 space-y-4">
    @csrf
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <x-input-label for="quantity" value="Jumlah" />
            <x-text-input id="quantity" name="quantity" type="number" min="1" max="{{ $asset->availableQuantity() }}" class="mt-1 block w-full" :value="old('quantity', 1)" required />
            <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
        </div>
        <div>
            <x-input-label for="borrow_date" value="Tanggal pinjam" />
            <x-text-input id="borrow_date" name="borrow_date" type="date" class="mt-1 block w-full" :value="old('borrow_date', now()->toDateString())" required />
            <x-input-error class="mt-2" :messages="$errors->get('borrow_date')" />
        </div>
        <div>
            <x-input-label for="return_date" value="Tanggal kembali" />
            <x-text-input id="return_date" name="return_date" type="date" class="mt-1 block w-full" :value="old('return_date', now()->addDay()->toDateString())" required />
            <x-input-error class="mt-2" :messages="$errors->get('return_date')" />
        </div>
    </div>
    <div>
        <x-input-label for="notes" value="Catatan (opsional)" />
        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
    </div>
    <div class="flex items-center gap-4">
        <x-primary-button>Ajukan peminjaman</x-primary-button>
    </div>
</form>
