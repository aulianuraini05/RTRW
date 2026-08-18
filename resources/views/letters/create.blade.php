<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-lg text-gray-800 leading-tight">Ajukan permohonan surat</h2></x-slot>

    <div>
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('letters.store') }}" class="space-y-6 rounded-lg bg-white p-6 shadow-sm">
                @csrf
                <div>
                    <x-input-label for="letter_number" value="Nomor surat (opsional)" />
                    <x-text-input id="letter_number" name="letter_number" type="text" class="mt-1 block w-full" :value="old('letter_number')" placeholder="Kosongkan untuk digenerate otomatis" />
                    <x-input-error class="mt-2" :messages="$errors->get('letter_number')" />
                </div>
                <div>
                    <x-input-label for="letter_type" value="Jenis surat" />
                    <select id="letter_type" name="letter_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Pilih jenis surat</option>
                        @foreach (['Surat Keterangan Domisili', 'Surat Pengantar KTP', 'Surat Pengantar KK', 'Surat Keterangan Usaha', 'Surat Keterangan Tidak Mampu', 'Surat Pengantar Nikah', 'Surat Keterangan Kematian', 'Lainnya'] as $type)
                            <option value="{{ $type }}" @selected(old('letter_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('letter_type')" />
                </div>
                <div>
                    <x-input-label for="purpose" value="Keperluan / Keterangan" />
                    <textarea id="purpose" name="purpose" rows="7" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="Jelaskan keperluan permohonan surat ini...">{{ old('purpose') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('purpose')" />
                </div>
                <div>
                    <x-input-label for="submission_date" value="Tanggal pengajuan" />
                    <x-text-input id="submission_date" name="submission_date" type="date" class="mt-1 block w-full" :value="old('submission_date', now()->toDateString())" required />
                    <x-input-error class="mt-2" :messages="$errors->get('submission_date')" />
                </div>
                <div>
                    <x-input-label for="letter_status" value="Status" />
                    <select id="letter_status" name="letter_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required disabled>
                        <option value="diajukan" selected>Diajukan</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('letter_status')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>Kirim permohonan</x-primary-button>
                    <a href="{{ route('letters.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
