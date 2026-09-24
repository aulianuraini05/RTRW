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

    <div>
        <x-input-label for="rt_id" value="Kepemilikan / Lingkup Aset" />
        <select id="rt_id" name="rt_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            @php
                $user = Auth::user();
                $currentRtId = old('rt_id', isset($asset) ? ($asset->rt_id ?? 'rw') : ($user->rt_id ?? 'rw'));
                if (is_null($currentRtId)) { $currentRtId = 'rw'; }
                $userRtName = $user->rt?->name ?? 'RT Setempat';
            @endphp
            @if ($user->rt_id)
                <option value="{{ $user->rt_id }}" @selected((string)$currentRtId === (string)$user->rt_id)>
                    Aset {{ $userRtName }}
                </option>
                <option value="rw" @selected((string)$currentRtId === 'rw')>
                    Aset Umum RW
                </option>
            @else
                <option value="rw" @selected((string)$currentRtId === 'rw')>Aset Umum RW</option>
                @foreach (\App\Models\Rt::orderByRaw("CAST(substr(name, 4) AS INTEGER)")->get() as $rt)
                    <option value="{{ $rt->id }}" @selected((string)$currentRtId === (string)$rt->id)>Khusus {{ $rt->name }}</option>
                @endforeach
            @endif
        </select>
        <p class="mt-1 text-xs text-gray-500">Pilih apakah aset ini milik RT setempat atau Aset Umum RW.</p>
        <x-input-error class="mt-2" :messages="$errors->get('rt_id')" />
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
        <x-input-label for="description" value="Keterangan" />
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $asset->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div>
        <x-input-label for="image" value="Foto Aset (opsional)" />
        <input id="image" name="image" type="file" accept="image/png,image/jpeg,image/jpg,image/webp" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100" />
        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WEBP (maks. 10 MB). Biarkan kosong jika tidak mengunggah foto.</p>
        <x-input-error class="mt-2" :messages="$errors->get('image')" />

        @if (!empty($asset->image))
            <div class="mt-3 flex items-center gap-3">
                <img src="{{ Storage::url($asset->image) }}" alt="Foto Aset" class="h-20 w-20 rounded-lg object-cover border border-gray-200 shadow-sm" />
                <span class="text-xs text-gray-500">Foto saat ini</span>
            </div>
        @endif
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('assets.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</div>
