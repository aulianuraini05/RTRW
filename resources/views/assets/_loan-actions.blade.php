<div class="flex flex-wrap items-center gap-2.5 sm:justify-end">
    @php
        $statusOptions = [
            'disetujui' => ['label' => 'Setujui', 'classes' => 'bg-indigo-600 hover:bg-indigo-500'],
            'diproses' => ['label' => 'Diproses', 'classes' => 'bg-yellow-500 hover:bg-yellow-400'],
            'dikembalikan' => ['label' => 'Kembalikan', 'classes' => 'bg-green-600 hover:bg-green-500'],
            'ditolak' => ['label' => 'Ditolak', 'classes' => 'bg-red-600 hover:bg-red-500'],
        ];
    @endphp

    @foreach ($statusOptions as $status => $option)
        @php
            $isCurrent = $loan->loan_status === $status;
        @endphp
        <form method="POST" action="{{ route('asset-loans.status.update', $loan) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="loan_status" value="{{ $status }}">
            <button
                type="submit"
                @disabled($isCurrent)
                class="rounded-md px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-colors {{ $option['classes'] }}{{ $isCurrent ? ' opacity-40 cursor-not-allowed' : '' }}"
            >{{ $option['label'] }}</button>
        </form>
    @endforeach
</div>
