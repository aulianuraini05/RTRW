<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">Notifikasi</h2>
            @if (auth()->user()->unreadNotifications()->count() > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Tandai semua dibaca</button>
                </form>
            @endif
        </div>
    </x-slot>

    <div>
        <div class="space-y-3">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            @forelse ($notifications as $notification)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="block w-full rounded-lg bg-white p-4 text-left shadow-sm ring-1 transition hover:ring-indigo-300 {{ is_null($notification->read_at) ? 'ring-indigo-200 bg-indigo-50/40' : 'ring-transparent' }}">
                        <div class="flex items-start gap-3">
                            <span class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ is_null($notification->read_at) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-500' }}">
                                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-center gap-2">
                                    <span class="truncate text-sm font-semibold text-gray-900">{{ $notification->data['title'] ?? 'Notifikasi' }}</span>
                                    @if (is_null($notification->read_at))
                                        <span class="h-2 w-2 shrink-0 rounded-full bg-indigo-600"></span>
                                    @endif
                                </span>
                                <span class="mt-0.5 block text-sm text-gray-600">{{ $notification->data['message'] ?? '' }}</span>
                                <span class="mt-1 block text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }} • {{ $notification->created_at->translatedFormat('d F Y H:i') }} WIB</span>
                            </span>
                        </div>
                    </button>
                </form>
            @empty
                <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                    Belum ada notifikasi.
                </div>
            @endforelse

            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
