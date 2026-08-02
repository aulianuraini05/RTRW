<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengumuman</h2>
            @if (Auth::user()->isAdmin())
                <a href="{{ route('announcements.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Buat pengumuman</a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            @forelse ($announcements as $announcement)
                <article class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                        <div>
                            <div class="mb-2 flex items-center gap-3 text-sm text-gray-500">
                                <time datetime="{{ $announcement->publication_date->toDateString() }}">{{ $announcement->publication_date->translatedFormat('d F Y') }}</time>
                                @if (Auth::user()->isAdmin())
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $announcement->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">{{ $announcement->status === 'active' ? 'Aktif' : 'Arsip' }}</span>
                                @endif
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900"><a href="{{ route('announcements.show', $announcement) }}" class="hover:text-indigo-600">{{ $announcement->announcement_title }}</a></h3>
                            <p class="mt-2 text-gray-600">{{ Str::limit($announcement->announcement_content, 180) }}</p>
                        </div>
                        @if (Auth::user()->isAdmin())
                            <div class="flex shrink-0 items-center gap-3 text-sm">
                                <a href="{{ route('announcements.edit', $announcement) }}" class="font-medium text-indigo-600 hover:text-indigo-800">Edit</a>
                                <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Hapus pengumuman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="font-medium text-red-600 hover:text-red-800" type="submit">Hapus</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">Belum ada pengumuman.</div>
            @endforelse

            {{ $announcements->links() }}
        </div>
    </div>
</x-app-layout>
