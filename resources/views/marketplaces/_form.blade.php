<div class="space-y-6">
    <div>
        <x-input-label for="product_name" value="Nama Produk" />
        <x-text-input id="product_name" name="product_name" type="text" class="mt-1 block w-full" :value="old('product_name', $marketplace->product_name ?? '')" placeholder="Contoh: Keripik Pisang" required />
        <x-input-error class="mt-2" :messages="$errors->get('product_name')" />
    </div>

    <div>
        <x-input-label for="description" value="Deskripsi Produk" />
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ceritakan produk Anda..." required>{{ old('description', $marketplace->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="price" value="Harga (Rp)" />
            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price', $marketplace->price ?? '')" placeholder="50000" required />
            <x-input-error class="mt-2" :messages="$errors->get('price')" />
        </div>
    </div>

    @if ($editMode ?? false)
        <div>
            <x-input-label for="product_status" value="Status Produk" />
            <select id="product_status" name="product_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="tersedia" @selected(old('product_status', $marketplace->product_status ?? '') === 'tersedia')>Tersedia</option>
                <option value="habis" @selected(old('product_status', $marketplace->product_status ?? '') === 'habis')>Habis</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('product_status')" />
        </div>
    @else
        <input type="hidden" name="product_status" value="tersedia">
    @endif

    <div>
        <x-input-label for="seller_phone" value="Nomor WhatsApp Penjual" />
        <x-text-input id="seller_phone" name="seller_phone" type="text" class="mt-1 block w-full" :value="old('seller_phone', $marketplace->seller_phone ?? '')" placeholder="Contoh: 0812-3456-7890" required />
        <p class="mt-1 text-xs text-gray-500">Digunakan untuk tombol "Beli via WhatsApp".</p>
        <x-input-error class="mt-2" :messages="$errors->get('seller_phone')" />
    </div>

    <div>
        <x-input-label for="image" value="Foto Produk" />
        <input
            id="image"
            name="image"
            type="file"
            accept="image/jpeg,image/png,image/jpg,image/webp"
            class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-500"
        />
        <p class="mt-1 text-xs text-gray-500">Format JPG, PNG, atau WEBP. Ukuran maksimal 10MB.</p>
        <x-input-error class="mt-2" :messages="$errors->get('image')" />

        @if (($marketplace->image ?? null) && Storage::disk('public')->exists($marketplace->image))
            <div class="mt-3">
                <p class="mb-2 text-xs font-medium text-gray-500">Foto saat ini:</p>
                <img src="{{ Storage::url($marketplace->image) }}" alt="{{ $marketplace->product_name }}" class="h-32 w-32 rounded-lg object-cover ring-1 ring-gray-200">
            </div>
        @endif
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('marketplaces.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</div>
