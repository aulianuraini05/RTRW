<div class="space-y-6">
    <div>
        <x-input-label for="asset_name" value="Nama aset" />
        <x-text-input id="asset_name" name="asset_name" type="text" class="mt-1 block w-full" :value="old('asset_name', $asset->asset_name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('asset_name')" />
    </div>

    <div>
        <x-input-label for="asset_type" value="Jenis aset" />
        <select id="asset_type" name="asset_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih jenis aset</option>
            @foreach (['Elektronik', 'Furniture', 'Kendaraan', 'Alat Kebersihan', 'Alat Olahraga', 'Bangunan', 'Lainnya'] as $type)
                <option value="{{ $type }}" @selected(old('asset_type', $asset->asset_type ?? '') === $type)>{{ $type }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('asset_type')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="quantity" value="Jumlah" />
            <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full" :value="old('quantity', $asset->quantity ?? 1)" required />
            <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
        </div>
        <div>
            <x-input-label for="condition" value="Kondisi" />
            <select id="condition" name="condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                @foreach (['baik' => 'Baik', 'rusak ringan' => 'Rusak Ringan', 'perlu perbaikan' => 'Perlu Perbaikan', 'rusak berat' => 'Rusak Berat'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('condition', $asset->condition ?? 'baik') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('condition')" />
        </div>
    </div>

    <div>
        <x-input-label for="description" value="Keterangan (opsional)" />
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $asset->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('assets.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</div>
