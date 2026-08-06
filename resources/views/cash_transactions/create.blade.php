<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ Auth::user()->isWarga() ? 'Ajukan Pembayaran Kas' : 'Catat Pembayaran Kas' }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('cash_transactions.store') }}" class="space-y-6 rounded-lg bg-white p-6 shadow-sm">
                @csrf
                @include('cash_transactions._form', ['submitLabel' => 'Simpan Catatan'])
            </form>
        </div>
    </div>
</x-app-layout>
