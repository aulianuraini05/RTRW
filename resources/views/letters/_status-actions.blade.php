<div class="flex flex-wrap items-center gap-2 sm:justify-end">
    @php
        $statusOptions = [
            'diproses' => ['label' => 'Diproses', 'classes' => 'bg-yellow-500 hover:bg-yellow-400'],
            'disetujui' => ['label' => 'Disetujui', 'classes' => 'bg-indigo-600 hover:bg-indigo-500'],
            'selesai' => ['label' => 'Selesai', 'classes' => 'bg-green-600 hover:bg-green-500'],
            'ditolak' => ['label' => 'Ditolak', 'classes' => 'bg-red-600 hover:bg-red-500'],
        ];
    @endphp

    @foreach ($statusOptions as $status => $option)
        @php
            $isCurrent = $letter->letter_status === $status;
        @endphp
        <form method="POST" action="{{ route('letters.status.update', $letter) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="letter_status" value="{{ $status }}">
            <button
                type="submit"
                @disabled($isCurrent)
                class="rounded-md px-3 py-2 text-sm font-semibold text-white {{ $option['classes'] }}{{ $isCurrent ? ' opacity-50 cursor-not-allowed' : '' }}"
            >{{ $option['label'] }}</button>
        </form>
    @endforeach
</div>
