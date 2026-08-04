<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Perpustakaan') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-surface-light text-text">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-apple-xl">
                <h1 class="font-display text-heading-xl text-text tracking-tight">
                    Perpustakaan
                </h1>
                <p class="font-body text-body-sm text-text-tertiary mt-2 text-center">Sistem Manajemen Peminjaman Buku</p>
            </div>

            <div class="w-full sm:max-w-md px-apple-lg py-apple-xl bg-white rounded-apple-lg shadow-apple">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
