<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-14 h-14 mx-auto flex items-center justify-center text-2xl rounded-2xl bg-emerald-400/15 border border-emerald-400/25 text-emerald-300 mb-4">✉️</div>
        <h2 class="font-display text-xl font-bold tracking-tight text-white">Verifikasi Email</h2>
        <p class="font-body text-sm text-white/45 mt-2 leading-relaxed">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 glass rounded-glass-sm border-emerald-400/25 px-4 py-3 font-body text-sm text-emerald-300">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="py-2.5">
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="font-body text-sm text-white/45 hover:text-white transition-colors">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
