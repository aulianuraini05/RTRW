<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-lg text-gray-800 leading-tight">Edit pengumuman</h2></x-slot>

    <div>
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('announcements.update', $announcement) }}" class="rounded-lg bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')
                @include('announcements._form', ['submitLabel' => 'Simpan perubahan'])
            </form>
        </div>
    </div>
</x-app-layout>
