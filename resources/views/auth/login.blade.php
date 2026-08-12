<x-guest-layout>
    <div class="mb-6 sm:mb-8 text-center">
        <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto rounded-2xl shadow-glow mb-3 sm:mb-4 overflow-hidden">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Perpustakaan" class="w-full h-full object-cover">
        </div>
        <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-white">Selamat Datang</h2>
        <p class="font-body text-sm text-white/45 mt-1.5">Masuk ke akun perpustakaan Anda</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block font-body text-xs font-medium text-white/70 mb-2">Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-white/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                       class="glass-input pl-10" placeholder="email@contoh.com">
            </div>
            @error('email') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="block font-body text-xs font-medium text-white/70 mb-2">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3.5 flex items-center text-white/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </span>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="glass-input pl-10" placeholder="Masukkan password">
            </div>
            @error('password') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input id="remember_me" type="checkbox" name="remember"
                       class="w-4 h-4 rounded bg-white/[0.06] border-white/15 text-primary focus:ring-primary/40">
                <span class="ms-2 font-body text-sm text-white/55">Ingat saya</span>
            </label>

        </div>

        <div class="pt-2">
            <button type="submit" class="glass-btn-primary w-full py-3">
                Masuk
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </button>
        </div>
    </form>

    <div class="mt-6 rounded-xl bg-white/[0.04] border border-white/10 p-4 sm:p-5">
        <p class="font-body text-sm font-medium text-white/70 text-center">Lupa password? Hubungi Customer Service</p>
        <div class="mt-3 space-y-2">
            <a href="mailto:cs@perpustakaan.test" class="flex items-center justify-center gap-2 font-body text-base text-sky-300 hover:text-sky-200 transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                cs@perpustakaan.test
            </a>
            <a href="https://wa.me/6283168565272" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 font-body text-base text-sky-300 hover:text-sky-200 transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                0831-6856-5272
            </a>
        </div>
    </div>

    @if (Route::has('register'))
        <p class="mt-6 text-center font-body text-sm text-white/40">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-white/85 hover:text-white font-medium transition-colors">Daftar sekarang</a>
        </p>
    @endif
</x-guest-layout>
