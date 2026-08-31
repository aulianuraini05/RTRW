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
    </style>
</head>
<body class="bg-[#F3F5F4] min-h-screen">

    <header class="w-full border-b border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between h-16">
            <div class="flex flex-col">
                <a href="/" class="font-serif text-2xl font-bold text-gray-900 tracking-wide">SIWARGA</a>
                <span class="text-xs text-gray-400 font-sans mt-1">sistem informasi RW10</span>
            </div>
            <div class="flex items-center">
                <nav class="hidden md:flex items-center space-x-8 mr-10">
                    <a href="#" class="text-sm font-semibold text-gray-800 font-sans hover:text-gray-600 transition">Home</a>
                    <a href="#" class="text-sm font-semibold text-gray-800 font-sans hover:text-gray-600 transition">Administrasi</a>
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
                    <line x1="60" y1="60" x2="200" y2="200" stroke="#2D3748" stroke-width="2" opacity="0.6"/>
                    <line x1="340" y1="80" x2="200" y2="200" stroke="#2D3748" stroke-width="2" opacity="0.6"/>
                    <line x1="80" y1="340" x2="200" y2="200" stroke="#2D3748" stroke-width="2" opacity="0.6"/>
                    <line x1="330" y1="320" x2="200" y2="200" stroke="#2D3748" stroke-width="2" opacity="0.6"/>
                    <circle cx="60" cy="60" r="20" fill="#C05634"/>
                    <circle cx="340" cy="80" r="20" fill="#23444D"/>
                    <circle cx="80" cy="340" r="20" fill="#D4A843"/>
                    <circle cx="330" cy="320" r="20" fill="#7A9A7E"/>
                    <circle cx="200" cy="200" r="14" fill="#1a1a1a"/>
                </svg>
            </div>
        </div>
    </main>

</body>
</html>
