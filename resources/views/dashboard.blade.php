<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Smart RT/RW') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="text-sm text-gray-500">Selamat datang, {{ Auth::user()->name }}.</p>
                        <h3 class="mt-1 text-lg font-semibold">Informasi lingkungan</h3>
                        <p class="mt-2 text-sm text-gray-600">Lihat pengumuman terbaru dari RT/RW.</p>
                        <a class="mt-4 inline-block font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('announcements.index') }}">Lihat pengumuman →</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold">Layanan warga</h3>
                        <p class="mt-2 text-sm text-gray-600">Ajukan aspirasi atau kebutuhan persuratan secara digital.</p>
                        <div class="mt-4 flex gap-4">
                            <a class="font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('aspirations.index') }}">Aspirasi</a>
                            <a class="font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('letters.index') }}">Surat</a>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->isAdmin())
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold">Kelola pengumuman</h3>
                            <p class="mt-2 text-sm text-gray-600">Buat informasi baru atau arsipkan pengumuman lama.</p>
                            <a class="mt-4 inline-block font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('announcements.create') }}">Buat pengumuman →</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
