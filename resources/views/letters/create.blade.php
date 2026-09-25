<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-lg text-gray-800 leading-tight">Ajukan permohonan surat</h2></x-slot>

    <div>
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('letters.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-lg bg-white p-6 shadow-sm">
                @csrf
                <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-700">
                    Nomor surat akan dibuat otomatis oleh sistem setelah permohonan dikirim.
                    Tiap jenis surat butuh lampiran yang berbeda — form lampiran muncul otomatis setelah jenis surat dipilih.
                </div>
                <div>
                    <x-input-label for="letter_type" value="Jenis surat" />
                    <select id="letter_type" name="letter_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Pilih jenis surat</option>
                        @foreach (array_keys($requirements) as $type)
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

                <div id="attachments-wrapper" class="space-y-4 rounded-md border border-dashed border-gray-300 p-4">
                    <p class="text-sm font-medium text-gray-700">Lampiran syarat</p>
                    <p id="attachments-hint" class="text-sm text-gray-500">Pilih jenis surat dulu untuk melihat syarat lampirannya.</p>
                    <div id="attachments-fields" class="space-y-4"></div>
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

    <script>
        const REQUIREMENTS = @json($requirements);
        const typeSelect = document.getElementById('letter_type');
        const fieldsBox = document.getElementById('attachments-fields');
        const hint = document.getElementById('attachments-hint');
        const oldType = @json(old('letter_type'));

        function renderAttachments(type) {
            fieldsBox.innerHTML = '';
            const reqs = REQUIREMENTS[type] || [];
            if (reqs.length === 0) {
                hint.textContent = 'Pilih jenis surat dulu untuk melihat syarat lampirannya.';
                hint.style.display = '';
                return;
            }
            hint.style.display = 'none';
            reqs.forEach((req) => {
                const div = document.createElement('div');
                div.innerHTML = `
                    <label class="block text-sm font-medium text-gray-700">${req.label} <span class="text-red-600">*</span></label>
                    <input type="file" name="attachments[${req.key}]" accept=".jpg,.jpeg,.png,.webp,.pdf" required
                        class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100" />
                    <p class="mt-1 text-xs text-gray-500">Format JPG/PNG/WEBP/PDF, maks 10 MB.</p>
                `;
                fieldsBox.appendChild(div);
            });
        }

        typeSelect.addEventListener('change', (e) => renderAttachments(e.target.value));
        if (oldType) renderAttachments(oldType);
    </script>
</x-app-layout>
