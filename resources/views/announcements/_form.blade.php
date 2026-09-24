<div class="space-y-6">
    <div>
        <x-input-label for="announcement_title" value="Judul pengumuman" />
        <x-text-input id="announcement_title" name="announcement_title" type="text" class="mt-1 block w-full" :value="old('announcement_title', $announcement->announcement_title ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('announcement_title')" />
    </div>

    <div>
        <x-input-label for="announcement_content" value="Isi pengumuman" />
        <textarea id="announcement_content" name="announcement_content" rows="7" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('announcement_content', $announcement->announcement_content ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('announcement_content')" />
    </div>

    <div>
        <x-input-label for="image" value="Foto Pengumuman (opsional)" />
        <input
            id="image"
            name="image"
            type="file"
            accept="image/jpeg,image/png,image/jpg,image/webp"
            class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-500"
        />
        <p class="mt-1 text-xs text-gray-500">Format JPG, PNG, atau WEBP. Ukuran maksimal 10MB.</p>
        <x-input-error class="mt-2" :messages="$errors->get('image')" />

        @if (($announcement->image ?? null) && \Illuminate\Support\Facades\Storage::disk('public')->exists($announcement->image))
            <div class="mt-3">
                <p class="mb-2 text-xs font-medium text-gray-500">Foto saat ini:</p>
                <img src="{{ Storage::url($announcement->image) }}" alt="{{ $announcement->announcement_title }}" class="h-24 w-24 rounded-xl object-cover ring-1 ring-gray-200">
            </div>
        @endif
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="category" value="Kategori" />
            <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                @foreach (['umum', 'kegiatan', 'kesehatan', 'keamanan', 'lingkungan', 'agenda'] as $category)
                    <option value="{{ $category }}" @selected(old('category', $announcement->category ?? 'umum') === $category)>{{ ucfirst($category) }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('category')" />
        </div>
        <div>
            <x-input-label for="priority" value="Prioritas" />
            <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                @foreach (['biasa' => 'Biasa', 'penting' => 'Penting', 'mendesak' => 'Mendesak'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('priority', $announcement->priority ?? 'biasa') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('priority')" />
        </div>
    </div>

    <div>
        <x-input-label value="Target Pengumuman" />
        <p class="mt-1 mb-3 text-sm text-gray-500">Pilih RT yang akan melihat pengumuman ini. Jika tidak ada yang dipilih, pengumuman akan ditampilkan untuk semua RT.</p>
        <div class="flex flex-wrap gap-3" id="targetRtGroup">
            @php
                // Create: jangan ada yang ter-ceklis by default (user request)
                // Edit: tampilkan sesuai data tersimpan (null = Semua RT, array = RT terpilih)
                if (old('target_rt_ids') !== null) {
                    $oldTargets = old('target_rt_ids');
                    $allSelected = in_array('all', (array) $oldTargets);
                } elseif (isset($announcement) && $announcement->exists) {
                    $oldTargets = $announcement->target_rt_ids ?? [];
                    $allSelected = empty($oldTargets);
                } else {
                    $oldTargets = [];
                    $allSelected = false;
                }
            @endphp
            <label class="target-rt-label flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:border-emerald-300 hover:bg-emerald-50 cursor-pointer">
                <input type="checkbox" name="target_rt_ids[]" value="all"
                    class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    @checked($allSelected)
                    onchange="handleAllRtChange(this)">
                <span class="flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Semua RT
                </span>
            </label>
            @foreach ($rts as $rt)
                @php
                    $isSelected = !empty($oldTargets) && in_array($rt->id, $oldTargets);
                @endphp
                <label class="target-rt-label flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:border-emerald-300 hover:bg-emerald-50 cursor-pointer">
                    <input type="checkbox" name="target_rt_ids[]" value="{{ $rt->id }}"
                        class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                        @checked($isSelected)
                        onchange="handleRtChange(this)">
                    <span class="flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        {{ $rt->name }}
                    </span>
                </label>
            @endforeach
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('target_rt_ids')" />
    </div>

    <script>
        function handleAllRtChange(checkbox) {
            const group = document.getElementById('targetRtGroup');
            const rtCheckboxes = group.querySelectorAll('input[name="target_rt_ids[]"]:not([value="all"])');
            if (checkbox.checked) {
                rtCheckboxes.forEach(el => { el.checked = false; el.disabled = true; });
            } else {
                rtCheckboxes.forEach(el => { el.disabled = false; });
            }
            updateLabelStyles();
        }

        function handleRtChange(checkbox) {
            const group = document.getElementById('targetRtGroup');
            const allCheckbox = group.querySelector('input[value="all"]');
            if (checkbox.checked) {
                allCheckbox.checked = false;
                allCheckbox.dispatchEvent(new Event('change'));
            }
            updateLabelStyles();
        }

        function updateLabelStyles() {
            const group = document.getElementById('targetRtGroup');
            group.querySelectorAll('label.target-rt-label').forEach(label => {
                const cb = label.querySelector('input[type="checkbox"]');
                if (cb.checked) {
                    label.classList.add('border-emerald-500', 'bg-emerald-50', 'text-emerald-700');
                    label.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');
                } else {
                    label.classList.remove('border-emerald-500', 'bg-emerald-50', 'text-emerald-700');
                    label.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const group = document.getElementById('targetRtGroup');
            group.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', updateLabelStyles);
            });
            const allCheckbox = group.querySelector('input[value="all"]');
            if (allCheckbox && allCheckbox.checked) {
                handleAllRtChange(allCheckbox);
            }
            updateLabelStyles();
        });
    </script>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="publication_date" value="Tanggal publikasi" />
            <x-text-input id="publication_date" name="publication_date" type="date" class="mt-1 block w-full" :value="old('publication_date', isset($announcement) ? $announcement->publication_date->toDateString() : now()->toDateString())" required />
            <x-input-error class="mt-2" :messages="$errors->get('publication_date')" />
        </div>
        <div>
            <x-input-label for="status" value="Status" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                <option value="active" @selected(old('status', $announcement->status ?? 'active') === 'active')>Aktif</option>
                <option value="archived" @selected(old('status', $announcement->status ?? 'active') === 'archived')>Arsip</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('status')" />
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input id="is_pinned" name="is_pinned" type="checkbox" value="1"
            class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
            @checked(old('is_pinned', $announcement->is_pinned ?? false)) />
        <x-input-label for="is_pinned" value="Sematkan pengumuman ini (pinned)" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
        <a href="{{ route('announcements.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</div>
