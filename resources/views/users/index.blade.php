<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-gradient">Manajemen Anggota</h2>
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

    <div class="space-y-6" x-data="userTable(@js($usersJson))">
        {{-- Live Filters --}}
        <div class="glass p-5 relative z-20">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div class="lg:col-span-2">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Cari</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center text-white/30 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" x-model="query" placeholder="Cari nama, NISN, atau email..." class="glass-input pl-10">
                    </div>
                </div>

                <div @selectbox:change="role = $event.detail">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Role</label>
                    <x-select-box :options="[
                        ['value' => '', 'label' => 'Semua Role'],
                        ['value' => 'admin', 'label' => 'Admin'],
                        ['value' => 'staff', 'label' => 'Staff'],
                        ['value' => 'user', 'label' => 'Anggota'],
                    ]" placeholder="Pilih Role" />
                </div>

                <div @selectbox:change="sort = $event.detail">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Urutkan</label>
                    <x-select-box :options="[
                        ['value' => 'recent', 'label' => 'Terbaru'],
                        ['value' => 'name', 'label' => 'Nama A-Z'],
                        ['value' => 'nisn', 'label' => 'NISN'],
                        ['value' => 'email', 'label' => 'Email A-Z'],
                    ]" placeholder="Urutkan" />
                </div>
            </div>
            <div class="mt-3">
                <p class="font-body text-xs text-white/40">
                    <span x-text="filtered.length" class="text-white/70 font-semibold"></span> anggota ditemukan
                </p>
            </div>
        </div>

        {{-- Table --}}
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
                        <template x-for="u in filtered" :key="u.id">
                            <tr>
                                <td class="font-mono text-xs text-white/60" x-text="u.nisn || '-'"></td>
                                <td>
            <div class="flex items-center gap-3 flex-wrap">
                                        <template x-if="u.profile_image">
                                            <img :src="u.profile_image" :alt="u.name" class="w-9 h-9 rounded-full object-cover border border-white/10 flex-shrink-0">
                                        </template>
                                        <template x-if="!u.profile_image">
                                            <span class="w-9 h-9 rounded-full bg-gradient-soft flex items-center justify-center font-display font-semibold text-sm text-white flex-shrink-0"
                                                  x-text="u.name ? u.name.charAt(0).toUpperCase() : '?'"></span>
                                        </template>
                                        <span class="font-medium text-white" x-text="u.name"></span>
                                    </div>
                                </td>
                                <td class="text-white/60" x-text="u.email"></td>
                                <td>
                                    <span class="glass-badge" :class="u.role === 'admin' ? 'glass-badge-red' : (u.role === 'staff' ? 'glass-badge-yellow' : 'glass-badge-blue')">
                                        <span x-text="u.role === 'user' ? 'Anggota' : u.role"></span>
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a :href="u.show_url" class="glass-btn-secondary text-xs py-1.5 px-3">Detail</a>
                                        <a :href="u.edit_url" class="glass-btn-secondary text-xs py-1.5 px-3">Edit</a>
                                        <form :action="u.destroy_url" method="POST" class="inline"
                                              @submit="confirmDelete($event, $el)">
                                            @csrf
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="glass-btn-danger text-xs py-1.5 px-3">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <template x-if="filtered.length === 0">
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
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
