<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-white">Manajemen Anggota</h2>
                <p class="font-body text-[13px] text-[#8B949E] mt-1">Kelola data pengguna perpustakaan</p>
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

    <style>
        html.dark .users-table thead th {
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
            color: #747C82;
            letter-spacing: 0.05em;
        }
        html.dark .users-table tbody td {
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
            padding-left: 0.9rem;
            padding-right: 0.9rem;
            font-size: 13px;
        }
        html.dark .users-table tbody tr {
            border-color: rgba(255, 255, 255, 0.045);
        }
        html.dark .users-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.025);
        }
        html.dark .rb-admin {
            background-color: rgba(224, 107, 115, 0.10);
            color: #E76B73;
        }
        html.dark .rb-staff {
            background-color: rgba(217, 164, 65, 0.12);
            color: #D9A441;
        }
        html.dark .rb-member {
            background-color: rgba(92, 159, 232, 0.12);
            color: #5C9FE8;
        }
    </style>

    <div class="space-y-5" x-data="userTable(@js($usersJson))">
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
                <p class="font-body text-xs text-[#747C82]">
                    <span x-text="filtered.length" class="text-white/70 font-semibold"></span> anggota ditemukan
                </p>
            </div>
        </div>

        {{-- Table --}}
        <div class="glass overflow-hidden">
            <div class="overflow-x-auto">
                <table class="glass-table users-table w-full min-w-[700px]">
                    <thead>
                        <tr class="border-b border-white/[0.06]">
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
                                <td class="font-mono text-[13px] text-white" x-text="u.nisn || '-'"></td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <template x-if="u.profile_image">
                                            <img :src="u.profile_image" :alt="u.name" class="w-8 h-8 rounded-full object-cover border border-white/10 flex-shrink-0">
                                        </template>
                                        <template x-if="!u.profile_image">
                                            <span class="w-8 h-8 rounded-full flex items-center justify-center font-display font-semibold text-sm text-white flex-shrink-0"
                                                  :class="u.role === 'admin' ? 'bg-[#2DB7A8]' : (u.role === 'staff' ? 'bg-[#2DB7A8]/60' : 'bg-[#2DB7A8]/25')"
                                                  x-text="u.name ? u.name.charAt(0).toUpperCase() : '?'"></span>
                                        </template>
                                        <span class="font-semibold text-[13px] text-white" x-text="u.name"></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-[13px] text-[#A5ADB3] block truncate max-w-[200px]" :title="u.email" x-text="u.email"></span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-medium whitespace-nowrap" :class="{
                                        'rb-admin': u.role === 'admin',
                                        'rb-staff': u.role === 'staff',
                                        'rb-member': u.role === 'user',
                                    }">
                                        <span x-text="u.role === 'user' ? 'Anggota' : u.role"></span>
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a :href="u.show_url" class="inline-flex items-center h-[30px] px-3 rounded-[7px] bg-[#202428] border border-white/[0.08] text-xs font-medium text-[#A5ADB3] hover:bg-[#252A2E] hover:text-white transition-colors">Detail</a>
                                        <a :href="u.edit_url" class="inline-flex items-center h-[30px] px-3 rounded-[7px] bg-[#202428] border border-white/[0.08] text-xs font-medium text-[#A5ADB3] hover:bg-[#252A2E] hover:text-white transition-colors">Edit</a>
                                        <form :action="u.destroy_url" method="POST" class="inline"
                                              @submit="confirmDelete($event, $el)">
                                            @csrf
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="inline-flex items-center h-[30px] px-3 rounded-[7px] bg-rose-500/10 border border-rose-400/15 text-xs font-medium text-[#E76B73] hover:bg-rose-500/15 transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <tr x-show="filtered.length === 0">
                            <td colspan="5" class="py-12 text-center">
                                <svg class="w-6 h-6 mx-auto mb-3 text-[#747C82]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="font-display font-semibold text-[15px] text-white" x-text="users.length === 0 ? 'Tidak ada anggota' : 'Tidak ada pengguna ditemukan'"></p>
                                <p class="font-body text-xs text-[#747C82] mt-1" x-text="users.length === 0 ? 'Belum ada pengguna yang terdaftar.' : 'Coba ubah kata pencarian atau filter yang digunakan.'"></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>