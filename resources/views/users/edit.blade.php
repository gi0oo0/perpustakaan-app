<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-[24px] font-semibold tracking-tight text-white">Edit Pengguna</h2>
                <p class="font-body text-[13px] text-[#8B949E] mt-1">Perbarui data pengguna</p>
            </div>
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 h-[38px] px-4 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#A5ADB3] text-sm font-medium hover:bg-[#252A2E] hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-[700px] mx-auto" x-data="reveal">
        <div class="glass rounded-[12px] p-6">
            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    {{-- Foto Profil --}}
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
                                <p class="font-body text-xs text-[#747C82] mt-0.5">JPG, PNG, atau GIF. Maks 2MB.</p>
                                <div class="flex flex-wrap items-center gap-2 mt-2.5">
                                    <label for="profile_image" class="inline-flex items-center gap-1.5 h-[32px] px-3.5 rounded-[7px] bg-[#202428] border border-white/[0.08] text-xs font-medium text-[#A5ADB3] hover:bg-[#252A2E] hover:text-white transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Ganti Foto
                                    </label>
                                    @if($user->profile_image)
                                        <input type="checkbox" id="remove_profile_image" name="remove_profile_image" value="1" class="hidden peer">
                                        <label for="remove_profile_image" class="inline-flex items-center gap-1.5 h-[32px] px-3.5 rounded-[7px] bg-[#E76B73]/[0.10] border border-[#E76B73]/25 text-xs font-medium text-[#E7A0A5] hover:bg-[#E76B73]/[0.16] transition-colors cursor-pointer peer-checked:ring-1 peer-checked:ring-[#E76B73]/40">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus Foto
                                        </label>
                                        <p class="font-body text-xs text-[#E7A0A5] hidden peer-checked:inline">Foto akan dihapus saat disimpan.</p>
                                    @endif
                                </div>
                                <input id="profile_image" name="profile_image" type="file" accept="image/jpeg,image/png,image/gif" class="hidden" @change="onPick">
                                <p class="font-body text-xs text-[#747C82] mt-1.5" x-text="fileName || 'Belum ada file dipilih.'"></p>
                                @error('profile_image') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block font-body text-xs font-medium text-[#A5ADB3] mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            class="glass-input w-full" placeholder="Masukkan nama lengkap">
                        @error('name') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nisn" class="block font-body text-xs font-medium text-[#A5ADB3] mb-2">NISN</label>
                        <input type="text" id="nisn" name="nisn" value="{{ old('nisn', $user->nisn) }}" required
                            class="glass-input w-full font-mono" placeholder="Masukkan NISN">
                        @error('nisn') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block font-body text-xs font-medium text-[#A5ADB3] mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="glass-input w-full" placeholder="Masukkan alamat email">
                        @error('email') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="role" class="block font-body text-xs font-medium text-[#A5ADB3] mb-2">Role</label>
                        <x-select-box :options="[
                            ['value' => 'admin', 'label' => 'Admin'],
                            ['value' => 'staff', 'label' => 'Staff'],
                            ['value' => 'user', 'label' => 'Anggota'],
                        ]" :value="old('role', $user->role)" placeholder="Pilih Role" name="role" />
                        @error('role') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password (opsional) --}}
                    <div class="pt-5 border-t border-white/[0.06]">
                        <p class="font-body text-[13px] font-medium text-white mb-0.5">Ubah Password</p>
                        <p class="font-body text-xs text-[#747C82] mb-4">Kosongkan jika tidak ingin mengubah password.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block font-body text-xs font-medium text-[#A5ADB3] mb-2">Password Baru</label>
                                <input type="password" id="password" name="password"
                                    class="glass-input w-full" placeholder="Masukkan password baru">
                                @error('password') <p class="mt-1 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block font-body text-xs font-medium text-[#A5ADB3] mb-2">Konfirmasi Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="glass-input w-full" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 mt-6 pt-5 border-t border-white/[0.06]">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center h-[40px] px-5 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#A5ADB3] text-sm font-medium hover:bg-[#252A2E] hover:text-white transition-colors">Batal</a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 h-[40px] px-6 rounded-[8px] bg-[#2DB7A8] text-[#071311] text-sm font-semibold hover:bg-[#27A99A] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Perbarui Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>