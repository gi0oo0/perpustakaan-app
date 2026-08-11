<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="w-14 h-14 mx-auto flex items-center justify-center text-2xl rounded-2xl bg-gradient-soft shadow-glow mb-4">📚</div>
        <h2 class="font-display text-2xl font-bold tracking-tight text-white">Selamat Datang</h2>
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

            @if (Route::has('password.request'))
                <a class="font-body text-sm text-sky-300 hover:text-sky-200 transition-colors" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit" class="glass-btn-primary w-full py-3">
                Masuk
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </button>
        </div>
    </form>

    @if (Route::has('register'))
        <p class="mt-6 text-center font-body text-sm text-white/40">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-white/85 hover:text-white font-medium transition-colors">Daftar sekarang</a>
        </p>
    @endif
</x-guest-layout>
