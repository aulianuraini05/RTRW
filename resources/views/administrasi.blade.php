<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrasi - {{ config('app.name', 'SIWARGA') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        html { -ms-overflow-style: none; scrollbar-width: none; }
        html::-webkit-scrollbar { display: none; }
        #app-content { transition: opacity 0.25s ease-in-out; }
        #app-content.fade-out { opacity: 0; }
        .card-glow {
            filter: blur(50px) !important;
            opacity: 0.14 !important;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-[#F3F5F4] min-h-screen">

    <header class="w-full border-b border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="/" class="flex flex-col" data-spa-link>
                <span class="font-serif text-2xl font-bold text-gray-900 tracking-wide">SIWARGA</span>
                <span class="text-xs text-gray-400 font-sans mt-1">sistem informasi RW10</span>
            </a>
            <div class="flex items-center">
                <nav class="hidden md:flex items-center space-x-8 mr-10">
                    <a href="/" class="text-sm font-semibold text-gray-800 font-sans hover:text-gray-600 transition" data-spa-link>Home</a>
                    <a href="/administrasi" class="text-sm font-semibold text-gray-800 font-sans hover:text-gray-600 transition" data-spa-link>Administrasi</a>
                    <a href="/layanan" class="text-sm font-semibold text-gray-800 font-sans hover:text-gray-600 transition" data-spa-link>Layanan</a>
                </nav>
                @if (Route::has('login'))
                    <div class="flex items-center space-x-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-700 text-gray-700 hover:bg-gray-100 transition">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-[#C05634] text-white hover:bg-[#a8482b] transition">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </header>

    <div id="app-content">
        <main class="max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-24">

            <div class="space-y-6">
                <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                    Ringkasan aset,<br>kas, dan iuran<br>warga.
                </h1>
                <p class="text-base text-gray-600 font-sans max-w-lg leading-relaxed">
                    Pantau kondisi aset, kas, iuran warga di lingkungan Anda dalam satu halaman. Sebagai tamu, ringkasannya bisa langsung dilihat<br>- masuk untuk membuka detail dan riwayat masing-masing.
                </p>
            </div>

            @guest
                <div class="max-w-lg mt-6 bg-yellow-50 border border-yellow-200 rounded-xl px-6 py-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-sans text-yellow-800">
                        Anda belum masuk, data ditampilkan terbatas. <a href="{{ route('login') }}" class="font-semibold underline hover:text-yellow-900 transition">Masuk sekarang</a> untuk mengakses seluruh fitur.
                    </p>
                </div>
            @endguest

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- GRID 3 CARD RINGKASAN                         --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">

                {{-- ── CARD 1: KAS RW ──────────────────────── --}}
                <div class="bg-white rounded-[18px] shadow-[0_2px_20px_-4px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden relative p-6 flex flex-col justify-between">
                    <div class="absolute -top-[60px] -right-[60px] w-[200px] h-[200px] rounded-full card-glow" style="background:#B9502C; filter:blur(50px); opacity:0.14; pointer-events:none;"></div>
                    <div>
                        <div class="flex items-center gap-2.5 mb-1">
                            <div class="w-[34px] h-[34px] rounded-[10px] flex items-center justify-center shrink-0" style="background:#B9502C">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                            </div>
                            <p class="text-sm font-sans font-medium text-gray-500">Kas RW</p>
                        </div>
                        <p class="font-serif text-3xl font-bold text-gray-900">Rp {{ number_format($totalKas, 0, ',', '.') }}</p>
                        @if($persentaseKas >= 0)
                            <span class="inline-flex items-center gap-1 mt-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-[#C05634]/10 text-[#C05634]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                                {{ $persentaseKas }}%
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 mt-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-600">
                                <svg class="w-3 h-3 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                                {{ abs($persentaseKas) }}%
                            </span>
                        @endif
                    </div>

                    {{-- Area Chart --}}
                    @php
                        $maxVal = $riwayatKas->max('total') ?: 1;
                        $chartW = 280;
                        $chartH = 80;
                        $padX = 10;
                        $padY = 8;
                        $innerW = $chartW - ($padX * 2);
                        $innerH = $chartH - ($padY * 2);
                        $points = [];
                        $areaPoints = [];
                        foreach ($riwayatKas as $i => $row) {
                            $x = $padX + ($i / max(count($riwayatKas) - 1, 1)) * $innerW;
                            $y = $chartH - $padY - ($row['total'] / $maxVal) * $innerH;
                            $points[] = round($x, 1) . ',' . round($y, 1);
                            $areaPoints[] = round($x, 1) . ',' . round($y, 1);
                        }
                        $areaFirst = $padX . ',' . $chartH;
                        $areaLast = ($padX + $innerW) . ',' . $chartH;
                        $polyline = implode(' ', $points);
                        $areaPath = 'M' . $areaFirst . ' L' . implode(' L', $areaPoints) . ' L' . $areaLast . ' Z';
                    @endphp
                    <div class="mt-4">
                        <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" class="w-full h-20" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="kasGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#C05634" stop-opacity="0.3"/>
                                    <stop offset="100%" stop-color="#C05634" stop-opacity="0.02"/>
                                </linearGradient>
                            </defs>
                            <path d="{{ $areaPath }}" fill="url(#kasGrad)"/>
                            <polyline points="{{ $polyline }}" fill="none" stroke="#C05634" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            @foreach($points as $pt)
                                @php $coords = explode(',', $pt); @endphp
                                <circle cx="{{ $coords[0] }}" cy="{{ $coords[1] }}" r="2.5" fill="#C05634"/>
                            @endforeach
                        </svg>
                        <div class="flex justify-between mt-1">
                            @foreach($riwayatKas as $row)
                                <span class="text-[10px] text-gray-400 font-sans">{{ $row['label'] }}</span>
                            @endforeach
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 font-sans mt-3">Saldo naik enam bulan berturut-turut.</p>
                </div>

                {{-- ── CARD 2: IURAN WARGA ────────────────── --}}
                <div class="bg-white rounded-[18px] shadow-[0_2px_20px_-4px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden relative p-6 flex flex-col justify-between">
                    <div class="absolute -top-[60px] -right-[60px] w-[200px] h-[200px] rounded-full card-glow" style="background:#D6A13B; filter:blur(50px); opacity:0.14; pointer-events:none;"></div>
                    <div>
                        <div class="flex items-center gap-2.5 mb-1">
                            <div class="w-[34px] h-[34px] rounded-[10px] flex items-center justify-center shrink-0" style="background:#D6A13B">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <p class="text-sm font-sans font-medium text-gray-500">Iuran Warga</p>
                        </div>
                        <p class="font-serif text-3xl font-bold text-gray-900">{{ $persentaseIuran }}%</p>
                    </div>

                    <div class="flex items-center gap-5 mt-4">
                        {{-- Donut Chart --}}
                        @php
                            $donutR = 38;
                            $donutCirc = 2 * pi() * $donutR;
                            $donutOffset = $donutCirc * (1 - $persentaseIuran / 100);
                        @endphp
                        <div class="relative shrink-0">
                            <svg width="96" height="96" viewBox="0 0 96 96">
                                <circle cx="48" cy="48" r="{{ $donutR }}" fill="none" stroke="#F3F5F4" stroke-width="8"/>
                                <circle cx="48" cy="48" r="{{ $donutR }}" fill="none" stroke="#C05634" stroke-width="8"
                                    stroke-dasharray="{{ $donutCirc }}"
                                    stroke-dashoffset="{{ $donutOffset }}"
                                    stroke-linecap="round"
                                    transform="rotate(-90 48 48)"
                                    class="transition-all duration-700"/>
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center font-serif text-lg font-bold text-gray-900">{{ $persentaseIuran }}%</span>
                        </div>

                        {{-- Legend --}}
                        <div class="flex flex-col gap-2 text-sm font-sans">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#C05634] shrink-0"></span>
                                <span class="text-gray-600">{{ $kkSudahBayar }} KK sudah membayar</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full border-2 border-gray-300 shrink-0"></span>
                                <span class="text-gray-600">{{ $kkBelumBayar }} KK belum, jatuh tempo 5 Sep</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── CARD 3: ASET LINGKUNGAN ────────────── --}}
                <div class="bg-white rounded-[18px] shadow-[0_2px_20px_-4px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden relative p-6 flex flex-col justify-between">
                    <div class="absolute -top-[60px] -right-[60px] w-[200px] h-[200px] rounded-full card-glow" style="background:#3E6B52; filter:blur(50px); opacity:0.14; pointer-events:none;"></div>
                    <div>
                        <div class="flex items-center gap-2.5 mb-1">
                            <div class="w-[34px] h-[34px] rounded-[10px] flex items-center justify-center shrink-0" style="background:#3E6B52">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            </div>
                            <p class="text-sm font-sans font-medium text-gray-500">Aset Lingkungan</p>
                        </div>
                        <p class="font-serif text-3xl font-bold text-gray-900">{{ $totalAset }} Unit</p>
                    </div>

                    {{-- Stacked Progress Bar --}}
                    @php
                        $totalAsetSafe = max($totalAset, 1);
                        $pBaik = ($asetBaik / $totalAsetSafe) * 100;
                        $pRingan = ($asetRusakRingan / $totalAsetSafe) * 100;
                        $pBerat = ($asetRusakBerat / $totalAsetSafe) * 100;
                    @endphp
                    <div class="mt-4 flex rounded-full overflow-hidden h-3 bg-gray-100">
                        @if($asetBaik > 0)
                            <div class="bg-[#3E6B52] transition-all duration-500" style="width: {{ $pBaik }}%"></div>
                        @endif
                        @if($asetRusakRingan > 0)
                            <div class="bg-[#D6A13B] transition-all duration-500" style="width: {{ $pRingan }}%"></div>
                        @endif
                        @if($asetRusakBerat > 0)
                            <div class="bg-[#B9502C] transition-all duration-500" style="width: {{ $pBerat }}%"></div>
                        @endif
                    </div>

                    {{-- Status List --}}
                    <div class="flex flex-col gap-2 mt-4 text-sm font-sans">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#3E6B52] shrink-0"></span>
                            <span class="text-gray-600">Kondisi baik — {{ $asetBaik }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D6A13B] shrink-0"></span>
                            <span class="text-gray-600">Rusak ringan — {{ $asetRusakRingan }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#B9502C] shrink-0"></span>
                            <span class="text-gray-600">Perlu perbaikan — {{ $asetRusakBerat }}</span>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script>
    (function () {
        function navigateTo(href) {
            var content = document.getElementById('app-content');
            content.classList.add('fade-out');

            setTimeout(function () {
                fetch(href, { headers: { 'X-SPA-Request': '1' } })
                    .then(function (r) { return r.text(); })
                    .then(function (html) {
                        var doc = new DOMParser().parseFromString(html, 'text/html');
                        var newContent = doc.getElementById('app-content');
                        if (newContent) content.innerHTML = newContent.innerHTML;

                        var newTitle = doc.querySelector('title');
                        if (newTitle) document.title = newTitle.textContent;

                        history.pushState({ path: href }, '', href);
                        content.classList.remove('fade-out');
                    })
                    .catch(function () { window.location.href = href; });
            }, 250);
        }

        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[data-spa-link]');
            if (!link) return;
            var href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('http') || href.startsWith('mailto:')) return;
            if (href === window.location.pathname) { e.preventDefault(); return; }
            e.preventDefault();
            navigateTo(href);
        });

        window.addEventListener('popstate', function () {
            navigateTo(window.location.pathname);
        });
    })();
    </script>

</body>
</html>
