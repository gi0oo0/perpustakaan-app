<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Perpustakaan') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body antialiased bg-[#FAFAFA]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative">
            <div class="geo-shapes"></div>

            <div class="relative z-10 mb-6">
                <h1 class="font-heading font-bold text-4xl text-border tracking-tight">
                    <span class="text-primary">📚</span> Perpustakaan
                </h1>
            </div>

            <div class="relative z-10 w-full sm:max-w-md px-6 py-8 bg-white border-3 border-border shadow-neo">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
