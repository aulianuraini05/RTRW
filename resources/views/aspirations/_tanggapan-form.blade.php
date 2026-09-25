<div class="mt-4 rounded-lg bg-gray-50 p-4">
    @php
        $isForwarded = $aspiration->aspiration_status === 'diteruskan';
        $canGiveTanggapan = !$isForwarded || Auth::user()->isRw() || Auth::user()->isSuperAdmin() || Auth::user()->role === 'admin';
    @endphp

    @if($canGiveTanggapan)
        <form method="POST" action="{{ route('aspirations.tanggapan', $aspiration) }}">
            @csrf
            @method('PATCH')
            <label for="tanggapan-{{ $aspiration->id }}" class="text-sm font-semibold text-gray-800">Tanggapan pengurus</label>
            <textarea id="tanggapan-{{ $aspiration->id }}" name="tanggapan" rows="3" required placeholder="Tulis tanggapan untuk warga..." class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('tanggapan', $aspiration->tanggapan) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('tanggapan')" />
            <button type="submit" class="mt-3 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Simpan tanggapan</button>
        </form>
    @else
        <p class="text-xs text-gray-500">Aspirasi yang diteruskan hanya bisa ditanggapi oleh RW/Admin.</p>
    @endif
</div>
