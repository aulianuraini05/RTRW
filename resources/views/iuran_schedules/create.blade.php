<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">Tentukan Iuran Bulan Ini</h2>
    </x-slot>

    <div>
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('iuran_schedules.store') }}" class="space-y-6 rounded-lg bg-white p-6 shadow-sm">
                @csrf

                <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-700">
                    Nominal yang Anda simpan otomatis menjadi tagihan untuk seluruh warga
                    @if (Auth::user()->isRt() && Auth::user()->rt)
                        {{ Auth::user()->rt->name }}
                    @endif
                    pada bulan tersebut. Warga tinggal memilih metode pembayaran.
                </div>

                @if (Auth::user()->isRt())
                    <input type="hidden" name="rt_id" value="{{ Auth::user()->rt_id }}">
                    <div>
                        <x-input-label value="RT" />
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ Auth::user()->rt?->name ?? '-' }}</p>
                    </div>
                @else
                    <div>
                        <x-input-label for="rt_id" value="RT" />
                        <select id="rt_id" name="rt_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">-- Pilih RT --</option>
                            @foreach ($rts as $rt)
                                <option value="{{ $rt->id }}" @selected(old('rt_id') == $rt->id)>{{ $rt->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('rt_id')" />
                    </div>
                @endif

                <div>
                    <x-input-label for="jenis" value="Jenis Iuran" />
                    <select id="jenis" name="jenis" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Pilih jenis</option>
                        @foreach (\App\Models\IuranSchedule::jenisOptions() as $jenis)
                            <option value="{{ $jenis }}" @selected(old('jenis') === $jenis)>{{ $jenis }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('jenis')" />
                </div>

                <div>
                    <x-input-label for="periode" value="Bulan Iuran" />
                    <x-text-input id="periode" name="periode" type="month" class="mt-1 block w-full" :value="old('periode', $defaultPeriode)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('periode')" />
                </div>

                <div>
                    <x-input-label for="amount" value="Nominal Iuran per Warga (Rp)" />
                    <x-text-input id="amount" name="amount" type="number" step="0.01" min="1000" class="mt-1 block w-full" :value="old('amount')" required />
                    <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Terbitkan Tagihan</x-primary-button>
                    <a href="{{ route('iuran_schedules.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
