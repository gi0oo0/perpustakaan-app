<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-display font-semibold text-text-primary">Manajemen Pengguna</h1>
                <p class="mt-1 text-text-tertiary">Kelola data pengguna perpustakaan</p>
            </div>
            <a href="{{ route('users.create') }}" class="apple-btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pengguna
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-apple-lg">
            <div class="bg-white rounded-apple-lg p-6">
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-4 mb-6">
                    <form action="{{ route('users.index') }}" method="GET" class="flex-1 flex gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengguna..."
                                class="apple-input w-full">
                        </div>
                        <div class="w-48">
                            <select name="role" class="apple-input w-full">
                                <option value="">Semua Role</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="anggota" {{ request('role') == 'anggota' ? 'selected' : '' }}>Anggota</option>
                            </select>
                        </div>
                        <button type="submit" class="apple-btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        @if(request('search') || request('role'))
                            <a href="{{ route('users.index') }}" class="apple-btn-secondary">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-surface-lighter">
                                <th class="text-left py-3 px-4 text-sm font-display font-semibold text-text-tertiary">NISN</th>
                                <th class="text-left py-3 px-4 text-sm font-display font-semibold text-text-tertiary">Nama</th>
                                <th class="text-left py-3 px-4 text-sm font-display font-semibold text-text-tertiary">Email</th>
                                <th class="text-left py-3 px-4 text-sm font-display font-semibold text-text-tertiary">Role</th>
                                <th class="text-right py-3 px-4 text-sm font-display font-semibold text-text-tertiary">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr class="border-b border-surface-lighter hover:bg-surface-light transition-colors">
                                    <td class="py-4 px-4 text-sm text-text-primary">{{ $user->nisn }}</td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-surface-light rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-text">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                            <span class="text-sm font-medium text-text-primary">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-text-tertiary">{{ $user->email }}</td>
                                    <td class="py-4 px-4">
                                        @if($user->role == 'admin')
                                            <span class="apple-badge-red">Admin</span>
                                        @elseif($user->role == 'staff')
                                            <span class="apple-badge-yellow">Staff</span>
                                        @else
                                            <span class="apple-badge-blue">Anggota</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('users.show', $user) }}" class="apple-btn-secondary text-sm py-1.5 px-3">
                                                Detail
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}" class="apple-btn-secondary text-sm py-1.5 px-3">
                                                Edit
                                            </a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                                onsubmit="return confirmDelete(event, this)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="apple-btn-danger text-sm py-1.5 px-3">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-text-tertiary">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-text-quaternary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>Tidak ada data pengguna ditemukan</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="mt-6">
                        {{ $users->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete(e, form) {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus Pengguna?',
                    text: "Data pengguna yang dihapus tidak dapat dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#E5484D',
                    cancelButtonColor: '#6E6E73',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            }
        </script>
    @endpush
</x-app-layout>
