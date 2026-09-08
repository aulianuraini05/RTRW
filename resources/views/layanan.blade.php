<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layanan — {{ config('app.name', 'SIWARGA') }}</title>
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
        <main class="relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12 lg:py-20 relative">

                {{-- Watermark --}}
                <div class="absolute top-0 left-1/2 -translate-x-1/2 select-none pointer-events-none" aria-hidden="true">
                    <span class="block font-serif text-[120px] sm:text-[160px] lg:text-[200px] font-bold text-gray-200/60 leading-none tracking-tight mt-10">LAYANAN</span>
                </div>

                {{-- Heading --}}
                <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-[#1a2e35] leading-tight max-w-2xl relative z-10">
                    Semua layanan<br>warga dalam satu<br>tempat.
                </h1>

                {{-- Deskripsi --}}
                <p class="mt-5 text-sm sm:text-base text-gray-500 font-sans max-w-lg leading-relaxed relative z-10">
                    Warga terhubung, lingkungan terlindungi. Rasakan kemudahan berinteraksi, bertransaksi, dan menjaga lingkungan sekitar dalam satu website pintar. Masuk sekarang untuk mulai terhubung.
                </p>

                {{-- Kategori pills --}}
                <div class="flex flex-wrap gap-3 mt-8 relative z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#B9502C] shrink-0"></span>
                        <span class="text-sm font-sans font-medium text-gray-700">Marketplace</span>
                    </div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#D6A13B] shrink-0"></span>
                        <span class="text-sm font-sans font-medium text-gray-700">Layanan Surat</span>
                    </div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#3E6B52] shrink-0"></span>
                        <span class="text-sm font-sans font-medium text-gray-700">Peminjaman Fasilitas</span>
                    </div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1a2e35] shrink-0"></span>
                        <span class="text-sm font-sans font-medium text-gray-700">Pengaduan & Aspirasi</span>
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
