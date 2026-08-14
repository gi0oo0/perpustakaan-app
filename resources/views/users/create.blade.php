<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-gradient">Tambah Pengguna</h2>
                <p class="font-body text-sm text-white/45 mt-1">Buat akun pengguna baru</p>
            </div>
            <a href="{{ route('users.index') }}" class="glass-btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto" x-data="reveal">
        <div class="glass p-6 sm:p-8">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-5">
                    <div x-data="filePicker">
                        <label class="block font-body text-xs font-medium text-white/70 mb-3">Foto Profil</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 bg-gradient-soft flex items-center justify-center border border-white/10 shadow-glow">
                                <svg class="w-7 h-7 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="flex-1">
                                <label for="profile_image" class="glass-btn-secondary cursor-pointer inline-flex">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Pilih Foto
                                </label>
                                <input id="profile_image" name="profile_image" type="file" accept="image/jpeg,image/png,image/gif" class="hidden" @change="onPick">
                                <p class="font-body text-xs text-white/40 mt-2" x-text="fileName || 'JPG, PNG, atau GIF. Maks 2MB.'"></p>
                                @error('profile_image') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="nisn" class="block font-body text-xs font-medium text-white/70 mb-2">NISN</label>
                        <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required
                            class="glass-input w-full font-mono" placeholder="Masukkan NISN">
                        @error('nisn') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="name" class="block font-body text-xs font-medium text-white/70 mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="glass-input w-full" placeholder="Masukkan nama lengkap">
                        @error('name') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block font-body text-xs font-medium text-white/70 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="glass-input w-full" placeholder="Masukkan email">
                        @error('email') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="role" class="block font-body text-xs font-medium text-white/70 mb-2">Role</label>
                        <x-select-box :options="[
                            ['value' => 'admin', 'label' => 'Admin'],
                            ['value' => 'staff', 'label' => 'Staff'],
                            ['value' => 'user', 'label' => 'Anggota'],
                        ]" :value="old('role')" placeholder="Pilih Role" name="role" />
                        @error('role') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="password" class="block font-body text-xs font-medium text-white/70 mb-2">Password</label>
                            <input type="password" id="password" name="password" required
                                class="glass-input w-full" placeholder="Masukkan password">
                            @error('password') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block font-body text-xs font-medium text-white/70 mb-2">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="glass-input w-full" placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-white/10">
                    <a href="{{ route('users.index') }}" class="glass-btn-secondary">Batal</a>
                    <button type="submit" class="glass-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
