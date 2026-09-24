<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">Detail Warga</h2>
    </x-slot>

    <div>
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <article class="rounded-lg bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-lg font-bold text-indigo-700">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg font-bold text-gray-900 truncate">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                        <span class="mt-1 inline-block rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                            {{ $user->rt?->name ?? 'Tanpa RT' }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-md bg-gray-50 p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Email / WA</p>
                        <p class="mt-1 text-sm font-medium text-gray-900 break-all">{{ $user->email }}</p>
                        <p class="text-xs text-gray-500">{{ $user->no_whatsapp ?? '-' }}</p>
                    </div>
                    <div class="rounded-md bg-gray-50 p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase">NIK / No. KK</p>
                        <p class="mt-1 text-sm font-medium text-gray-900 font-mono">{{ $user->nik ?? '-' }}</p>
                        <p class="text-xs text-gray-500 font-mono">KK: {{ $user->no_kk ?? '-' }}</p>
                    </div>
                    <div class="rounded-md bg-gray-50 p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase">TTL</p>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $user->tempat_lahir ?? '-' }}{{ $user->tanggal_lahir ? ', '.$user->tanggal_lahir->translatedFormat('d F Y') : '' }}</p>
                        <p class="text-xs text-gray-500">{{ $user->jenis_kelamin === 'L' ? 'Laki-laki' : ($user->jenis_kelamin === 'P' ? 'Perempuan' : '-') }} • {{ $user->agama ?? '-' }}</p>
                    </div>
                    <div class="rounded-md bg-gray-50 p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase">RT / Alamat</p>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $user->rt?->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $user->alamat_rumah ? $user->alamat_rumah.($user->no_rumah ? ' No. '.$user->no_rumah : '') : '-' }}</p>
                    </div>
                    <div class="rounded-md bg-gray-50 p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Status / Pendidikan / Pekerjaan</p>
                        <p class="mt-1 text-sm font-medium text-gray-900 capitalize">{{ str_replace('_',' ', $user->status_perkawinan ?? '-') }}</p>
                        <p class="text-xs text-gray-500">{{ $user->pendidikan_terakhir ?? '-' }} • {{ $user->pekerjaan ?? '-' }}</p>
                    </div>
                    <div class="rounded-md bg-gray-50 p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Role / Terdaftar</p>
                        <p class="mt-1 text-sm font-medium text-gray-900 capitalize">{{ $user->role }}</p>
                        <p class="text-xs text-gray-500">{{ $user->created_at->translatedFormat('d F Y H:i') }}</p>
                    </div>
                </div>

                <div class="mt-6 border-t pt-6">
                    <h3 class="text-sm font-bold text-gray-800">Ringkasan Aktivitas</h3>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-xs text-gray-500">Kas</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->cashTransactions->count() }} transaksi</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-xs text-gray-500">Iuran</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->contributions->count() }} transaksi</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-xs text-gray-500">Aspirasi</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->aspirations->count() }} pengajuan</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-xs text-gray-500">Persuratan</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->letters->count() }} pengajuan</p>
                        </div>
                    </div>
                </div>
            </article>

            <div class="flex items-center gap-3">
                <a href="{{ route('warga.edit', $user) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Edit Warga</a>
                <form method="POST" action="{{ route('warga.destroy', $user) }}" onsubmit="return confirm('Yakin hapus {{ $user->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-red-600 ring-1 ring-red-200 hover:bg-red-50">Hapus Warga</button>
                </form>
                <a href="{{ route('warga.index') }}" class="ml-auto text-sm font-medium text-gray-600 hover:text-gray-900">Kembali ke Data Warga</a>
            </div>
        </div>
    </div>
</x-app-layout>
