<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="font-display text-2xl font-bold tracking-tight text-white">Daftar Akun</h2>
        <p class="font-body text-sm text-white/45 mt-1.5">Buat akun baru untuk memulai</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block font-body text-xs font-medium text-white/70 mb-2">Nama</label>
            <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                   class="glass-input" placeholder="Nama lengkap">
            @error('name') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="nisn" class="block font-body text-xs font-medium text-white/70 mb-2">NISN</label>
            <input id="nisn" type="text" name="nisn" :value="old('nisn')" autocomplete="nisn"
                   class="glass-input" placeholder="0081234567 (opsional)">
            @error('nisn') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block font-body text-xs font-medium text-white/70 mb-2">Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                   class="glass-input" placeholder="email@contoh.com">
            @error('email') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block font-body text-xs font-medium text-white/70 mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       class="glass-input" placeholder="Min. 8 karakter">
                @error('password') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block font-body text-xs font-medium text-white/70 mb-2">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="glass-input" placeholder="Ulangi password">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="glass-btn-primary w-full py-3">
                Daftar
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </button>
        </div>
    </form>

    <p class="mt-6 text-center font-body text-sm text-white/40">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-white/85 hover:text-white font-medium transition-colors">Masuk di sini</a>
    </p>
</x-guest-layout>
