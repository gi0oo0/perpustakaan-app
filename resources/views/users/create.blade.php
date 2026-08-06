<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-display text-text">Tambah Pengguna</h1>
                <p class="mt-1 text-text-tertiary">Buat akun pengguna baru</p>
            </div>
            <a href="{{ route('users.index') }}" class="apple-btn-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-apple-lg">
            <div class="bg-white rounded-apple-lg p-8">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <!-- NISN -->
                        <div>
                            <label for="nisn" class="block text-sm font-display font-semibold text-text mb-2">NISN</label>
                            <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required
                                class="apple-input w-full @error('nisn') border-danger @enderror"
                                placeholder="Masukkan NISN">
                            @error('nisn')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-display font-semibold text-text mb-2">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="apple-input w-full @error('name') border-danger @enderror"
                                placeholder="Masukkan nama lengkap">
                            @error('name')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-display font-semibold text-text mb-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="apple-input w-full @error('email') border-danger @enderror"
                                placeholder="Masukkan email">
                            @error('email')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-display font-semibold text-text mb-2">Role</label>
                            <select id="role" name="role" required
                                class="apple-input w-full @error('role') border-danger @enderror">
                                <option value="">Pilih Role</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="anggota" {{ old('role') == 'anggota' ? 'selected' : '' }}>Anggota</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-display font-semibold text-text mb-2">Password</label>
                            <input type="password" id="password" name="password" required
                                class="apple-input w-full @error('password') border-danger @enderror"
                                placeholder="Masukkan password">
                            @error('password')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-display font-semibold text-text mb-2">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="apple-input w-full"
                                placeholder="Ulangi password">
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-surface-lighter">
                        <a href="{{ route('users.index') }}" class="apple-btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="apple-btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
