<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-gradient">Manajemen Anggota</h2>
                <p class="font-body text-sm text-white/45 mt-1">Kelola data pengguna perpustakaan</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('users.import') }}" class="glass-btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import CSV
                </a>
                <a href="{{ route('users.create') }}" class="glass-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Pengguna
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="reveal">
        <div class="glass p-5">
            <form action="{{ route('users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1" x-data="userSearch('{{ request('search') }}')">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Cari</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center text-white/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" name="search" x-model="query" @input="doSearch"
                               @keydown.escape="reset" autocomplete="off"
                               value="{{ request('search') }}" placeholder="Cari nama, NISN, atau email..."
                               class="glass-input pl-10">

                        <div x-show="open" @click.outside="reset()"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute left-0 right-0 top-full mt-2 glass rounded-glass shadow-glass-lg overflow-hidden z-50"
                             style="display: none;">
                            <div class="max-h-72 overflow-y-auto">
                                <template x-if="loading">
                                    <div class="px-4 py-3 space-y-2">
                                        <div class="search-skeleton h-12 rounded-glass-sm"></div>
                                        <div class="search-skeleton h-12 rounded-glass-sm"></div>
                                    </div>
                                </template>
                                <template x-if="!loading && results.length === 0">
                                    <div class="px-4 py-6 text-center">
                                        <p class="font-body text-sm text-white/50">Anggota tidak ditemukan</p>
                                    </div>
                                </template>
                                <template x-for="u in results" :key="u.id">
                                    <button @click="go(u.url)" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white/[0.06] transition-colors text-start">
                                        <span class="w-9 h-9 rounded-full bg-gradient-soft flex items-center justify-center font-display font-semibold text-xs text-white flex-shrink-0"
                                              x-text="u.name ? u.name.charAt(0).toUpperCase() : '?'"></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-body text-sm font-medium text-white truncate" x-text="u.name"></p>
                                            <p class="font-body text-xs text-white/40 truncate" x-text="(u.nisn ? u.nisn + ' · ' : '') + u.email"></p>
                                        </div>
                                        <span class="glass-badge flex-shrink-0"
                                              :class="u.role === 'admin' ? 'glass-badge-red' : (u.role === 'staff' ? 'glass-badge-yellow' : 'glass-badge-blue')">
                                            <span x-text="u.role === 'user' ? 'Anggota' : u.role"></span>
                                        </span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full sm:w-48">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Role</label>
                    <select name="role" class="glass-select w-full">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Anggota</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="glass-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari
                    </button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('users.index') }}" class="glass-btn-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="glass overflow-hidden">
            <div class="overflow-x-auto">
                <table class="glass-table w-full">
                    <thead>
                        <tr class="border-b border-white/[0.07] bg-white/[0.02]">
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="font-mono text-xs text-white/60">{{ $user->nisn }}</td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-full bg-gradient-soft flex items-center justify-center font-display font-semibold text-sm text-white flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                        <span class="font-medium text-white">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="text-white/60">{{ $user->email }}</td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="glass-badge-red">Admin</span>
                                    @elseif($user->role == 'staff')
                                        <span class="glass-badge-yellow">Staff</span>
                                    @else
                                        <span class="glass-badge-blue">Anggota</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('users.show', $user) }}" class="glass-btn-secondary text-xs py-1.5 px-3">
                                            Detail
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="glass-btn-secondary text-xs py-1.5 px-3">
                                            Edit
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                            onsubmit="return confirmDelete(event, this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="glass-btn-danger text-xs py-1.5 px-3">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-14 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-2xl bg-white/[0.04] border border-white/10 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-display font-semibold text-white">Tidak ada data pengguna</p>
                                            <p class="font-body text-xs text-white/40 mt-1">Coba ubah kata kunci pencarian.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="mt-6 px-4 pb-4">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function confirmDelete(e, form) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Pengguna?',
                text: "Data pengguna yang dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                background: '#0b1220',
                color: '#ffffff',
                iconColor: '#fbbf24',
                showCancelButton: true,
                confirmButtonColor: '#fb5e63',
                cancelButtonColor: '#3a3f52',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-2xl border border-white/10' }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }
    </script>
</x-app-layout>
