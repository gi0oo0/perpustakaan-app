<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Perpustakaan Sekolah') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <script>
            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
            }
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="antialiased bg-night text-white min-h-screen font-body">
        <div class="relative min-h-screen overflow-x-clip flex flex-col items-center justify-center px-4 py-10">
            {{-- Subtle background --}}
            <div class="fixed inset-0 -z-10 bg-night overflow-hidden" aria-hidden="true">
                <div class="absolute -top-44 -left-32 w-[36rem] h-[36rem] rounded-full bg-primary/[0.05] blur-[130px]"></div>
                <div class="absolute -bottom-52 -right-36 w-[38rem] h-[38rem] rounded-full bg-primary/[0.04] blur-[140px]"></div>
            </div>

            <div class="w-full max-w-[440px] animate-fade-up">
                <div class="glass w-full rounded-2xl p-6 sm:p-8 shadow-glass-lg">
                    {{ $slot }}
                </div>

                <p class="mt-8 text-center font-body text-xs text-white/40">© 2026 Sistem Manajemen Perpustakaan</p>
            </div>
        </div>
    </body>
</html>
