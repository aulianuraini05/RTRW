<div x-data="{ open: false }">
    {{-- Top bar untuk layar kecil (mobile) --}}
    <div class="sticky top-0 z-40 flex h-14 items-center justify-between border-b border-cream-200 bg-white px-4 lg:hidden">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="h-8 w-8 fill-brand-600" />
            <span class="text-base font-bold text-ink-800">{{ config('app.name', 'Smart RT/RW') }}</span>
        </a>

        <div class="flex items-center gap-1">
            <a href="{{ route('notifications.index') }}" aria-label="Notifikasi"
               class="relative inline-flex items-center justify-center rounded-lg p-2 text-brand-700 hover:bg-cream-100 focus:outline-none">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                @php $unreadMobile = auth()->user()->unreadNotifications()->count(); @endphp
                @if ($unreadMobile > 0)
                    <span class="absolute right-0.5 top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-none text-white">{{ $unreadMobile > 99 ? '99+' : $unreadMobile }}</span>
                @endif
            </a>
            <button @click="open = !open"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-brand-700 hover:bg-cream-100 focus:outline-none"
                    aria-label="Buka menu">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Sidebar desktop (selalu tampil di layar lg ke atas) --}}
    <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-cream-200 bg-white lg:flex">
        @include('layouts._sidebar')
    </aside>

    {{-- Drawer mobile + backdrop --}}
    <div x-cloak x-show="open"
         @click="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-gray-900/50 lg:hidden">
    </div>

    <aside x-cloak x-show="open"
           x-transition:enter="transition transform ease-out duration-200"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition transform ease-in duration-150"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-50 w-64 shadow-xl lg:hidden">
        @include('layouts._sidebar')
    </aside>
</div>