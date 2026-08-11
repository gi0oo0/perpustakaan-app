<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="font-display text-2xl font-bold tracking-tight text-white">Lupa Password</h2>
        <p class="font-body text-sm text-white/45 mt-2 leading-relaxed">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="email@contoh.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>

    <p class="mt-6 text-center">
        <a href="{{ route('login') }}" class="font-body text-sm text-sky-300 hover:text-sky-200 transition-colors">← Kembali ke login</a>
    </p>
</x-guest-layout>
