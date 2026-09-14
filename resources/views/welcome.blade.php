<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>laravel</title>
    <meta name="description" content="SIWARGA: satu sistem untuk mengelola lingkungan — layanan warga dan ringkasan administrasi dalam satu landing page.">
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
                    colors: {
                        brand: { dark: '#23444D', rust: '#C05634', gold: '#D4A843', sage: '#7A9A7E', ink: '#1a2e35' },
                    },
                },
            },
        }
    </script>
    <style>
        /* ── 1. SMOOTH SCROLL (inti permintaan) ─────────────────── */
        html {
            scroll-behavior: smooth;
            /* offset agar section tidak tertutup sticky navbar (h-16 = 64px + margin) */
            scroll-padding-top: 84px;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        html::-webkit-scrollbar { display: none; }
        body { font-family: 'Inter', sans-serif; }

        /* Setiap section diberi margin aman saat di-anchor */
        section[id] { scroll-margin-top: 84px; }

        /* ── 2. NAVBAR ACTIVE STATE ─────────────────────────────── */
        .nav-link {
            position: relative;
            color: #1f2937;
            transition: color .2s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            left: 0; bottom: -6px;
            width: 100%; height: 2px;
            background: #C05634;
            border-radius: 999px;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .25s ease;
        }
        .nav-link:hover { color: #C05634; }
        .nav-link.active { color: #C05634; }
        .nav-link.active::after { transform: scaleX(1); }

        /* ── 3. HERO SVG ANIMATION (bawaan desain lama) ─────────── */
        .draw-line {
            stroke-dasharray: 200;
            stroke-dashoffset: 200;
            animation: drawLine 1s ease-out forwards;
        }
        .draw-line.d1 { animation-delay: 0.1s; }
        .draw-line.d2 { animation-delay: 0.35s; }
        .draw-line.d3 { animation-delay: 0.6s; }
        .draw-line.d4 { animation-delay: 0.85s; }
        @keyframes drawLine { to { stroke-dashoffset: 0; } }

        .dot-appear { opacity: 0; animation: dotFadeIn 0.4s ease-out forwards; }
        .dot-appear.d1 { animation-delay: 0.8s; }
        .dot-appear.d2 { animation-delay: 1.05s; }
        .dot-appear.d3 { animation-delay: 1.3s; }
        .dot-appear.d4 { animation-delay: 1.55s; }
        .dot-appear.d5 { animation-delay: 0.6s; }
        @keyframes dotFadeIn { to { opacity: 1; } }

        /* ── 4. REVEAL ON SCROLL ────────────────────────────────── */
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity .7s ease, transform .7s ease; }
        .reveal.visible { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .reveal { opacity: 1; transform: none; transition: none; }
            .draw-line, .dot-appear { animation: none; opacity: 1; stroke-dashoffset: 0; }
        }

        /* ── 5. MISC ────────────────────────────────────────────── */
        .card-glow { filter: blur(50px) !important; opacity: 0.14 !important; pointer-events: none; }
        #navbar { transition: box-shadow .25s ease, background-color .25s ease; }
        #navbar.scrolled { box-shadow: 0 4px 24px -8px rgba(0,0,0,.12); }
        #back-to-top { transition: opacity .3s ease, transform .3s ease; opacity: 0; pointer-events: none; transform: translateY(12px); }
        #back-to-top.show { opacity: 1; pointer-events: auto; transform: none; }

        /* Mobile drawer */
        #mobile-menu { transition: opacity .25s ease, transform .25s ease; transform-origin: top; }
        #mobile-menu.hidden-menu { opacity: 0; transform: scaleY(.96) translateY(-6px); pointer-events: none; }
    </style>
</head>
<body class="bg-[#F3F5F4] min-h-screen text-gray-900 antialiased">

    <!-- ═══════════════════════════════════════════════════════════
         NAVBAR — urutan kiri→kanan = urutan section atas→bawah:
         Home (#home) → Administrasi (#administrasi) → Layanan (#layanan)
         ═══════════════════════════════════════════════════════════ -->
    <header id="navbar" class="w-full border-b border-gray-200 bg-white/95 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="#home" class="flex flex-col nav-anchor" aria-label="SIWARGA ke Home">
                <span class="font-serif text-2xl font-bold text-gray-900 tracking-wide leading-none">SIWARGA</span>
                <span class="text-xs text-gray-400 font-sans mt-1">sistem informasi RW10</span>
            </a>

            <div class="flex items-center">
                <nav class="hidden md:flex items-center space-x-8 mr-10" aria-label="Navigasi utama">
                    <a href="#home"         class="nav-link text-sm font-semibold font-sans" data-nav="home">Home</a>
                    <a href="#administrasi" class="nav-link text-sm font-semibold font-sans" data-nav="administrasi">Administrasi</a>
                    <a href="#layanan"      class="nav-link text-sm font-semibold font-sans" data-nav="layanan">Layanan</a>
                </nav>

                @if (Route::has('login'))
                    <div class="hidden sm:flex items-center space-x-3">
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

                <!-- Hamburger (mobile) -->
                <button id="hamburger" class="md:hidden ml-3 p-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50" aria-label="Buka menu" aria-expanded="false">
                    <svg id="icon-open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    <svg id="icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile drawer: urutan SAMA dengan desktop -->
        <div id="mobile-menu" class="hidden-menu md:hidden border-t border-gray-100 bg-white px-6 py-4">
            <nav class="flex flex-col space-y-1" aria-label="Navigasi mobile">
                <a href="#home" class="nav-link nav-anchor text-sm font-semibold font-sans py-2.5 border-b border-gray-50" data-nav="home">Home</a>
                <a href="#administrasi" class="nav-link nav-anchor text-sm font-semibold font-sans py-2.5 border-b border-gray-50" data-nav="administrasi">Administrasi</a>
                <a href="#layanan" class="nav-link nav-anchor text-sm font-semibold font-sans py-2.5" data-nav="layanan">Layanan</a>
                <div class="flex gap-3 pt-3 sm:hidden">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-700 text-gray-700">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2 text-sm font-medium rounded-lg bg-[#C05634] text-white">Register</a>
                        @endif
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <main>
        <!-- ═══════════════════════════════════════════════════════
             SECTION 1 — HOME / HERO (id="home")
             ═══════════════════════════════════════════════════════ -->
        <section id="home" class="max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="space-y-6 reveal">
                    <h1 class="font-serif text-4xl sm:text-5xl lg:text-5xl font-bold text-gray-900 leading-tight">
                        Satu sistem untuk mengelola lingkungan Anda.
                    </h1>
                    <p class="text-lg text-gray-600 font-sans max-w-lg leading-relaxed">
                        Semua urusan kas, data warga, dan informasi lingkungan, tercatat rapi dan mudah diakses.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-block px-8 py-3.5 bg-[#23444D] text-white font-sans font-medium text-sm rounded-lg hover:bg-[#1a353d] transition">Masuk ke Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-block px-8 py-3.5 bg-[#23444D] text-white font-sans font-medium text-sm rounded-lg hover:bg-[#1a353d] transition">Masuk ke Dashboard</a>
                            @endauth
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-center reveal">
                    <svg viewBox="0 0 400 400" class="w-full max-w-sm h-auto" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustrasi jaringan warga">
                        <line x1="200" y1="200" x2="60" y2="60" stroke="#2D3748" stroke-width="2" opacity="0.6" class="draw-line d1"/>
                        <line x1="200" y1="200" x2="340" y2="80" stroke="#2D3748" stroke-width="2" opacity="0.6" class="draw-line d2"/>
                        <line x1="200" y1="200" x2="80" y2="340" stroke="#2D3748" stroke-width="2" opacity="0.6" class="draw-line d3"/>
                        <line x1="200" y1="200" x2="330" y2="320" stroke="#2D3748" stroke-width="2" opacity="0.6" class="draw-line d4"/>
                        <circle cx="200" cy="200" r="14" fill="#1a1a1a" class="dot-appear d5"/>
                        <circle cx="60" cy="60" r="20" fill="#C05634" class="dot-appear d1"/>
                        <circle cx="340" cy="80" r="20" fill="#23444D" class="dot-appear d2"/>
                        <circle cx="80" cy="340" r="20" fill="#D4A843" class="dot-appear d3"/>
                        <circle cx="330" cy="320" r="20" fill="#7A9A7E" class="dot-appear d4"/>
                    </svg>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════
             SECTION 2 — ADMINISTRASI (id="administrasi")
             ═══════════════════════════════════════════════════════ -->
        <section id="administrasi" class="max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-24">
            <div class="space-y-6 reveal">
                <p class="text-xs font-bold tracking-[0.2em] text-[#C05634] font-sans uppercase">02 — Administrasi</p>
                <h2 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                    Ringkasan aset,<br>kas, dan iuran<br>warga.
                </h2>
                <p class="text-base text-gray-600 font-sans max-w-lg leading-relaxed">
                    Pantau kondisi aset, kas, iuran warga di lingkungan Anda dalam satu halaman. Sebagai tamu, ringkasannya bisa langsung dilihat<br>- masuk untuk membuka detail dan riwayat masing-masing.
                </p>
            </div>

            @guest
                <div class="max-w-lg mt-6 bg-yellow-50 border border-yellow-200 rounded-xl px-6 py-4 flex items-start gap-3 reveal">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-sans text-yellow-800">
                        Anda belum masuk, data ditampilkan terbatas. <a href="{{ route('login') }}" class="font-semibold underline hover:text-yellow-900 transition">Masuk sekarang</a> untuk mengakses seluruh fitur.
                    </p>
                </div>
            @endguest

            {{-- GRID 3 CARD RINGKASAN --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">

                {{-- CARD 1: ASET --}}
                <div class="reveal bg-white rounded-[18px] shadow-[0_2px_20px_-4px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden relative p-6 flex flex-col justify-between">
                    <div class="absolute -top-[60px] -right-[60px] w-[200px] h-[200px] rounded-full card-glow" style="background:#3E6B52;"></div>
                    <div>
                        <div class="flex items-center gap-2.5 mb-1">
                            <div class="w-[34px] h-[34px] rounded-[10px] flex items-center justify-center shrink-0" style="background:#3E6B52">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            </div>
                            <p class="text-sm font-sans font-medium text-gray-500">Aset Lingkungan</p>
                        </div>
                        <p class="font-serif text-3xl font-bold text-gray-900">{{ $totalAset ?? 0 }} Unit</p>
                    </div>
                    @php
                        $totalAsetSafe = max($totalAset ?? 0, 1);
                        $pBaik = (($asetBaik ?? 0) / $totalAsetSafe) * 100;
                        $pRingan = (($asetRusakRingan ?? 0) / $totalAsetSafe) * 100;
                        $pBerat = (($asetRusakBerat ?? 0) / $totalAsetSafe) * 100;
                    @endphp
                    <div class="mt-4 flex rounded-full overflow-hidden h-3 bg-gray-100">
                        @if(($asetBaik ?? 0) > 0)
                            <div class="bg-[#3E6B52] transition-all duration-500" style="width: {{ $pBaik }}%"></div>
                        @endif
                        @if(($asetRusakRingan ?? 0) > 0)
                            <div class="bg-[#D6A13B] transition-all duration-500" style="width: {{ $pRingan }}%"></div>
                        @endif
                        @if(($asetRusakBerat ?? 0) > 0)
                            <div class="bg-[#B9502C] transition-all duration-500" style="width: {{ $pBerat }}%"></div>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2 mt-4 text-sm font-sans">
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-[#3E6B52] shrink-0"></span><span class="text-gray-600">Kondisi baik — {{ $asetBaik ?? 0 }}</span></div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-[#D6A13B] shrink-0"></span><span class="text-gray-600">Rusak ringan — {{ $asetRusakRingan ?? 0 }}</span></div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-[#B9502C] shrink-0"></span><span class="text-gray-600">Perlu perbaikan — {{ $asetRusakBerat ?? 0 }}</span></div>
                    </div>
                </div>

                {{-- CARD 2: KAS --}}
                <div class="reveal bg-white rounded-[18px] shadow-[0_2px_20px_-4px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden relative p-6 flex flex-col justify-between">
                    <div class="absolute -top-[60px] -right-[60px] w-[200px] h-[200px] rounded-full card-glow" style="background:#B9502C;"></div>
                    <div>
                        <div class="flex items-center gap-2.5 mb-1">
                            <div class="w-[34px] h-[34px] rounded-[10px] flex items-center justify-center shrink-0" style="background:#B9502C">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                            </div>
                            <p class="text-sm font-sans font-medium text-gray-500">Kas RW</p>
                        </div>
                        <p class="font-serif text-3xl font-bold text-gray-900">Rp {{ number_format($totalKas ?? 0, 0, ',', '.') }}</p>
                        @if(($persentaseKas ?? 0) >= 0)
                            <span class="inline-flex items-center gap-1 mt-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-[#C05634]/10 text-[#C05634]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                                {{ $persentaseKas ?? 0 }}%
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 mt-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-600">
                                <svg class="w-3 h-3 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                                {{ abs($persentaseKas ?? 0) }}%
                            </span>
                        @endif
                    </div>
                    @php
                        $riwayat = $riwayatKas ?? collect();
                        $maxVal = $riwayat->max('total') ?: 1;
                        $chartW = 280; $chartH = 80; $padX = 10; $padY = 8;
                        $innerW = $chartW - ($padX * 2); $innerH = $chartH - ($padY * 2);
                        $points = []; $areaPoints = [];
                        foreach ($riwayat as $i => $row) {
                            $x = $padX + ($i / max(count($riwayat) - 1, 1)) * $innerW;
                            $y = $chartH - $padY - ($row['total'] / $maxVal) * $innerH;
                            $points[] = round($x, 1) . ',' . round($y, 1);
                            $areaPoints[] = round($x, 1) . ',' . round($y, 1);
                        }
                        $polyline = implode(' ', $points);
                        $areaPath = count($areaPoints) ? 'M' . $padX . ',' . $chartH . ' L' . implode(' L', $areaPoints) . ' L' . ($padX + $innerW) . ',' . $chartH . ' Z' : '';
                    @endphp
                    <div class="mt-4">
                        <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" class="w-full h-20" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="kasGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#C05634" stop-opacity="0.3"/>
                                    <stop offset="100%" stop-color="#C05634" stop-opacity="0.02"/>
                                </linearGradient>
                            </defs>
                            @if($areaPath)<path d="{{ $areaPath }}" fill="url(#kasGrad)"/>@endif
                            @if($polyline)<polyline points="{{ $polyline }}" fill="none" stroke="#C05634" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>@endif
                            @foreach($points as $pt)
                                @php $coords = explode(',', $pt); @endphp
                                <circle cx="{{ $coords[0] }}" cy="{{ $coords[1] }}" r="2.5" fill="#C05634"/>
                            @endforeach
                        </svg>
                        <div class="flex justify-between mt-1">
                            @foreach($riwayat as $row)
                                <span class="text-[10px] text-gray-400 font-sans">{{ $row['label'] }}</span>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 font-sans mt-3">Saldo naik enam bulan berturut-turut.</p>
                </div>

                {{-- CARD 3: IURAN --}}
                <div class="reveal bg-white rounded-[18px] shadow-[0_2px_20px_-4px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden relative p-6 flex flex-col justify-between">
                    <div class="absolute -top-[60px] -right-[60px] w-[200px] h-[200px] rounded-full card-glow" style="background:#D6A13B;"></div>
                    <div>
                        <div class="flex items-center gap-2.5 mb-1">
                            <div class="w-[34px] h-[34px] rounded-[10px] flex items-center justify-center shrink-0" style="background:#D6A13B">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <p class="text-sm font-sans font-medium text-gray-500">Iuran Warga</p>
                        </div>
                        <p class="font-serif text-3xl font-bold text-gray-900">{{ $persentaseIuran ?? 0 }}%</p>
                    </div>
                    <div class="flex items-center gap-5 mt-4">
                        @php
                            $pct = $persentaseIuran ?? 0;
                            $donutR = 38; $donutCirc = 2 * pi() * $donutR;
                            $donutOffset = $donutCirc * (1 - $pct / 100);
                        @endphp
                        <div class="relative shrink-0">
                            <svg width="96" height="96" viewBox="0 0 96 96">
                                <circle cx="48" cy="48" r="{{ $donutR }}" fill="none" stroke="#F3F5F4" stroke-width="8"/>
                                <circle cx="48" cy="48" r="{{ $donutR }}" fill="none" stroke="#C05634" stroke-width="8"
                                    stroke-dasharray="{{ $donutCirc }}" stroke-dashoffset="{{ $donutOffset }}"
                                    stroke-linecap="round" transform="rotate(-90 48 48)" class="transition-all duration-700"/>
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center font-serif text-lg font-bold text-gray-900">{{ $pct }}%</span>
                        </div>
                        <div class="flex flex-col gap-2 text-sm font-sans">
                            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-[#C05634] shrink-0"></span><span class="text-gray-600">{{ $kkSudahBayar ?? 0 }} KK sudah membayar</span></div>
                            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full border-2 border-gray-300 shrink-0"></span><span class="text-gray-600">{{ $kkBelumBayar ?? 0 }} KK belum, jatuh tempo 5 Sep</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA strip -->
            <div class="reveal mt-10 rounded-2xl bg-[#23444D] text-white px-8 py-8 sm:px-10 flex flex-col sm:flex-row sm:items-center gap-5 justify-between">
                <div>
                    <h3 class="font-serif text-2xl font-bold">Butuh detail &amp; riwayat lengkap?</h3>
                    <p class="text-sm text-white/70 font-sans mt-1">Masuk untuk membuka dashboard kas, iuran, aset, surat, dan aspirasi.</p>
                </div>
                <div class="flex flex-wrap gap-3 shrink-0">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-3 bg-white text-[#23444D] text-sm font-semibold rounded-lg hover:bg-gray-100 transition">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-3 bg-white text-[#23444D] text-sm font-semibold rounded-lg hover:bg-gray-100 transition">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-3 bg-[#C05634] text-white text-sm font-semibold rounded-lg hover:bg-[#a8482b] transition">Daftar</a>
                        @endif
                    @endauth
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════
             SECTION 3 — LAYANAN (id="layanan")
             ═══════════════════════════════════════════════════════ -->
        <section id="layanan" class="relative overflow-hidden bg-white border-y border-gray-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-24 relative">
                {{-- Watermark --}}
                <div class="absolute top-0 left-1/2 -translate-x-1/2 select-none pointer-events-none" aria-hidden="true">
                    <span class="block font-serif text-[110px] sm:text-[150px] lg:text-[190px] font-bold text-gray-200/60 leading-none tracking-tight mt-6">LAYANAN</span>
                </div>

                <div class="relative z-10 reveal">
                    <p class="text-xs font-bold tracking-[0.2em] text-[#C05634] font-sans uppercase">03 — Layanan</p>
                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-[#1a2e35] leading-tight max-w-2xl mt-3">
                        Semua layanan<br>warga dalam satu<br>tempat.
                    </h2>
                    <p class="mt-5 text-sm sm:text-base text-gray-500 font-sans max-w-lg leading-relaxed">
                        Warga terhubung, lingkungan terlindungi. Rasakan kemudahan berinteraksi, bertransaksi, dan menjaga lingkungan sekitar dalam satu website pintar. Masuk sekarang untuk mulai terhubung.
                    </p>

                    {{-- Kategori pills (bawaan desain lama) --}}
                    <div class="flex flex-wrap gap-3 mt-8">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#F3F5F4] border border-gray-200 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#B9502C] shrink-0"></span>
                            <span class="text-sm font-sans font-medium text-gray-700">Marketplace</span>
                        </span>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#F3F5F4] border border-gray-200 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#D6A13B] shrink-0"></span>
                            <span class="text-sm font-sans font-medium text-gray-700">Layanan Surat</span>
                        </span>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#F3F5F4] border border-gray-200 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#3E6B52] shrink-0"></span>
                            <span class="text-sm font-sans font-medium text-gray-700">Peminjaman Fasilitas</span>
                        </span>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#F3F5F4] border border-gray-200 shadow-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#1a2e35] shrink-0"></span>
                            <span class="text-sm font-sans font-medium text-gray-700">Pengaduan &amp; Aspirasi</span>
                        </span>
                    </div>
                </div>

                {{-- Kartu layanan → masuk ke modul (perlu login) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-10 relative z-10">
                    <a href="{{ route('login') }}" class="reveal group bg-[#F3F5F4] rounded-2xl border border-gray-100 p-6 hover:shadow-lg hover:-translate-y-1 transition">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white mb-4" style="background:#B9502C">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        </div>
                        <h3 class="font-sans font-semibold text-gray-900">Marketplace / UMKM</h3>
                        <p class="text-sm text-gray-500 font-sans mt-1.5 leading-relaxed">Daftarkan dagangan &amp; beli produk tetangga via WhatsApp.</p>
                        <span class="inline-flex items-center gap-1 text-sm font-semibold text-[#B9502C] mt-3 group-hover:gap-2 transition-all">Buka →</span>
                    </a>
                    <a href="{{ route('login') }}" class="reveal group bg-[#F3F5F4] rounded-2xl border border-gray-100 p-6 hover:shadow-lg hover:-translate-y-1 transition">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white mb-4" style="background:#D6A13B">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <h3 class="font-sans font-semibold text-gray-900">Layanan Surat</h3>
                        <p class="text-sm text-gray-500 font-sans mt-1.5 leading-relaxed">Ajukan surat domisili, SKTM, dll. secara digital.</p>
                        <span class="inline-flex items-center gap-1 text-sm font-semibold text-[#B9502C] mt-3 group-hover:gap-2 transition-all">Buka →</span>
                    </a>
                    <a href="{{ route('login') }}" class="reveal group bg-[#F3F5F4] rounded-2xl border border-gray-100 p-6 hover:shadow-lg hover:-translate-y-1 transition">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white mb-4" style="background:#3E6B52">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        </div>
                        <h3 class="font-sans font-semibold text-gray-900">Peminjaman Fasilitas</h3>
                        <p class="text-sm text-gray-500 font-sans mt-1.5 leading-relaxed">Pinjam tenda, kursi, sound &amp; aset lingkungan.</p>
                        <span class="inline-flex items-center gap-1 text-sm font-semibold text-[#B9502C] mt-3 group-hover:gap-2 transition-all">Buka →</span>
                    </a>
                    <a href="{{ route('login') }}" class="reveal group bg-[#F3F5F4] rounded-2xl border border-gray-100 p-6 hover:shadow-lg hover:-translate-y-1 transition">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white mb-4" style="background:#1a2e35">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <h3 class="font-sans font-semibold text-gray-900">Pengaduan &amp; Aspirasi</h3>
                        <p class="text-sm text-gray-500 font-sans mt-1.5 leading-relaxed">Laporkan masalah &amp; pantau status penanganannya.</p>
                        <span class="inline-flex items-center gap-1 text-sm font-semibold text-[#B9502C] mt-3 group-hover:gap-2 transition-all">Buka →</span>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex flex-col">
                <span class="font-serif text-xl font-bold text-gray-900">SIWARGA</span>
                <span class="text-xs text-gray-400 font-sans">sistem informasi RW10</span>
            </div>
            <nav class="flex items-center space-x-6" aria-label="Navigasi footer">
                <a href="#home" class="nav-anchor text-sm font-sans text-gray-500 hover:text-[#C05634] transition">Home</a>
                <a href="#administrasi" class="nav-anchor text-sm font-sans text-gray-500 hover:text-[#C05634] transition">Administrasi</a>
                <a href="#layanan" class="nav-anchor text-sm font-sans text-gray-500 hover:text-[#C05634] transition">Layanan</a>
            </nav>
            <p class="text-xs text-gray-400 font-sans">© {{ date('Y') }} SIWARGA RW10. All rights reserved.</p>
        </div>
    </footer>

    <!-- Back to top -->
    <a href="#home" id="back-to-top" class="nav-anchor fixed bottom-6 right-6 z-50 w-11 h-11 rounded-full bg-[#23444D] text-white flex items-center justify-center shadow-lg hover:bg-[#1a353d]" aria-label="Kembali ke atas">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
    </a>

    <script>
    (function () {
        'use strict';

        /* ── A. Smooth scroll manual (tanpa reload, offset presisi) ──
           CSS scroll-behavior:smooth sudah menangani klik anchor,
           JS ini memastikan offset sticky header & menutup drawer mobile. */
        var HEADER_OFFSET = 72;
        var mobileMenu = document.getElementById('mobile-menu');
        var hamburger = document.getElementById('hamburger');
        var iconOpen = document.getElementById('icon-open');
        var iconClose = document.getElementById('icon-close');

        function closeMobileMenu() {
            if (!mobileMenu) return;
            mobileMenu.classList.add('hidden-menu');
            if (hamburger) hamburger.setAttribute('aria-expanded', 'false');
            if (iconOpen) iconOpen.classList.remove('hidden');
            if (iconClose) iconClose.classList.add('hidden');
        }

        document.addEventListener('click', function (e) {
            var link = e.target.closest('a.nav-anchor[href^="#"]');
            if (!link) return;
            var id = link.getAttribute('href');
            if (!id || id === '#') return;
            var target = document.querySelector(id);
            if (!target) return;
            e.preventDefault(); // cegah reload / jump kasar
            closeMobileMenu();
            var top = target.getBoundingClientRect().top + window.pageYOffset - HEADER_OFFSET;
            window.history.replaceState(null, '', id); // update URL tanpa reload
            window.scrollTo({ top: Math.max(top, 0), behavior: 'smooth' });
        });

        /* ── B. Hamburger toggle ── */
        if (hamburger && mobileMenu) {
            hamburger.addEventListener('click', function () {
                var isClosed = mobileMenu.classList.contains('hidden-menu');
                if (isClosed) {
                    mobileMenu.classList.remove('hidden-menu');
                    hamburger.setAttribute('aria-expanded', 'true');
                    iconOpen.classList.add('hidden');
                    iconClose.classList.remove('hidden');
                } else {
                    closeMobileMenu();
                }
            });
        }

        /* ── C. Active menu state saat scroll (IntersectionObserver) ── */
        var sections = ['home', 'administrasi', 'layanan']
            .map(function (id) { return document.getElementById(id); })
            .filter(Boolean);

        function setActive(id) {
            document.querySelectorAll('.nav-link[data-nav]').forEach(function (a) {
                a.classList.toggle('active', a.getAttribute('data-nav') === id);
            });
        }

        if ('IntersectionObserver' in window && sections.length) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) setActive(entry.target.id);
                });
            }, { rootMargin: '-40% 0px -55% 0px', threshold: 0 });
            sections.forEach(function (s) { observer.observe(s); });
        } else {
            // Fallback: hitung posisi scroll manual
            window.addEventListener('scroll', function () {
                var current = 'home';
                sections.forEach(function (s) {
                    if (window.pageYOffset >= s.offsetTop - 120) current = s.id;
                });
                setActive(current);
            }, { passive: true });
        }

        /* ── D. Navbar shadow + tombol back-to-top ── */
        var navbar = document.getElementById('navbar');
        var backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', function () {
            var y = window.pageYOffset || document.documentElement.scrollTop;
            if (navbar) navbar.classList.toggle('scrolled', y > 8);
            if (backToTop) backToTop.classList.toggle('show', y > 600);
        }, { passive: true });

        /* ── E. Reveal on scroll ── */
        var revealEls = document.querySelectorAll('.reveal');
        if ('IntersectionObserver' in window) {
            var revealObs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        revealObs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            revealEls.forEach(function (el) { revealObs.observe(el); });
        } else {
            revealEls.forEach(function (el) { el.classList.add('visible'); });
        }

        /* ── F. Jika URL dibuka langsung dengan hash (/#layanan) ── */
        if (window.location.hash) {
            var initial = document.querySelector(window.location.hash);
            if (initial) {
                setTimeout(function () {
                    var top = initial.getBoundingClientRect().top + window.pageYOffset - HEADER_OFFSET;
                    window.scrollTo({ top: Math.max(top, 0), behavior: 'smooth' });
                }, 100);
            }
        } else {
            setActive('home');
        }
    })();
    </script>

</body>
</html>
