<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail surat</h2></x-slot>

    <div class="py-12">
        <article class="mx-auto max-w-3xl rounded-lg bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                <span class="font-mono text-xs">{{ $letter->letter_number }}</span>
                <span>•</span>
                <span>{{ $letter->letter_type }}</span>
                <span>•</span>
                <time datetime="{{ $letter->submission_date->toDateString() }}">{{ $letter->submission_date->translatedFormat('d F Y') }}</time>
                <span>•</span><span class="font-medium capitalize">{{ $letter->letter_status }}</span>
            </div>
            <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $letter->letter_type }}</h1>
            @if (Auth::user()->isAdmin())
                <p class="mt-2 text-sm text-gray-500">Diajukan oleh: {{ $letter->user?->name ?? 'Data warga lama' }}</p>
            @endif
            <div class="mt-6 whitespace-pre-line leading-7 text-gray-700">{{ $letter->purpose }}</div>

            @if (Auth::user()->isAdmin())
                <div class="mt-8 border-t pt-6">
                    @include('letters._status-actions', ['letter' => $letter])
                </div>
            @endif

            <a href="{{ route('letters.index') }}" class="mt-8 inline-block border-t pt-6 text-sm font-medium text-gray-600 hover:text-gray-900">← Kembali ke daftar</a>
        </article>
    </div>
</x-app-layout>
