<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans text-gray-900 antialiased zeta-shell">
        @php
            $isRegisterPage = request()->routeIs('register');
        @endphp
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 zeta-shell">
            <div>
                <a href="/" class="flex flex-col items-center gap-2">
                    <x-application-logo variant="full" class="h-16 w-auto" />
                    <span class="zeta-brand-wordmark text-xs">REFRIGERACION ZETA</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 zeta-card overflow-hidden sm:rounded-lg {{ $isRegisterPage ? 'md:max-w-3xl lg:max-w-6xl' : '' }}">
                {{ $slot }}
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
