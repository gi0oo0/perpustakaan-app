<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="font-heading font-bold text-xl text-border">Daftar Akun</h2>
        <p class="font-body text-sm text-muted mt-1">Buat akun baru untuk memulai</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Nama</label>
            <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                   class="neo-input" placeholder="Nama lengkap">
            @error('name') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-4">
            <label for="email" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                   class="neo-input" placeholder="email@contoh.com">
            @error('email') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-4">
            <label for="password" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="neo-input" placeholder="Minimal 8 karakter">
            @error('password') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-4">
            <label for="password_confirmation" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="neo-input" placeholder="Ulangi password">
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="font-body text-sm text-primary hover:text-primary-700 underline" href="{{ route('login') }}">
                Sudah punya akun?
            </a>

            <button type="submit" class="neo-btn-primary">
                Daftar →
            </button>
        </div>
    </form>
</x-guest-layout>
