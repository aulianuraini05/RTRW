<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">{{ Auth::user()->isWarga() ? 'Ajukan Pembayaran Iuran' : 'Catat Pembayaran Iuran' }}</h2>
    </x-slot>

    <div>
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('contributions.store') }}" class="space-y-6 rounded-lg bg-white p-6 shadow-sm">
                @csrf
                @include('contributions._form', ['submitLabel' => 'Simpan Catatan'])
            </form>
        </div>
    </div>
</x-app-layout>
