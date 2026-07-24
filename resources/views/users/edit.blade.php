<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            ✏ Edit Anggota
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('users.index') }}" class="neo-btn-secondary inline-flex items-center gap-2 text-xs">
                    ← Kembali
                </a>
            </div>

            <div class="neo-card">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nisn" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">NISN</label>
                            <input id="nisn" name="nisn" type="text" :value="old('nisn', $user->nisn)" class="neo-input" placeholder="0081234567">
                            @error('nisn') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="name" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Nama Lengkap</label>
                            <input id="name" name="name" type="text" :value="old('name', $user->name)" required class="neo-input">
                            @error('name') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Email</label>
                            <input id="email" name="email" type="email" :value="old('email', $user->email)" required class="neo-input">
                            @error('email') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="role" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Role</label>
                            <select id="role" name="role" class="neo-input">
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Anggota</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 border-t-3 border-border pt-6">
                            <p class="font-heading font-semibold text-xs text-muted uppercase tracking-wide mb-3">Kosongkan jika tidak ingin mengubah password</p>
                        </div>

                        <div>
                            <label for="password" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Password Baru</label>
                            <input id="password" name="password" type="password" class="neo-input" placeholder="Minimal 6 karakter">
                            @error('password') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Konfirmasi Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="neo-input" placeholder="Ulangi password">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8 border-t-3 border-border pt-6">
                        <a href="{{ route('users.index') }}">
                            <button type="button" class="neo-btn-secondary">Batal</button>
                        </a>
                        <button type="submit" class="neo-btn-primary">✓ Perbarui</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
