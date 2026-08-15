<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-[24px] font-semibold tracking-tight text-white">Tambah Pengguna</h2>
                <p class="font-body text-[13px] text-[#8B949E] mt-1">Buat akun pengguna baru</p>
            </div>
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 h-[38px] px-4 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#A5ADB3] text-sm font-medium hover:bg-[#252A2E] hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-[700px] mx-auto" x-data="reveal">
        <div class="glass rounded-[12px] p-6">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-4">
                    {{-- Foto Profil --}}
                    <div x-data="filePicker">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 bg-[#2DB7A8]/25 flex items-center justify-center border border-white/10">
                                <svg class="w-7 h-7 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-body text-[13px] font-medium text-white">Foto Profil</p>
                                <p class="font-body text-xs text-[#747C82] mt-0.5">JPG, PNG, atau GIF. Maks 2MB.</p>
                                <div class="mt-2.5">
                                    <label for="profile_image" class="inline-flex items-center gap-1.5 h-[32px] px-3.5 rounded-[7px] bg-[#202428] border border-white/[0.08] text-xs font-medium text-[#A5ADB3] hover:bg-[#252A2E] hover:text-white transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Pilih Foto
                                    </label>
                                    <input id="profile_image" name="profile_image" type="file" accept="image/jpeg,image/png,image/gif" class="hidden" @change="onPick">
                                    <p class="font-body text-xs text-[#747C82] mt-1.5" x-text="fileName || 'Belum ada file dipilih.'"></p>
                                    @error('profile_image') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block font-body text-xs font-medium text-white/70 mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="glass-input w-full" placeholder="Masukkan nama lengkap">
                        @error('name') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nisn" class="block font-body text-xs font-medium text-white/70 mb-2">NISN</label>
                        <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required
                            class="glass-input w-full font-mono" placeholder="Masukkan NISN">
                        @error('nisn') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block font-body text-xs font-medium text-white/70 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="glass-input w-full" placeholder="Masukkan alamat email">
                        @error('email') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="role" class="block font-body text-xs font-medium text-white/70 mb-2">Role</label>
                        <x-select-box :options="[
                            ['value' => 'admin', 'label' => 'Admin'],
                            ['value' => 'staff', 'label' => 'Staff'],
                            ['value' => 'user', 'label' => 'Anggota'],
                        ]" :value="old('role')" placeholder="Pilih Role" name="role" />
                        @error('role') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block font-body text-xs font-medium text-white/70 mb-2">Password</label>
                            <input type="password" id="password" name="password" required
                                class="glass-input w-full" placeholder="Masukkan password">
                            @error('password') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block font-body text-xs font-medium text-white/70 mb-2">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="glass-input w-full" placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 mt-6 pt-5 border-t border-white/[0.06]">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center h-[40px] px-5 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#A5ADB3] text-sm font-medium hover:bg-[#252A2E] hover:text-white transition-colors">Batal</a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 h-[40px] px-6 rounded-[8px] bg-[#2DB7A8] text-[#071311] text-sm font-semibold hover:bg-[#27A99A] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>