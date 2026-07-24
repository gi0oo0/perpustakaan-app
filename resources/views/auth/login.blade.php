<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="font-heading font-bold text-xl text-border">Masuk</h2>
        <p class="font-body text-sm text-muted mt-1">Selamat datang kembali!</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Email</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                   class="neo-input" placeholder="email@contoh.com">
            @error('email') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-4">
            <label for="password" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="neo-input" placeholder="Masukkan password">
            @error('password') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-4 h-4 border-3 border-border text-primary focus:ring-primary" name="remember">
                <span class="ms-2 font-body text-sm text-muted">Ingat saya</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="font-body text-sm text-primary hover:text-primary-700 underline" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif

            <button type="submit" class="neo-btn-primary">
                Masuk →
            </button>
        </div>
    </form>
</x-guest-layout>
