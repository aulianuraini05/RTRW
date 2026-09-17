<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">Iuran {{ $iuranSchedule->jenis }} {{ $iuranSchedule->period_label }} — {{ $iuranSchedule->rt?->name ?? '' }}</h2>
    </x-slot>

    <div>
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-lg bg-indigo-600 p-6 text-white shadow-sm">
                    <p class="text-sm font-medium opacity-90">Nominal per Warga</p>
                    <p class="mt-2 text-xl font-extrabold">Rp {{ number_format((float) $iuranSchedule->amount, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs opacity-75">Iuran {{ $iuranSchedule->jenis }} {{ $iuranSchedule->period_label }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500">Sudah Bayar</p>
                    <p class="mt-2 text-lg font-bold text-green-600">{{ $totalLunas }} / {{ $totalWarga }} warga</p>
                    <p class="mt-1 text-xs text-gray-400">Terkumpul Rp {{ number_format((float) $totalTerkumpul, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-yellow-500">
                    <p class="text-sm font-medium text-gray-500">Belum Bayar</p>
                    <p class="mt-2 text-lg font-bold text-yellow-600">{{ $totalWarga - $totalLunas }} warga</p>
                    <form method="POST" action="{{ route('iuran_schedules.sync', $iuranSchedule) }}" class="mt-2">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Terbitkan tagihan susulan</button>
                    </form>
                </div>
            </div>

            <div class="space-y-3">
                @forelse ($contributions as $contribution)
                    <article class="rounded-lg bg-white p-5 shadow-sm">
                        <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">{{ $contribution->display_name }}</h3>
                                <p class="mt-1 text-sm text-gray-600">Rp {{ number_format((float) $contribution->amount, 0, ',', '.') }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $contribution->payment_status === 'lunas' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($contribution->payment_status) }}
                            </span>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">Belum ada tagihan untuk periode ini.</div>
                @endforelse

                {{ $contributions->links() }}
            </div>

            <a href="{{ route('iuran_schedules.index') }}" class="inline-block text-sm font-medium text-gray-600 hover:text-gray-900">Kembali ke daftar jadwal iuran</a>
        </div>
    </div>
</x-app-layout>
