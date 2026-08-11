<section>
    <header>
        <div class="flex items-center gap-3 mb-1">
            <span class="w-9 h-9 rounded-glass-sm bg-violet-400/10 border border-violet-400/20 flex items-center justify-center text-lg">👤</span>
            <h2 class="font-display font-semibold text-lg text-white">
                {{ __('Informasi Profil') }}
            </h2>
        </div>
        <p class="font-body text-sm text-white/45">
            {{ __("Perbarui informasi profil akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block font-body text-xs font-medium text-white/70 mb-2">Nama</label>
            <input id="name" name="name" type="text" class="glass-input" :value="old('name', $user->name)" required autofocus autocomplete="name">
            @error('name') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="nisn" class="block font-body text-xs font-medium text-white/70 mb-2">NISN</label>
            <input id="nisn" name="nisn" type="text" class="glass-input font-mono" :value="old('nisn', $user->nisn)" autocomplete="nisn" placeholder="0081234567">
            @error('nisn') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block font-body text-xs font-medium text-white/70 mb-2">Email</label>
            <input id="email" name="email" type="email" class="glass-input" :value="old('email', $user->email)" required autocomplete="username">
            @error('email') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="font-body text-sm text-white/40">
                        {{ __('Email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="font-body text-xs text-sky-300 hover:text-sky-200 transition-colors">
                            {{ __('Klik untuk kirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-body text-xs text-emerald-300">
                            {{ __('Link verifikasi baru telah dikirim ke email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="glass-btn-primary">Simpan Perubahan</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="font-body text-sm text-emerald-300 flex items-center gap-1"
                ><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Tersimpan</p>
            @endif
        </div>
    </form>
</section>
