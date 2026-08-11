<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-gradient">Tambah Pengguna</h2>
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
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="space-y-5">
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
                        <select id="role" name="role" required class="glass-select w-full">
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Anggota</option>
                        </select>
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
