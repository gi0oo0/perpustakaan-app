<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            👥 Kelola Anggota
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-primary text-white border-3 border-border shadow-neo px-6 py-4 font-heading font-semibold uppercase tracking-wide text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-coral text-white border-3 border-border shadow-neo px-6 py-4 font-heading font-semibold uppercase tracking-wide text-sm">
                    ✗ {{ session('error') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="neo-card mb-6">
                <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari nama, email, NISN..."
                        class="neo-input flex-1">
                    <select name="role" class="neo-input w-full sm:w-40">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Anggota</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="submit" class="neo-btn-primary text-xs">Cari</button>
                        <a href="{{ route('users.index') }}">
                            <button type="button" class="neo-btn-secondary text-xs">Reset</button>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 mb-6">
                <a href="{{ route('users.create') }}">
                    <button type="button" class="neo-btn-primary">+ Tambah Anggota</button>
                </a>
            </div>

            {{-- Users Table --}}
            <div class="neo-card overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-3 border-border">
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">NISN</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Nama</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Email</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Role</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Total Pinjam</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-mono text-sm text-border">{{ $user->nisn ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('users.show', $user) }}" class="font-heading font-semibold text-sm text-border hover:text-primary underline">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 font-body text-sm text-muted">{{ $user->email }}</td>
                                <td class="py-3 px-4">
                                    @if ($user->isAdmin())
                                        <span class="neo-badge bg-coral text-white text-xs">Admin</span>
                                    @else
                                        <span class="neo-badge bg-primary text-white text-xs">Anggota</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-body text-sm text-border">{{ $user->loans()->count() }}</td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('users.show', $user) }}">
                                            <button type="button" class="neo-btn-primary text-xs py-1 px-2">Detail</button>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}">
                                            <button type="button" class="neo-btn-secondary text-xs py-1 px-2">Edit</button>
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="delete-form-user">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="neo-btn-danger text-xs py-1 px-2">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center">
                                    <div class="text-4xl mb-3">👥</div>
                                    <p class="font-heading font-semibold text-border">Belum ada anggota</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-form-user').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Akun yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FF6B6B',
                    cancelButtonColor: '#111827',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
