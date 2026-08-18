{{-- Bagian isi sidebar (dipakai untuk versi desktop & drawer mobile) --}}
<div class="flex h-full flex-col bg-white">

    {{-- Brand --}}
    <div class="flex h-16 shrink-0 items-center justify-between border-b border-cream-200 px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="h-8 w-8 fill-brand-600" />
            <span class="text-base font-bold text-ink-800">{{ config('app.name', 'Smart RT/RW') }}</span>
        </a>
        <button @click="open = false"
                class="rounded-lg p-2 text-brand-700 hover:bg-cream-100 lg:hidden"
                aria-label="Tutup menu">
            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Navigasi --}}
    <div class="flex-1 overflow-y-auto px-3 py-4">

        {{-- Menu Utama --}}
        <p class="px-3 text-sm font-bold uppercase tracking-wide text-ink-400">Menu Utama</p>
        <nav class="mt-2 space-y-1">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <svg class="h-5 w-5 shrink-0" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                {{ __('Dashboard') }}
            </x-nav-link>

            <x-nav-link :href="route('announcements.index')" :active="request()->routeIs('announcements.*')">
                <svg class="h-5 w-5 shrink-0" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                </svg>
                {{ __('Pengumuman') }}
            </x-nav-link>

            <x-nav-link :href="route(Auth::user()->isWarga() ? 'aspirations.create' : 'aspirations.index')" :active="request()->routeIs('aspirations.*')">
                <svg class="h-5 w-5 shrink-0" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                </svg>
                {{ __('Aspirasi') }}
            </x-nav-link>

            <x-nav-link :href="route(Auth::user()->isWarga() ? 'letters.create' : 'letters.index')" :active="request()->routeIs('letters.*')">
                <svg class="h-5 w-5 shrink-0" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                {{ __('Persuratan') }}
            </x-nav-link>
        </nav>

        {{-- Administrasi --}}
        <p class="mt-6 px-3 text-xs font-bold uppercase tracking-wide text-ink-400">Administrasi</p>
        <nav class="mt-2 space-y-1">
            <x-nav-link :href="route('assets.index')" :active="request()->routeIs('assets.*')">
                <svg class="h-5 w-5 shrink-0" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
                {{ __(Auth::user()->isAdmin() ? 'Administrasi' : 'Aset') }}
            </x-nav-link>

            <x-nav-link :href="route('cash_transactions.index')" :active="request()->routeIs('cash_transactions.*')">
                <svg class="h-5 w-5 shrink-0" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
                {{ __('Kas RT/RW') }}
            </x-nav-link>

            <x-nav-link :href="route('contributions.index')" :active="request()->routeIs('contributions.*')">
                <svg class="h-5 w-5 shrink-0" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                </svg>
                {{ __('Iuran Warga') }}
            </x-nav-link>
        </nav>

        {{-- Layanan --}}
        <p class="mt-6 px-3 text-xs font-bold uppercase tracking-wide text-ink-400">Layanan</p>
        <nav class="mt-2 space-y-1">
            <x-nav-link :href="route('marketplaces.index')" :active="request()->routeIs('marketplaces.*')">
                <svg class="h-5 w-5 shrink-0" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                {{ __('Marketplace') }}
            </x-nav-link>
        </nav>

        {{-- Akun --}}
        <p class="mt-6 px-3 text-xs font-bold uppercase tracking-wide text-ink-400">Akun</p>
        <nav class="mt-2 space-y-1">
            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                <svg class="h-5 w-5 shrink-0" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                {{ __('Profile') }}
            </x-nav-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                    <svg class="h-5 w-5 shrink-0" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    {{ __('Log Out') }}
                </x-nav-link>
            </form>
        </nav>
    </div>

    {{-- Info pengguna --}}
    <div class="shrink-0 border-t border-cream-200 p-3">
        <div class="flex items-center gap-2.5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-ink-800">{{ Auth::user()->name }}</p>
                <p class="truncate text-xs text-ink-400">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</div>