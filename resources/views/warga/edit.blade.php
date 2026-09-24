<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">Edit Warga — {{ $user->name }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ $user->rt?->name ?? 'Tanpa RT' }} • {{ $user->email }}</p>
    </x-slot>

    <div>
        <div class="mx-auto max-w-3xl">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('warga.update', $user) }}">
                    @csrf
                    @method('PUT')
                    @include('warga._form', ['submitLabel' => 'Perbarui Warga'])
                </form>

                <div class="mt-8 border-t pt-6">
                    <h3 class="text-sm font-semibold text-red-700">Hapus Warga</h3>
                    <p class="mt-1 text-xs text-gray-500">Tindakan ini menghapus akun warga beserta aksesnya. Tidak dapat dibatalkan.</p>
                    <form method="POST" action="{{ route('warga.destroy', $user) }}" class="mt-3" onsubmit="return confirm('Yakin hapus {{ $user->name }}?')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>Hapus Akun Warga</x-danger-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
