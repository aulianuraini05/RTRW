<div x-data="{ open: false }">
    {{-- Top bar untuk layar kecil (mobile) --}}
    <div class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 lg:hidden">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <x-application-logo class="h-8 w-8 fill-indigo-600" />
            <span class="text-base font-bold text-gray-800">{{ config('app.name', 'Smart RT/RW') }}</span>
        </a>

        <button @click="open = !open"
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none"
                aria-label="Buka menu">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>

    {{-- Sidebar desktop (selalu tampil di layar lg ke atas) --}}
    <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-gray-200 bg-white lg:flex">
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