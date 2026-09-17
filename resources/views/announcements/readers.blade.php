<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">Pembaca Pengumuman</h2>
    </x-slot>

    <div>
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Pengumuman</p>
                <h1 class="mt-1 text-lg font-bold text-gray-900">{{ $announcement->announcement_title }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ number_format($announcement->read_count) }} warga sudah membaca</p>
            </div>

            <div class="space-y-3">
                @forelse ($readers as $reader)
                    <article class="flex items-center justify-between gap-4 rounded-lg bg-white p-4 shadow-sm">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">{{ $reader->name }}</h3>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $reader->rt?->name ?? 'RT' }} • {{ $reader->email }}</p>
                        </div>
                        <span class="shrink-0 text-xs text-gray-400">
                            {{ $reader->pivot->read_at ? \Carbon\Carbon::parse($reader->pivot->read_at)->translatedFormat('d M Y H:i') : '-' }}
                        </span>
                    </article>
                @empty
                    <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                        Belum ada warga yang membaca pengumuman ini.
                    </div>
                @endforelse

                {{ $readers->links() }}
            </div>

            <a href="{{ route('announcements.index') }}" class="inline-block text-sm font-medium text-gray-600 hover:text-gray-900">Kembali ke daftar pengumuman</a>
        </div>
    </div>
</x-app-layout>
