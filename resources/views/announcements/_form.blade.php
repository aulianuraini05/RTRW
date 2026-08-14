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
