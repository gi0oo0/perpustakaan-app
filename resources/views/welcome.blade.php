<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Perpustakaan</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
        <style>
            *,*::after,*::before{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}
            html{line-height:1.5;font-family:Inter,system-ui,sans-serif;-webkit-text-size-adjust:100%}
            body{margin:0;line-height:1.5}
            a{color:inherit;text-decoration:inherit}
            .min-h-screen{min-height:100vh}
            .flex{display:flex}
            .flex-col{flex-direction:column}
            .items-center{align-items:center}
            .justify-center{justify-content:center}
            .text-center{text-align:center}
            .bg-surface{background-color:#f5f5f7}
            .bg-white{background-color:#fff}
            .text-text{color:#1d1d1f}
            .text-text-secondary{color:#6e6e73}
            .font-display{font-family:Inter,system-ui,sans-serif}
            .text-5xl{font-size:3rem;line-height:1.1}
            .text-lg{font-size:1.125rem;line-height:1.6}
            .text-sm{font-size:0.875rem;line-height:1.5}
            .font-semibold{font-weight:600}
            .font-medium{font-weight:500}
            .mt-4{margin-top:1rem}
            .mt-8{margin-top:2rem}
            .mt-12{margin-top:3rem}
            .mb-2{margin-bottom:0.5rem}
            .gap-4{gap:1rem}
            .rounded-full{border-radius:9999px}
            .rounded-xl{border-radius:0.75rem}
            .px-6{padding-left:1.5rem;padding-right:1.5rem}
            .py-3{padding-top:0.75rem;padding-bottom:0.75rem}
            .px-8{padding-left:2rem;padding-right:2rem}
            .py-4{padding-top:1rem;padding-bottom:1rem}
            .p-2{padding:0.5rem}
            .inline-flex{display:inline-flex}
            .transition{transition-property:color,background-color,border-color,box-shadow,opacity;transition-duration:200ms;transition-timing-function:ease}
            .shadow-sm{box-shadow:0 1px 2px 0 rgba(0,0,0,0.05)}
            .w-12{width:3rem}
            .h-12{height:3rem}
            .max-w-lg{max-width:32rem}
            .mx-auto{margin-left:auto;margin-right:auto}
            .hover\:bg-primary-700:hover{background-color:#0066cc}
            .hover\:text-primary-700:hover{color:#0066cc}
            .text-primary{color:#0071e3}
            .bg-primary{background-color:#0071e3}
            .text-white{color:#fff}
            .border-0{border-width:0}
            .font-body{font-family:Inter,system-ui,sans-serif}
            .text-heading-xl{font-size:3rem;line-height:1.1;letter-spacing:-0.02em;font-weight:600}
            .text-heading-sm{font-size:1.5rem;line-height:1.3;letter-spacing:-0.01em;font-weight:600}
            .rounded-apple-lg{border-radius:1rem}
        </style>
    </head>
    <body class="bg-surface min-h-screen antialiased">
        <div class="min-h-screen flex items-center justify-center px-6">
            <div class="text-center max-w-lg mx-auto">
                <div class="w-16 h-16 bg-white rounded-full shadow-sm mx-auto flex items-center justify-center text-3xl mb-6">📚</div>

                <h1 class="font-display text-heading-xl text-text">Perpustakaan</h1>
                <p class="font-body text-lg text-text-secondary mt-4">Sistem manajemen perpustakaan yang modern dan sederhana.</p>

                <div class="mt-12 inline-flex gap-4 flex-col sm:flex-row">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="apple-btn-primary">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="apple-btn-primary">
                                Masuk
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="apple-btn-secondary">
                                    Daftar
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 left-0 right-0 text-center py-6">
            <p class="text-sm text-text-secondary">Perpustakaan &copy; {{ date('Y') }}</p>
        </div>
    </body>
</html>
