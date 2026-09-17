<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">Kas Bulanan per RT</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('cash_transactions.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Batal</a>
                <a href="{{ route('kas_schedules.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">+ Tentukan Kas Bulan Ini</a>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            @if (! Auth::user()->isRt())
                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <form method="GET" action="{{ route('kas_schedules.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <select name="rt_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua RT</option>
                            @foreach ($rts as $rt)
                                <option value="{{ $rt->id }}" @selected(request('rt_id') == $rt->id)>{{ $rt->name }}</option>
                            @endforeach
                        </select>
                        <x-primary-button type="submit">Filter</x-primary-button>
                        <a href="{{ route('kas_schedules.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Batal</a>
                    </form>
                </div>
            @endif

            <div class="space-y-3">
                @forelse ($schedules as $schedule)
                    <article class="rounded-lg bg-white p-5 shadow-sm">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                            <div class="flex-1">
                                <div class="mb-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                    <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 font-medium text-indigo-700">{{ $schedule->rt?->name ?? 'RT' }}</span>
                                    <span>•</span>
                                    <span>{{ $schedule->paid_count }} / {{ $schedule->transactions_count }} warga sudah bayar</span>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900">
                                    <a href="{{ route('kas_schedules.show', $schedule) }}" class="hover:text-indigo-600">
                                        Kas {{ $schedule->period_label }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm font-semibold text-gray-700">Rp {{ number_format((float) $schedule->amount, 0, ',', '.') }} / warga</p>
                                <p class="mt-1 text-xs text-gray-400">Ditetapkan oleh {{ $schedule->creator?->name ?? 'Pengurus' }} • {{ $schedule->created_at->translatedFormat('d F Y') }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 border-t pt-3 sm:border-l sm:border-t-0 sm:pl-4 sm:pt-0">
                                <a href="{{ route('kas_schedules.show', $schedule) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Lihat Tagihan</a>
                                <form method="POST" action="{{ route('kas_schedules.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal kas ini? Tagihan yang belum dibayar ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                        Belum ada jadwal kas bulanan.
                    </div>
                @endforelse

                {{ $schedules->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
