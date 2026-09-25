<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-lg text-gray-800 leading-tight">Detail aspirasi</h2></x-slot>

    <div>
        <article class="mx-auto max-w-3xl rounded-lg bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                <span>{{ $aspiration->category }}</span><span>•</span>
                <time datetime="{{ $aspiration->submission_date->toDateString() }}">{{ $aspiration->submission_date->translatedFormat('d F Y') }}</time>
                <span>•</span><span class="font-medium capitalize">{{ $aspiration->aspiration_status }}</span>
                @if($aspiration->aspiration_status === 'diteruskan')
                    <span>•</span><span class="rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-700">Diteruskan ke RW</span>
                @endif
            </div>
            @if($aspiration->forwarded_at)
                <p class="mt-1 text-xs text-purple-600">Diteruskan oleh RT pada {{ $aspiration->forwarded_at->translatedFormat('d F Y H:i') }}</p>
            @endif
            <h1 class="mt-2 text-lg font-bold text-gray-900">{{ $aspiration->aspiration_title }}</h1>
            @if (Auth::user()->isAdmin())
                <p class="mt-2 text-sm text-gray-500">Diajukan oleh: {{ $aspiration->user?->name ?? 'Data warga lama' }}</p>
            @endif
            <div class="mt-3 whitespace-pre-line leading-7 text-gray-700">{{ $aspiration->aspiration_content }}</div>
            @if($aspiration->photo_path)
                <div class="mt-4 flex flex-col items-center text-center">
                    <button type="button" onclick="document.getElementById('aspiration-lightbox').classList.remove('hidden'); document.body.style.overflow='hidden';" class="cursor-zoom-in focus:outline-none">
                        <img src="{{ asset('storage/'.$aspiration->photo_path) }}" alt="Foto bukti aspirasi" class="aspect-[4/3] w-full max-w-md rounded-lg border object-cover hover:opacity-95">
                    </button>
                    <p class="mt-1 text-xs text-gray-500">Foto bukti • klik untuk memperbesar</p>

                    {{-- Lightbox: klik X / klik luar foto / tekan Esc untuk kembali --}}
                    <div id="aspiration-lightbox" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
                        onclick="if (event.target === this) { this.classList.add('hidden'); document.body.style.overflow=''; }">
                        <button type="button" aria-label="Tutup foto" onclick="document.getElementById('aspiration-lightbox').classList.add('hidden'); document.body.style.overflow='';"
                            class="absolute right-4 top-4 z-10 flex h-11 w-11 items-center justify-center rounded-full bg-white text-2xl font-bold leading-none text-gray-900 shadow-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white">
                            &times;
                        </button>
                        <img src="{{ asset('storage/'.$aspiration->photo_path) }}" alt="Foto bukti aspirasi (diperbesar)"
                            class="max-h-[85vh] max-w-full rounded-lg object-contain shadow-2xl">
                    </div>
                    <script>
                        document.addEventListener('keydown', function (e) {
                            if (e.key === 'Escape') {
                                var lb = document.getElementById('aspiration-lightbox');
                                if (lb && !lb.classList.contains('hidden')) {
                                    lb.classList.add('hidden');
                                    document.body.style.overflow = '';
                                }
                            }
                        });
                    </script>
                </div>
            @endif
            @if($aspiration->tanggapan)
                <div class="mt-5 rounded-lg border border-indigo-100 bg-indigo-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Tanggapan pengurus</p>
                    <p class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $aspiration->tanggapan }}</p>
                    <p class="mt-2 text-xs text-gray-500">Oleh {{ $aspiration->tanggapanAuthor?->name ?? 'Pengurus' }} • {{ $aspiration->tanggapan_at?->translatedFormat('d F Y H:i') }}</p>
                </div>
            @endif
            @if (Auth::user()->isAdmin())
                <div class="mt-6 border-t pt-5">
                    @include('aspirations._status-actions', ['aspiration' => $aspiration])
                    @include('aspirations._tanggapan-form', ['aspiration' => $aspiration])
                </div>
            @endif
            <a href="{{ route('aspirations.index') }}" class="mt-6 inline-block border-t pt-5 text-sm font-medium text-gray-600 hover:text-gray-900">Kembali ke daftar</a>
        </article>
    </div>
</x-app-layout>
