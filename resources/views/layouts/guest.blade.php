<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cream-100">
            <div>
                <a href="/" class="flex items-center gap-2.5">
                    <x-application-logo class="w-12 h-12 fill-brand-600" />
                    <span class="text-lg font-bold text-ink-800">{{ config('app.name', 'Smart RT/RW') }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-2xl border border-cream-200">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
