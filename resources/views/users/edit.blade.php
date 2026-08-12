<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-gradient">Edit Pengguna</h2>
                <p class="font-body text-sm text-white/45 mt-1">Perbarui data pengguna</p>
            </div>
            <a href="{{ route('users.index') }}" class="glass-btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto" x-data="reveal">
        <div class="glass p-6 sm:p-8">
            <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <div x-data="filePicker">
                        <label class="block font-body text-xs font-medium text-white/70 mb-3">Foto Profil</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 bg-gradient-soft flex items-center justify-center border border-white/10 shadow-glow">
                                @if($user->profile_image)
                                    <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="font-display font-semibold text-xl text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <label for="profile_image" class="glass-btn-secondary cursor-pointer inline-flex">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Ganti Foto
                                    </label>
                                    @if($user->profile_image)
                                        <input type="checkbox" id="remove_profile_image" name="remove_profile_image" value="1" class="hidden peer">
                                        <label for="remove_profile_image" class="glass-btn-danger cursor-pointer inline-flex peer-checked:ring-2 peer-checked:ring-rose-300/70">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus Foto
                                        </label>
                                        <p class="font-body text-xs text-rose-300 hidden peer-checked:inline">Foto akan dihapus saat disimpan.</p>
                                    @endif
                                </div>
                                <input id="profile_image" name="profile_image" type="file" accept="image/jpeg,image/png,image/gif" class="hidden" @change="onPick">
                                <p class="font-body text-xs text-white/40 mt-2" x-text="fileName || 'JPG, PNG, atau GIF. Maks 2MB.'"></p>
                                @error('profile_image') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="nisn" class="block font-body text-xs font-medium text-white/70 mb-2">NISN</label>
                        <input type="text" id="nisn" name="nisn" value="{{ old('nisn', $user->nisn) }}" required
                            class="glass-input w-full font-mono" placeholder="Masukkan NISN">
                        @error('nisn') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="name" class="block font-body text-xs font-medium text-white/70 mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            class="glass-input w-full" placeholder="Masukkan nama lengkap">
                        @error('name') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block font-body text-xs font-medium text-white/70 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="glass-input w-full" placeholder="Masukkan email">
                        @error('email') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="role" class="block font-body text-xs font-medium text-white/70 mb-2">Role</label>
                        <select id="role" name="role" required class="glass-select w-full">
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Anggota</option>
                        </select>
                        @error('role') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 border-t border-white/10">
                        <label for="password" class="block font-body text-xs font-medium text-white/70 mb-2">Password Baru</label>
                        <input type="password" id="password" name="password"
                            class="glass-input w-full" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <p class="mt-1 font-body text-xs text-white/40">Kosongkan jika tidak ingin mengubah password</p>
                        @error('password') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block font-body text-xs font-medium text-white/70 mb-2">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="glass-input w-full" placeholder="Ulangi password">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-white/10">
                    <a href="{{ route('users.index') }}" class="glass-btn-secondary">Batal</a>
                    <button type="submit" class="glass-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Perbarui Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
