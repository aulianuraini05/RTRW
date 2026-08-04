<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ Auth::user()->isAdmin() ? 'Aspirasi & Pengaduan Warga' : 'Aspirasi & Pengaduan Saya' }}</h2>
            @if (Auth::user()->isWarga())
                <a href="{{ route('aspirations.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Ajukan aspirasi</a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            @forelse ($aspirations as $aspiration)
                @php
                    $statusClasses = [
                        'dikirim' => 'bg-blue-100 text-blue-700',
                        'diterima' => 'bg-indigo-100 text-indigo-700',
                        'diproses' => 'bg-yellow-100 text-yellow-700',
                        'selesai' => 'bg-green-100 text-green-700',
                        'ditolak' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <article class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                        <div>
                            <div class="mb-2 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                                <span>{{ $aspiration->category }}</span>
                                <span>•</span>
                                <time datetime="{{ $aspiration->submission_date->toDateString() }}">{{ $aspiration->submission_date->translatedFormat('d F Y') }}</time>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClasses[$aspiration->aspiration_status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($aspiration->aspiration_status) }}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900"><a href="{{ route('aspirations.show', $aspiration) }}" class="hover:text-indigo-600">{{ $aspiration->aspiration_title }}</a></h3>
                            @if (Auth::user()->isAdmin())
                                <p class="mt-1 text-sm text-gray-500">Pengaju: {{ $aspiration->user?->name ?? 'Data warga lama' }}</p>
                            @endif
                            <p class="mt-2 text-gray-600">{{ Str::limit($aspiration->aspiration_content, 180) }}</p>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-3 sm:flex-col sm:items-end">
                            @if (Auth::user()->isAdmin())
                                @include('aspirations._status-actions', ['aspiration' => $aspiration])
                            @endif
                            <a href="{{ route('aspirations.show', $aspiration) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat detail</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                    {{ Auth::user()->isAdmin() ? 'Belum ada aspirasi dari warga.' : 'Anda belum mengirim aspirasi atau pengaduan.' }}
                    @if (Auth::user()->isWarga())
                        <a href="{{ route('aspirations.create') }}" class="mt-4 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Ajukan aspirasi pertama</a>
                    @endif
                </div>
            @endforelse

            {{ $aspirations->links() }}
        </div>
    </div>
</x-app-layout>
