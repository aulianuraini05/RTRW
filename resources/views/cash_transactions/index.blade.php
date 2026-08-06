<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kas & Keuangan RT/RW</h2>
            @if (Auth::user()->isWarga())
                <a href="{{ route('cash_transactions.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Bayar Kas</a>
            @else
                <a href="{{ route('cash_transactions.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">+ Catat Pembayaran Kas</a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <!-- Summary Cards -->
            <div class="grid gap-6 sm:grid-cols-3">
                <div class="rounded-lg bg-indigo-600 p-6 text-white shadow-sm">
                    <p class="text-sm font-medium opacity-90">Sudah Lunas</p>
                    <p class="mt-2 text-3xl font-extrabold">{{ $totalPaid }} warga</p>
                    <p class="mt-1 text-xs opacity-75">Warga yang sudah membayar kas</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-yellow-500">
                    <p class="text-sm font-medium text-gray-500">Menunggu Pembayaran</p>
                    <p class="mt-2 text-2xl font-bold text-yellow-600">{{ $totalPending }} warga</p>
                    <p class="mt-1 text-xs text-gray-400">Warga yang status kas masih pending</p>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm border-l-4 border-gray-400">
                    <p class="text-sm font-medium text-gray-500">Total Catatan</p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">{{ $cashTransactions->total() }} catatan</p>
                    <p class="mt-1 text-xs text-gray-400">Seluruh data pembayaran kas tercatat</p>
                </div>
            </div>

            <!-- Search & Filter Bar -->
            <div class="rounded-lg bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('cash_transactions.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                        @if (Auth::user()->isAdmin())
                            <x-text-input type="text" name="search" placeholder="Cari nama warga..." :value="request('search')" class="w-full sm:max-w-xs" />
                        @endif
                        <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="lunas" @selected(request('status') === 'lunas')>Lunas</option>
                            <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
                        </select>
                        <x-primary-button type="submit">Filter</x-primary-button>
                        @if (request()->hasAny(['search', 'status']))
                            <a href="{{ route('cash_transactions.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Payment List -->
            <div class="space-y-3">
                @forelse ($cashTransactions as $transaction)
                    <article class="rounded-lg bg-white p-5 shadow-sm">
                        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                            <div class="flex-1">
                                <div class="mb-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                    <span class="rounded-full px-2.5 py-0.5 font-medium {{ $transaction->payment_status === 'lunas' ? 'bg-green-100 text-green-700' : ($transaction->payment_status === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ ucfirst($transaction->payment_status) }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $transaction->created_at->translatedFormat('d F Y') }}</span>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900">
                                    <a href="{{ route('cash_transactions.show', $transaction) }}" class="hover:text-indigo-600">
                                        {{ $transaction->user?->name ?? 'Warga lama' }}
                                    </a>
                                </h3>
                                @if ($transaction->proof_of_payment)
                                    <p class="mt-1 text-sm text-gray-500">Bukti: {{ $transaction->proof_of_payment }}</p>
                                @endif
                            </div>
                            @if (Auth::user()->isAdmin())
                                <div class="flex flex-wrap items-center gap-2 border-t pt-3 sm:border-l sm:border-t-0 sm:pl-4 sm:pt-0">
                                    @foreach (['pending', 'lunas', 'ditolak'] as $status)
                                        @php
                                            $isCurrent = $transaction->payment_status === $status;
                                            $classes = [
                                                'pending' => 'bg-yellow-500 hover:bg-yellow-400',
                                                'lunas' => 'bg-green-600 hover:bg-green-500',
                                                'ditolak' => 'bg-red-600 hover:bg-red-500',
                                            ];
                                        @endphp
                                        <form method="POST" action="{{ route('cash_transactions.status.update', $transaction) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="payment_status" value="{{ $status }}">
                                            <button type="submit" @disabled($isCurrent)
                                                class="rounded-md px-3 py-1.5 text-xs font-semibold text-white {{ $classes[$status] }}{{ $isCurrent ? ' opacity-40 cursor-not-allowed' : '' }}">
                                                {{ ucfirst($status) }}
                                            </button>
                                        </form>
                                    @endforeach
                                    <a href="{{ route('cash_transactions.edit', $transaction) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Edit</a>
                                    <form method="POST" action="{{ route('cash_transactions.destroy', $transaction) }}" onsubmit="return confirm('Hapus catatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">Hapus</button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('cash_transactions.show', $transaction) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat detail</a>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                        {{ Auth::user()->isAdmin() ? 'Belum ada catatan pembayaran kas warga.' : 'Anda belum memiliki catatan pembayaran kas.' }}
                        @if (Auth::user()->isWarga())
                            <a href="{{ route('cash_transactions.create') }}" class="mt-4 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Bayar Kas Sekarang</a>
                        @else
                            <a href="{{ route('cash_transactions.create') }}" class="mt-4 inline-block rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">+ Catat Pembayaran Kas Pertama</a>
                        @endif
                    </div>
                @endforelse

                {{ $cashTransactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
