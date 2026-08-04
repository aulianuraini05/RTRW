<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajukan aspirasi atau pengaduan</h2></x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('aspirations.store') }}" class="space-y-6 rounded-lg bg-white p-6 shadow-sm">
                @csrf
                <div>
                    <x-input-label for="aspiration_title" value="Judul" />
                    <x-text-input id="aspiration_title" name="aspiration_title" type="text" class="mt-1 block w-full" :value="old('aspiration_title')" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('aspiration_title')" />
                </div>
                <div>
                    <x-input-label for="category" value="Kategori" />
                    <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Pilih kategori</option>
                        @foreach (['Pengaduan lingkungan', 'Usulan kegiatan', 'Keamanan', 'Kebersihan', 'Lainnya'] as $category)
                            <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('category')" />
                </div>
                <div>
                    <x-input-label for="aspiration_content" value="Isi aspirasi atau pengaduan" />
                    <textarea id="aspiration_content" name="aspiration_content" rows="7" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('aspiration_content') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('aspiration_content')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>Kirim aspirasi</x-primary-button>
                    <a href="{{ route('aspirations.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
