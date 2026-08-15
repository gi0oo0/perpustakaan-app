<section class="space-y-4">
    <header class="flex items-start gap-3">
        <span class="w-8 h-8 rounded-[9px] bg-[#202428] border border-white/[0.06] flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-[#E76B73]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </span>
        <div>
            <h2 class="font-display text-[15px] font-semibold text-[#E76B73]">Hapus Akun</h2>
            <p class="font-body text-[13px] text-[#8B949E] mt-0.5">Penghapusan akun bersifat permanen dan tidak dapat dibatalkan.</p>
        </div>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center gap-1.5 h-[36px] px-4 rounded-[8px] bg-rose-500/10 border border-rose-400/15 text-sm font-medium text-[#E76B73] hover:bg-rose-500/15 transition-colors"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="font-display font-semibold text-lg text-white">
                Yakin ingin menghapus akun Anda?
            </h2>

            <p class="mt-2 font-body text-sm text-[#8B949E] leading-relaxed">
                Penghapusan akun bersifat permanen dan tidak dapat dibatalkan. Masukkan password Anda untuk konfirmasi.
            </p>

            <div class="mt-5">
                <x-input-label for="password" value="Password" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="Password saat ini"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center justify-center h-[38px] px-4 rounded-[8px] bg-[#202428] border border-white/[0.08] text-sm font-medium text-[#A5ADB3] hover:bg-[#252A2E] hover:text-white transition-colors">
                    Batal
                </button>

                <button type="submit" data-loading-text="Menghapus..." class="inline-flex items-center justify-center h-[38px] px-4 rounded-[8px] bg-rose-500/10 border border-rose-400/15 text-sm font-medium text-[#E76B73] hover:bg-rose-500/15 transition-colors">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>