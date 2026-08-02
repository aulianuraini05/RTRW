<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat pengumuman</h2></x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('announcements.store') }}" class="rounded-lg bg-white p-6 shadow-sm">
                @csrf
                @include('announcements._form', ['submitLabel' => 'Simpan pengumuman'])
            </form>
        </div>
    </div>
</x-app-layout>
