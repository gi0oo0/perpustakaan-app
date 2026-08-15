<section>
    <header class="flex items-start gap-3">
        <span class="w-8 h-8 rounded-[9px] bg-[#202428] border border-white/[0.06] flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-[#35B8A5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </span>
        <div>
            <h2 class="font-display text-[15px] font-semibold text-white">Informasi Profil</h2>
            <p class="font-body text-[13px] text-[#8B949E] mt-0.5">Perbarui informasi profil akun Anda.</p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
        @csrf
        @method('patch')

        <div x-data="filePicker">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 bg-[#2DB7A8]/25 flex items-center justify-center border border-white/10">
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="font-display font-semibold text-xl text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-body text-[13px] font-medium text-white">Foto Profil</p>
                    <p class="font-body text-xs text-[#747C82] mt-0.5">JPG, PNG, GIF — maks. 2MB</p>
                    <div class="mt-2.5 flex flex-wrap items-center gap-2">
                        <label for="profile_image" class="inline-flex items-center gap-1.5 h-[32px] px-3.5 rounded-[7px] bg-[#202428] border border-white/[0.08] text-xs font-medium text-[#A5ADB3] hover:bg-[#252A2E] hover:text-white transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Pilih Foto
                        </label>
                        @if($user->profile_image)
                            <input type="checkbox" id="remove_profile_image" name="remove_profile_image" value="1" class="hidden peer">
                            <label for="remove_profile_image" class="inline-flex items-center gap-1.5 h-[32px] px-3.5 rounded-[7px] bg-rose-500/10 border border-rose-400/15 text-xs font-medium text-[#E76B73] hover:bg-rose-500/15 transition-colors cursor-pointer peer-checked:ring-2 peer-checked:ring-[#E76B73]/40">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus Foto
                            </label>
                            <p class="font-body text-xs text-[#E76B73] hidden peer-checked:inline">Foto akan dihapus saat disimpan.</p>
                        @endif
                    </div>
                    <input id="profile_image" name="profile_image" type="file" accept="image/jpeg,image/png,image/gif" class="hidden" @change="onPick">
                    <p class="font-body text-xs text-[#747C82] mt-1.5" x-text="fileName || 'Belum ada file dipilih.'"></p>
                    @error('profile_image') <p class="font-body text-xs text-[#E76B73] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div>
            <label for="name" class="block font-body text-xs font-medium text-white/70 mb-2">Nama</label>
            <input id="name" name="name" type="text" class="glass-input" :value="old('name', $user->name)" required autofocus autocomplete="name">
            @error('name') <p class="font-body text-xs text-[#E76B73] mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if($user->isMember())
                <div>
                    <label class="block font-body text-xs font-medium text-white/70 mb-2">NISN</label>
                    <p class="glass-input w-full font-mono text-white/80">{{ $user->nisn ?: '—' }}</p>
                    <p class="mt-1 font-body text-xs text-[#747C82]">NISN tidak dapat diubah melalui profil.</p>
                </div>
            @else
                <div>
                    <label for="nisn" class="block font-body text-xs font-medium text-white/70 mb-2">NISN</label>
                    <input id="nisn" name="nisn" type="text" class="glass-input font-mono" :value="old('nisn', $user->nisn)" autocomplete="nisn" placeholder="0081234567">
                    @error('nisn') <p class="font-body text-xs text-[#E76B73] mt-1">{{ $message }}</p> @enderror
                </div>
            @endif

            <div>
                <label for="email" class="block font-body text-xs font-medium text-white/70 mb-2">Email</label>
                <input id="email" name="email" type="email" class="glass-input" :value="old('email', $user->email)" required autocomplete="username">
                @error('email') <p class="font-body text-xs text-[#E76B73] mt-1">{{ $message }}</p> @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="font-body text-sm text-[#747C82]">
                            Email Anda belum terverifikasi.

                            <button form="send-verification" class="font-body text-xs text-sky-300 hover:text-sky-200 transition-colors">
                                Klik untuk kirim ulang email verifikasi.
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-body text-xs text-emerald-300">
                                Link verifikasi baru telah dikirim ke email Anda.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-1">
            <button type="submit" class="inline-flex items-center justify-center h-[40px] px-6 rounded-[8px] bg-[#2DB7A8] text-[#071311] text-sm font-semibold hover:bg-[#27A99A] transition-colors">Simpan Perubahan</button>

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