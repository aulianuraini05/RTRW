<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">{{ Auth::user()->isAdmin() ? 'Persuratan Warga' : 'Surat Saya' }}</h2>
            @if (Auth::user()->isWarga())
                <a href="{{ route('letters.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Ajukan surat</a>
            @endif
        </div>
    </x-slot>

    <div>
        <div class="space-y-4">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            @forelse ($letters as $letter)
                @php
                    $statusClasses = [
                        'diajukan' => 'bg-blue-100 text-blue-700',
                        'diproses' => 'bg-yellow-100 text-yellow-700',
                        'disetujui' => 'bg-indigo-100 text-indigo-700',
                        'selesai' => 'bg-green-100 text-green-700',
                        'ditolak' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <article class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                        <div>
                            <div class="mb-2 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                                <span class="font-mono text-xs">{{ $letter->letter_number }}</span>
                                <span>•</span>
                                <span>{{ $letter->letter_type }}</span>
                                <span>•</span>
                                <time datetime="{{ $letter->submission_date->toDateString() }}">{{ $letter->submission_date->translatedFormat('d F Y') }}</time>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClasses[$letter->letter_status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($letter->letter_status) }}</span>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900"><a href="{{ route('letters.show', $letter) }}" class="hover:text-indigo-600">{{ $letter->letter_type }}</a></h3>
                            @if (Auth::user()->isAdmin())
                                <p class="mt-1 text-sm text-gray-500">Pemohon: {{ $letter->user?->name ?? 'Data warga lama' }}</p>
                            @endif
                            <p class="mt-2 text-gray-600">{{ Str::limit($letter->purpose, 180) }}</p>
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center gap-3 sm:flex-col sm:items-end">
                            @if (Auth::user()->isAdmin())
                                @include('letters._status-actions', ['letter' => $letter])
                            @endif
                            <a href="{{ route('letters.show', $letter) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat detail</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                    {{ Auth::user()->isAdmin() ? 'Belum ada permohonan surat dari warga.' : 'Anda belum mengajukan surat apa pun.' }}
                    @if (Auth::user()->isWarga())
                        <a href="{{ route('letters.create') }}" class="mt-4 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Ajukan surat pertama</a>
                    @endif
                </div>
            @endforelse

            {{ $letters->links() }}
        </div>
    </div>
</x-app-layout>
