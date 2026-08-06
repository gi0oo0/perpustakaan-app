<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Perpustakaan') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-surface-light text-text">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-apple-xl text-center">
                <div class="w-16 h-16 bg-surface-light mx-auto flex items-center justify-center text-3xl mb-4 rounded-full">📚</div>
                <h1 class="font-display text-4xl text-text tracking-tight">
                    Perpustakaan
                </h1>
                <p class="font-body text-body-sm text-text-tertiary mt-2">Sistem Manajemen Peminjaman Buku</p>
            </div>

            <div class="w-full sm:max-w-md px-apple-lg py-apple-xl bg-white rounded-apple-lg border border-text/10 shadow-apple">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
