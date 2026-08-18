<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-lg text-gray-800 leading-tight">Detail aspirasi</h2></x-slot>

    <div>
        <article class="mx-auto max-w-3xl rounded-lg bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                <span>{{ $aspiration->category }}</span><span>•</span>
                <time datetime="{{ $aspiration->submission_date->toDateString() }}">{{ $aspiration->submission_date->translatedFormat('d F Y') }}</time>
                <span>•</span><span class="font-medium capitalize">{{ $aspiration->aspiration_status }}</span>
            </div>
            <h1 class="mt-2 text-lg font-bold text-gray-900">{{ $aspiration->aspiration_title }}</h1>
            @if (Auth::user()->isAdmin())
                <p class="mt-2 text-sm text-gray-500">Diajukan oleh: {{ $aspiration->user?->name ?? 'Data warga lama' }}</p>
            @endif
            <div class="mt-3 whitespace-pre-line leading-7 text-gray-700">{{ $aspiration->aspiration_content }}</div>
            @if (Auth::user()->isAdmin())
                <div class="mt-6 border-t pt-5">
                    @include('aspirations._status-actions', ['aspiration' => $aspiration])
                </div>
            @endif
            <a href="{{ route('aspirations.index') }}" class="mt-6 inline-block border-t pt-5 text-sm font-medium text-gray-600 hover:text-gray-900">← Kembali ke daftar</a>
        </article>
    </div>
</x-app-layout>
