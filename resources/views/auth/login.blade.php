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

    <div class="mt-6 rounded-xl bg-white/[0.04] border border-white/10 p-3.5 text-center">
        <p class="font-body text-xs font-medium text-white/60">Lupa password? Hubungi Customer Service</p>
        <p class="font-body text-sm text-white/85 mt-1">
            <a href="mailto:cs@perpustakaan.test" class="text-sky-300 hover:text-sky-200 transition-colors">cs@perpustakaan.test</a>
            <span class="text-white/30 mx-1.5">/</span>
            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="text-sky-300 hover:text-sky-200 transition-colors">+62 812-3456-7890</a>
        </p>
    </div>

    @if (Route::has('register'))
        <p class="mt-6 text-center font-body text-sm text-white/40">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-white/85 hover:text-white font-medium transition-colors">Daftar sekarang</a>
        </p>
    @endif
</x-guest-layout>
