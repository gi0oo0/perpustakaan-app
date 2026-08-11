<section class="space-y-6">
    <header>
        <div class="flex items-center gap-3 mb-1">
            <span class="w-9 h-9 rounded-glass-sm bg-rose-400/10 border border-rose-400/20 flex items-center justify-center text-lg">🗑️</span>
            <h2 class="font-display font-semibold text-lg text-white">
                {{ __('Delete Account') }}
            </h2>
        </div>
        <p class="font-body text-sm text-white/45">
            {{ __('Setelah akun dihapus, semua data Anda akan terhapus permanen. Pastikan Anda menyimpan data penting sebelum menghapus akun.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8">
            @csrf
            @method('delete')

            <h2 class="font-display font-semibold text-lg text-white">
                {{ __('Yakin ingin menghapus akun Anda?') }}
            </h2>

            <p class="mt-2 font-body text-sm text-white/45 leading-relaxed">
                {{ __('Setelah akun dihapus, semua data dan sumber dayanya akan terhapus permanen. Masukkan password Anda untuk konfirmasi.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
