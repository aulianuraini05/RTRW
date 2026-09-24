<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                    Data Warga
                    @if(Auth::user()->isRt() && Auth::user()->rt)
                        — {{ Auth::user()->rt->name }}
                    @endif
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    @if(Auth::user()->isRt())
                        Daftar warga di {{ Auth::user()->rt?->name ?? 'RT Anda' }} — hanya warga RT ini yang tampil dan bisa Anda kelola (CRUD).
                    @elseif(Auth::user()->isRw())
                        Daftar semua warga di bawah RW — semua RT tampil dan bisa dikelola.
                    @else
                        Daftar warga terdaftar di sistem.
                    @endif
                </p>
            </div>
            <a href="{{ route('warga.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Tambah Warga</a>
        </div>
    </x-slot>

    <div>
        <div class="space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <!-- Summary Cards -->
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-lg bg-indigo-600 p-6 text-white shadow-sm">
                    <p class="text-sm font-medium opacity-90">Total Warga</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ $totalWarga }} warga</p>
                    <p class="mt-1 text-xs opacity-75">
                        @if(Auth::user()->isRt())
                            Warga di {{ Auth::user()->rt?->name ?? 'RT Anda' }}
                        @else
                            Seluruh warga terdaftar
                        @endif
                    </p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-emerald-500">
                    <p class="text-sm font-medium text-gray-500">RT Terdaftar</p>
                    <p class="mt-2 text-xl font-bold text-emerald-600">{{ $totalRt }} RT</p>
                    <p class="mt-1 text-xs text-gray-400">Total RT di lingkungan</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-gray-400">
                    <p class="text-sm font-medium text-gray-500">Halaman</p>
                    <p class="mt-2 text-lg font-bold text-gray-800">{{ $warga->total() }} data</p>
                    <p class="mt-1 text-xs text-gray-400">Warga sesuai filter saat ini</p>
                </div>
            </div>

            <!-- Search & Filter Bar -->
            <div class="rounded-lg bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('warga.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        <x-text-input type="text" name="search" placeholder="Cari nama atau email warga..." :value="request('search')" class="w-full sm:max-w-xs" />
                        @if(Auth::user()->isRw() || Auth::user()->isSuperAdmin() || Auth::user()->role === 'admin')
                            <select name="rt_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="all">Semua RT</option>
                                @foreach($rts as $rt)
                                    <option value="{{ $rt->id }}" @selected((string)request('rt_id') === (string)$rt->id)>{{ $rt->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        <x-primary-button type="submit">Cari</x-primary-button>
                        @if (request()->hasAny(['search', 'rt_id']))
                            <a href="{{ route('warga.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Warga List -->
            <div class="space-y-3">
                @forelse ($warga as $item)
                    <article class="rounded-lg bg-white p-5 shadow-sm">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                            <div class="flex flex-1 items-center gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">
                                    {{ strtoupper(substr($item->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base font-semibold text-gray-900 truncate">
                                        <a href="{{ route('warga.show', $item) }}" class="hover:text-indigo-600">
                                            {{ $item->name }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 truncate">{{ $item->email }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                                        <span class="rounded-full bg-cream-100 px-2.5 py-0.5 font-medium text-ink-700 border border-cream-200">
                                            {{ $item->rt?->name ?? 'Tanpa RT' }}
                                        </span>
                                        <span class="text-gray-400">•</span>
                                        <span class="text-gray-500">Bergabung {{ $item->created_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <a href="{{ route('warga.show', $item) }}" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-indigo-600 ring-1 ring-indigo-600 hover:bg-indigo-50">
                                    Lihat
                                </a>
                                <a href="{{ route('warga.edit', $item) }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('warga.destroy', $item) }}" onsubmit="return confirm('Hapus {{ $item->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 ring-1 ring-red-200 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                        @if(Auth::user()->isRt())
                            Belum ada warga terdaftar di {{ Auth::user()->rt?->name ?? 'RT Anda' }}.
                        @else
                            Belum ada data warga.
                        @endif
                    </div>
                @endforelse

                {{ $warga->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
