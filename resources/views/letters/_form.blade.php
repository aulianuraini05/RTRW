<div class="space-y-6">
    <div>
        <x-input-label for="letter_type" value="Jenis surat" />
        <select id="letter_type" name="letter_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih jenis surat</option>
            @foreach (['Surat Keterangan Domisili', 'Surat Pengantar KTP', 'Surat Pengantar KK', 'Surat Keterangan Usaha', 'Surat Keterangan Tidak Mampu', 'Surat Pengantar Nikah', 'Surat Keterangan Kematian', 'Lainnya'] as $type)
                <option value="{{ $type }}" @selected(old('letter_type', $letter->letter_type ?? '') === $type)>{{ $type }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('letter_type')" />
    </div>

    <div>
        <x-input-label for="purpose" value="Keperluan / Keterangan" />
        <textarea id="purpose" name="purpose" rows="7" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('purpose', $letter->purpose ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('purpose')" />
    </div>

    <div>
        <x-input-label for="submission_date" value="Tanggal pengajuan" />
        <x-text-input id="submission_date" name="submission_date" type="date" class="mt-1 block w-full" :value="old('submission_date', isset($letter) ? $letter->submission_date->toDateString() : now()->toDateString())" required />
        <x-input-error class="mt-2" :messages="$errors->get('submission_date')" />
    </div>

    <div>
        <x-input-label for="letter_status" value="Status" />
        <select id="letter_status" name="letter_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach (['diajukan' => 'Diajukan', 'diproses' => 'Diproses', 'disetujui' => 'Disetujui', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $status => $label)
                <option value="{{ $status }}" @selected(old('letter_status', $letter->letter_status ?? 'diajukan') === $status)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('letter_status')" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('letters.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</div>
