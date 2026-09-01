<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SIWARGA') }}</title>
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

        /* Stroke draw animation */
        .draw-line {
            stroke-dasharray: 200;
            stroke-dashoffset: 200;
            animation: drawLine 1s ease-out forwards;
        }
        .draw-line.d1 { animation-delay: 0.1s; }
        .draw-line.d2 { animation-delay: 0.35s; }
        .draw-line.d3 { animation-delay: 0.6s; }
        .draw-line.d4 { animation-delay: 0.85s; }

        @keyframes drawLine {
            to { stroke-dashoffset: 0; }
        }

        /* Circle fade-in */
        .dot-appear {
            opacity: 0;
            animation: dotFadeIn 0.4s ease-out forwards;
        }
        .dot-appear.d1 { animation-delay: 0.8s; }
        .dot-appear.d2 { animation-delay: 1.05s; }
        .dot-appear.d3 { animation-delay: 1.3s; }
        .dot-appear.d4 { animation-delay: 1.55s; }
        .dot-appear.d5 { animation-delay: 0.6s; }

        @keyframes dotFadeIn {
            to { opacity: 1; }
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
                    <a href="#" class="text-sm font-semibold text-gray-800 font-sans hover:text-gray-600 transition">Layanan</a>
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
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="space-y-6">
                    <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                        Satu sistem untuk mengelola lingkungan Anda.
                    </h1>
                    <p class="text-lg text-gray-600 font-sans max-w-lg leading-relaxed">
                        Semua urusan kas, data warga, dan informasi lingkungan, tercatat rapi dan mudah diakses.
                    </p>
                    <div>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-block px-8 py-3.5 bg-[#23444D] text-white font-sans font-medium text-sm rounded-lg hover:bg-[#1a353d] transition">Masuk ke Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-block px-8 py-3.5 bg-[#23444D] text-white font-sans font-medium text-sm rounded-lg hover:bg-[#1a353d] transition">Masuk ke Dashboard</a>
                            @endauth
                        @else
                            <a href="/" class="inline-block px-8 py-3.5 bg-[#23444D] text-white font-sans font-medium text-sm rounded-lg hover:bg-[#1a353d] transition">Masuk ke Dashboard</a>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <svg viewBox="0 0 400 400" class="w-full max-w-md h-auto" xmlns="http://www.w3.org/2000/svg">
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

                        /* replay SVG draw animations */
                        content.querySelectorAll('.draw-line, .dot-appear').forEach(function (el) {
                            el.style.animation = 'none';
                            el.offsetHeight;
                            el.style.animation = '';
                        });

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
