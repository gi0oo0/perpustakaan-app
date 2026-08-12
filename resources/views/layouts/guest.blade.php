<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Perpustakaan Sekolah') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <script>
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.add('light');
            }
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="antialiased text-white bg-night min-h-screen">
        <div class="relative min-h-screen overflow-x-clip">
            {{-- Aurora background --}}
            <div class="fixed inset-0 -z-10 bg-night overflow-hidden" aria-hidden="true">
                <div class="absolute -top-44 -left-32 w-[38rem] h-[38rem] rounded-full bg-primary/30 blur-[130px] animate-float"></div>
                <div class="absolute top-1/3 -right-44 w-[34rem] h-[34rem] rounded-full bg-accent/20 blur-[130px] animate-float-slow"></div>
                <div class="absolute bottom-[-10rem] left-1/4 w-[30rem] h-[30rem] rounded-full bg-violet/25 blur-[140px] animate-float-fast"></div>

                <div class="aurora-grid absolute inset-0 opacity-[0.035]"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-night/85"></div>
            </div>

            <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">
                {{-- Brand --}}
                <div class="mb-8 text-center animate-fade-up">
                    <div class="w-14 h-14 sm:w-20 sm:h-20 mx-auto rounded-2xl shadow-glow mb-3 sm:mb-4 overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Perpustakaan" class="w-full h-full object-cover">
                    </div>
                    <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-gradient">
                        Perpustakaan
                    </h1>
                    <p class="font-body text-sm text-white/45 mt-2">Sistem Manajemen Peminjaman Buku</p>
                </div>

                <div class="w-full sm:max-w-md glass px-5 py-6 sm:px-8 sm:py-8 animate-fade-up" style="animation-delay: 100ms;">
                    {{ $slot }}
                </div>

                <p class="mt-8 font-body text-xs text-white/30">© {{ date('Y') }} Sistem Manajemen Perpustakaan</p>
            </div>
        </div>
    </body>
</html>
