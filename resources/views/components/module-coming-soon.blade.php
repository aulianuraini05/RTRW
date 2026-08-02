<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-8 text-center shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Modul sedang disiapkan</h3>
                <p class="mt-2 text-gray-600">Halaman {{ strtolower($title) }} akan tersedia setelah alur layanan ini selesai dikembangkan.</p>
                <a href="{{ route('dashboard') }}" class="mt-5 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-800">Kembali ke dashboard</a>
            </div>
        </div>
    </div>
</x-app-layout>
