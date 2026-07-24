<section>
    <header>
        <h2 class="font-heading font-bold text-lg text-border uppercase tracking-wide">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 font-body text-sm text-muted">
            {{ __("Perbarui informasi profil akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Nama</label>
            <input id="name" name="name" type="text" class="neo-input" :value="old('name', $user->name)" required autofocus autocomplete="name">
            @error('name') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="nisn" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">NISN</label>
            <input id="nisn" name="nisn" type="text" class="neo-input" :value="old('nisn', $user->nisn)" autocomplete="nisn" placeholder="0081234567">
            @error('nisn') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Email</label>
            <input id="email" name="email" type="email" class="neo-input" :value="old('email', $user->email)" required autocomplete="username">
            @error('email') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="font-body text-sm text-muted mt-2">
                        {{ __('Email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="font-heading font-semibold text-xs text-primary hover:underline">
                            {{ __('Klik untuk kirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-heading font-semibold text-xs text-primary">
                            {{ __('Link verifikasi baru telah dikirim ke email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="neo-btn-primary">Simpan</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="font-heading font-semibold text-xs text-primary"
                >Tersimpan ✓</p>
            @endif
        </div>
    </form>
</section>
