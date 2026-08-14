<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengumuman</h2></x-slot>

    <div class="py-12">
        <article class="mx-auto max-w-3xl rounded-lg bg-white p-6 shadow-sm sm:p-8">
            @php
                $priorityStyles = [
                    'biasa' => 'bg-slate-100 text-slate-600',
                    'penting' => 'bg-amber-100 text-amber-700',
                    'mendesak' => 'bg-red-100 text-red-700',
                ];
                $categoryStyles = [
                    'umum' => 'bg-slate-100 text-slate-600',
                    'kegiatan' => 'bg-sky-100 text-sky-700',
                    'kesehatan' => 'bg-emerald-100 text-emerald-700',
                    'keamanan' => 'bg-amber-100 text-amber-700',
                    'lingkungan' => 'bg-lime-100 text-lime-700',
                    'agenda' => 'bg-indigo-100 text-indigo-700',
                ];
            @endphp
            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                <time datetime="{{ $announcement->publication_date->toDateString() }}">{{ $announcement->publication_date->translatedFormat('d F Y') }}</time>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $priorityStyles[$announcement->priority] ?? $priorityStyles['biasa'] }}">{{ ucfirst($announcement->priority) }}</span>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $categoryStyles[$announcement->category] ?? $categoryStyles['umum'] }}">{{ ucfirst($announcement->category) }}</span>
                @if (Auth::user()->isAdmin())
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $announcement->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">{{ $announcement->status === 'active' ? 'Aktif' : 'Arsip' }}</span>
                @endif
            </div>
            <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $announcement->announcement_title }}</h1>
            <div class="mt-6 whitespace-pre-line leading-7 text-gray-700">{{ $announcement->announcement_content }}</div>
            <div class="mt-8 flex items-center gap-4 border-t pt-6">
                <a href="{{ route('announcements.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">← Kembali</a>
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('announcements.edit', $announcement) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Edit pengumuman</a>
                @endif
            </div>
        </article>
    </div>
</x-app-layout>
