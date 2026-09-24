<div class="flex flex-wrap items-center gap-2 sm:justify-end">
    @php
        $isForwarded = $aspiration->aspiration_status === 'diteruskan';
        $statusOptions = [
            'diterima' => ['label' => 'Diterima', 'classes' => 'bg-indigo-600 hover:bg-indigo-500'],
            'diproses' => ['label' => 'Diproses', 'classes' => 'bg-yellow-500 hover:bg-yellow-400'],
            'selesai' => ['label' => 'Selesai', 'classes' => 'bg-green-600 hover:bg-green-500'],
            'ditolak' => ['label' => 'Ditolak', 'classes' => 'bg-red-600 hover:bg-red-500'],
        ];
        // Ketua RT: selalu ada tombol Teruskan (di semua akun RT) selama belum diteruskan
        $canForward = Auth::user()->isRt() && !$isForwarded;
        // Jika sudah diteruskan, hanya RW/Admin yang bisa finalisasi
        $canChangeStatus = !$isForwarded || Auth::user()->isRw() || Auth::user()->isSuperAdmin() || Auth::user()->role === 'admin';
    @endphp

    @if($canForward)
        <form method="POST" action="{{ route('aspirations.status.update', $aspiration) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="aspiration_status" value="diteruskan">
            <button type="submit" class="rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white hover:bg-purple-500">Teruskan ke RW</button>
        </form>
    @endif

    @foreach ($statusOptions as $status => $option)
        @php
            $isCurrent = $aspiration->aspiration_status === $status;
            $disabled = $isCurrent || !$canChangeStatus;
        @endphp
        <form method="POST" action="{{ route('aspirations.status.update', $aspiration) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="aspiration_status" value="{{ $status }}">
            <button
                type="submit"
                @disabled($disabled)
                class="rounded-md px-3 py-2 text-sm font-semibold text-white {{ $option['classes'] }}{{ $disabled ? ' opacity-50 cursor-not-allowed' : '' }}"
                title="{{ !$canChangeStatus ? 'Hanya RW yang bisa memproses setelah diteruskan' : '' }}"
            >{{ $option['label'] }}</button>
        </form>
    @endforeach
    @if($isForwarded)
        <span class="rounded-md bg-purple-100 px-2 py-1 text-xs font-medium text-purple-700">Diteruskan ke RW • {{ $aspiration->forwarded_at?->translatedFormat('d M Y H:i') }}</span>
    @endif
</div>
