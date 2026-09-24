<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">Tambah Warga Baru</h2>
        <p class="mt-1 text-sm text-gray-500">
            @if(Auth::user()->isRt())
                Warga baru akan otomatis terdaftar di {{ Auth::user()->rt?->name ?? 'RT Anda' }}.
            @else
                Pilih RT tujuan — RW / Admin bisa menambah warga di semua RT.
            @endif
        </p>
    </x-slot>

    <div>
        <div class="mx-auto max-w-3xl">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('warga.store') }}">
                    @csrf
                    @include('warga._form', ['submitLabel' => 'Simpan Warga'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
