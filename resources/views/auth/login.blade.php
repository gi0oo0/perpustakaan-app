<x-guest-layout>
    {{-- Brand --}}
    <div class="text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl border border-white/10 overflow-hidden bg-white">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Perpustakaan" class="w-full h-full object-cover">
        </div>
        <h1 class="mt-4 font-display text-2xl font-bold tracking-tight text-white">Perpustakaan</h1>
        <p class="mt-1 font-body text-[13px] text-white/40">Sistem Manajemen Perpustakaan</p>
    </div>

    {{-- Heading --}}
    <div class="mt-8">
        <h2 class="font-display text-[22px] font-bold tracking-tight text-white">Selamat Datang</h2>
        <p class="mt-1.5 font-body text-sm text-white/40">Masuk ke akun perpustakaan Anda</p>
    </div>

    <x-auth-session-status class="mt-5" :status="session('status')" />

    {{-- Global error --}}
    @if ($errors->any())
        <div class="mt-5 rounded-[10px] bg-rose-500/10 border border-rose-500/20 px-4 py-3">
            <div class="flex items-start gap-2.5">
                <svg class="w-[18px] h-[18px] shrink-0 mt-0.5 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="font-body text-sm font-medium text-rose-300">Login gagal</p>
                    <p class="font-body text-[13px] text-rose-300/90 mt-0.5">{{ $errors->first() }}</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5"
          x-data="{ showPassword: false, submitting: false }"
          x-on:submit="submitting = true">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block font-body text-[13px] font-medium text-white/70 mb-2">Email</label>
            <div class="relative">
                <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                       placeholder="email@contoh.com"
                       class="peer glass-input h-12 pl-12 !rounded-[10px]">
                <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-white/35 peer-focus:text-primary transition-colors duration-200">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
            </div>
            @error('email') <p class="mt-1.5 font-body text-[13px] text-rose-300">{{ $message }}</p> @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block font-body text-[13px] font-medium text-white/70 mb-2">Password</label>
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       placeholder="Masukkan password"
                       :type="showPassword ? 'text' : 'password'"
                       class="peer glass-input h-12 pl-12 pr-12 !rounded-[10px]">
                <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-white/35 peer-focus:text-primary transition-colors duration-200">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </span>
                <button type="button" @click="showPassword = !showPassword" :aria-pressed="showPassword"
                        aria-label="Tampilkan atau sembunyikan password"
                        class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-white/40 hover:text-white/70 transition-colors duration-200">
                    <svg x-show="!showPassword" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showPassword" x-cloak class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                </button>
            </div>
            @error('password') <p class="mt-1.5 font-body text-[13px] text-rose-300">{{ $message }}</p> @enderror
        </div>

        {{-- Remember me --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                <input id="remember_me" type="checkbox" name="remember"
                       class="w-[18px] h-[18px] rounded-[5px] border border-slate-300 bg-white text-primary checked:bg-primary checked:border-primary focus:outline-none focus:ring-2 focus:ring-primary/25 focus:ring-offset-0 transition-colors duration-200 cursor-pointer">
                <span class="font-body text-sm text-white/60">Ingat saya</span>
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit" :disabled="submitting"
                class="glass-btn-primary h-12 w-full !rounded-[10px] text-[15px] font-semibold">
            <span x-show="!submitting" class="inline-flex items-center gap-2">
                Masuk
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </span>
            <span x-show="submitting" x-cloak class="inline-flex items-center gap-2.5">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Memproses...
            </span>
        </button>
    </form>

    {{-- Customer service --}}
    <div class="mt-6 rounded-[10px] bg-white/[0.03] border border-white/10 px-4 py-3.5">
        <p class="font-body text-[13px] font-medium text-white/70">Lupa password?</p>
        <p class="font-body text-[13px] text-white/40 mt-0.5">Hubungi Customer Service</p>
        <div class="mt-3 flex flex-col gap-2">
            <a href="mailto:cs@perpustakaan.test" class="inline-flex items-center gap-2 font-body text-[13px] text-primary hover:text-primary-hover transition-colors duration-200">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                cs@perpustakaan.test
            </a>
            <a href="https://wa.me/6283168565272" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 font-body text-[13px] text-primary hover:text-primary-hover transition-colors duration-200">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                0831-6856-5272
            </a>
        </div>
    </div>

    @if (Route::has('register'))
        <p class="mt-6 text-center font-body text-[13px] text-white/40">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-primary hover:text-primary-hover transition-colors duration-200">Daftar sekarang</a>
        </p>
    @endif
</x-guest-layout>
