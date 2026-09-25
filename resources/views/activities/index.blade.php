<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">{{ Auth::user()->isWarga() ? 'Riwayat Aktivitas Saya' : 'Riwayat Aktivitas' }}</h2>
    </x-slot>

    <div>
        <div class="space-y-4">
            <form method="GET" action="{{ route('activities.index') }}" class="flex flex-col gap-2 rounded-lg bg-white p-4 shadow-sm sm:flex-row sm:items-center">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas / pelaku..."
                    class="w-full flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                <select name="module" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua modul</option>
                    @foreach (['pengumuman' => 'Pengumuman', 'aspirasi' => 'Aspirasi', 'aset' => 'Aset', 'kas' => 'Kas', 'iuran' => 'Iuran', 'surat' => 'Surat', 'marketplace' => 'Marketplace'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('module') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Cari</button>
                    @if (request()->hasAny(['search', 'module']))
                        <a href="{{ route('activities.index') }}" class="rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-200">Reset</a>
                    @endif
                </div>
            </form>

            @forelse ($activities as $activity)
                @php
                    $moduleClasses = [
                        'pengumuman' => 'bg-brand-50 text-brand-700',
                        'aspirasi' => 'bg-amber-50 text-amber-700',
                        'aset' => 'bg-violet-50 text-violet-700',
                        'kas' => 'bg-emerald-50 text-emerald-700',
                        'iuran' => 'bg-rose-50 text-rose-700',
                        'surat' => 'bg-sky-50 text-sky-700',
                        'marketplace' => 'bg-orange-50 text-orange-700',
                    ];
                @endphp
                <article class="rounded-lg bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $moduleClasses[$activity->module] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($activity->module) }}</span>
                        <span class="font-medium text-gray-900">{{ $activity->action }}</span>
                        @if ($activity->subject)
                            <span class="text-gray-500">• {{ $activity->subject }}</span>
                        @endif
                    </div>
                    @if ($activity->description)
                        <p class="mt-1 text-sm text-gray-600">{{ $activity->description }}</p>
                    @endif
                    <p class="mt-2 text-xs text-gray-500">
                        {{ $activity->user?->name ?? 'Pengguna dihapus' }}
                        @if ($activity->rt) • {{ $activity->rt->name }} @endif
                        • {{ $activity->created_at->translatedFormat('d F Y H:i') }}
                    </p>
                </article>
            @empty
                <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                    Belum ada aktivitas tercatat.
                </div>
            @endforelse

            {{ $activities->links() }}
        </div>
    </div>
</x-app-layout>
