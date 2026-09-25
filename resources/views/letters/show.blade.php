<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-lg text-gray-800 leading-tight">Detail surat</h2></x-slot>

    <div>
        <article class="mx-auto max-w-3xl rounded-lg bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                <span class="font-mono text-xs">{{ $letter->letter_number }}</span>
                <span>•</span>
                <span>{{ $letter->letter_type }}</span>
                <span>•</span>
                <time datetime="{{ $letter->submission_date->toDateString() }}">{{ $letter->submission_date->translatedFormat('d F Y') }}</time>
                <span>•</span><span class="font-medium capitalize">{{ $letter->letter_status }}</span>
            </div>
            <h1 class="mt-2 text-lg font-bold text-gray-900">{{ $letter->letter_type }}</h1>
            @if (Auth::user()->isAdmin())
                <p class="mt-2 text-sm text-gray-500">Diajukan oleh: {{ $letter->user?->name ?? 'Data warga lama' }}</p>
            @endif
            <div class="mt-3 whitespace-pre-line leading-7 text-gray-700">{{ $letter->purpose }}</div>

            <div class="mt-6 border-t pt-5">
                <h3 class="text-sm font-semibold text-gray-800">Lampiran syarat ({{ $letter->attachments->count() }} file)</h3>
                @if ($letter->attachments->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">Tidak ada lampiran. Pengajuan lama sebelum fitur lampiran wajib.</p>
                @else
                    <ul class="mt-3 space-y-2">
                        @foreach ($letter->attachments as $att)
                            <li class="flex flex-wrap items-center justify-between gap-2 rounded-md bg-gray-50 px-3 py-2 text-sm">
                                <span class="font-medium text-gray-700">{{ $att->label }}</span>
                                <a href="{{ Storage::url($att->file_path) }}" target="_blank" rel="noopener" class="font-medium text-indigo-600 hover:text-indigo-800">Lihat file</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if (in_array($letter->letter_status, ['disetujui', 'selesai'], true))
                <div class="mt-6 border-t pt-5">
                    <a href="{{ route('letters.cetak', $letter) }}" target="_blank" rel="noopener" class="inline-block rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-500">Lihat PDF Surat Resmi</a>
                    <p class="mt-2 text-xs text-gray-500">Klik untuk melihat surat resmi dalam format PDF.</p>
                </div>
            @endif

            @if (Auth::user()->isAdmin())
                <div class="mt-6 border-t pt-5">
                    @include('letters._status-actions', ['letter' => $letter])
                </div>
            @endif

            <a href="{{ route('letters.index') }}" class="mt-6 inline-block border-t pt-5 text-sm font-medium text-gray-600 hover:text-gray-900">Kembali ke daftar</a>
        </article>
    </div>
</x-app-layout>
