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
    <body class="antialiased text-white bg-night">
        <div class="min-h-screen relative overflow-x-clip">
            {{-- Subtle background: faint teal glow, no grid --}}
            <div class="fixed inset-0 -z-10 bg-night overflow-hidden" aria-hidden="true">
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_70%_45%_at_50%_-10%,rgba(15,118,110,0.05),transparent_60%)]"></div>
            </div>

            @include('layouts.navigation')

            <main class="relative lg:pl-[240px]">
                <div class="px-4 sm:px-6 lg:px-8 py-6 lg:py-8 max-w-[1400px] mx-auto">
                    @if (isset($header))
                        <header class="mb-6 lg:mb-7">
                            {{ $header }}
                        </header>
                    @endif

                    <div>
                        {{ $slot }}
                    </div>
                </div>
            </main>

            {{-- Toast host --}}
            <div class="fixed bottom-6 right-6 z-[100] flex flex-col items-end gap-3 w-full max-w-sm px-4 sm:px-0"
                 x-data="{}"
                 x-cloak>
                <template x-for="t in $store.toast.items" :key="t.id">
                    <div x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                         class="glass flex items-center gap-3 pl-4 pr-2 py-3 rounded-glass shadow-glass-lg w-full pointer-events-auto"
                         :class="{
                             'border-emerald-400/30': t.type === 'success',
                             'border-rose-400/30': t.type === 'error',
                             'border-sky-400/30': t.type === 'info',
                         }">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                              :class="{
                                  'bg-emerald-400/15 text-emerald-300': t.type === 'success',
                                  'bg-rose-400/15 text-rose-300': t.type === 'error',
                                  'bg-sky-400/15 text-sky-300': t.type === 'info',
                              }">
                            <template x-if="t.type === 'success'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="t.type === 'error'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </template>
                            <template x-if="t.type === 'info'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </template>
                        </span>
                        <p class="flex-1 font-body text-sm text-white/90 leading-snug" x-text="t.message"></p>
                        <button @click="$store.toast.remove(t.id)" class="p-2 text-white/40 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if (session('success'))
                    window.toast(@js(session('success')), 'success');
                @endif

                @if (session('error'))
                    window.toast(@js(session('error')), 'error');
                @endif

                @if (session('info'))
                    window.toast(@js(session('info')), 'info');
                @endif
            });
        </script>
    </body>
</html>
