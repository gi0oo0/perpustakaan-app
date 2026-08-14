<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Perpustakaan Sekolah') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="antialiased bg-[#080B12] text-[#F4F7FB] min-h-screen font-body">
        <div class="relative min-h-screen overflow-x-clip flex flex-col items-center justify-center px-4 py-10">
            {{-- Ambient background --}}
            <div class="fixed inset-0 -z-10 bg-[#080B12] overflow-hidden" aria-hidden="true">
                <div class="absolute -top-44 -left-32 w-[36rem] h-[36rem] rounded-full bg-[#7C5CFF]/[0.13] blur-[130px]"></div>
                <div class="absolute -bottom-52 -right-36 w-[38rem] h-[38rem] rounded-full bg-[#22D3EE]/[0.07] blur-[140px]"></div>
                <div class="auth-grid absolute inset-0"></div>
            </div>

            <div class="w-full max-w-[440px] animate-fade-up">
                <div class="w-full rounded-[18px] bg-[#111722] border border-white/[0.08] p-6 sm:p-8 shadow-[inset_0_1px_0_rgba(255,255,255,0.04),0_18px_50px_-24px_rgba(0,0,0,0.75)]">
                    {{ $slot }}
                </div>

                <p class="mt-8 text-center font-body text-xs text-[#5F6B7A]">© 2026 Sistem Manajemen Perpustakaan</p>
            </div>
        </div>
    </body>
</html>
