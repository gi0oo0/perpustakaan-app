<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-gradient">Riwayat Peminjaman</h2>
                <p class="font-body text-sm text-white/45 mt-1">Pantau seluruh aktivitas peminjaman</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('loans.borrow.create') }}" class="glass-btn-primary text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Pinjam
                </a>
                @if (Auth::user()->isStaff())
                    <a href="{{ route('loans.return.create') }}" class="glass-btn-secondary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Kembalikan
                    </a>
                    <a :href="$store.loanExport.url || '{{ route('loans.export') }}'" x-data="{}" class="glass-btn-secondary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export CSV
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="loanTable(@js($loansData), '{{ route('loans.export') }}', {{ $isStaff ? 'true' : 'false' }})">
        {{-- Summary --}}
        <div class="glass p-5">
            <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
                <div class="flex items-center gap-6">
                    <div>
                        <p class="font-body text-xs text-white/40">Total bulan ini</p>
                        <p class="font-display text-xl sm:text-2xl font-bold text-white"><span x-data="countUp" data-count="{{ $totalThisMonth }}" x-text="displayed"></span></p>
                    </div>
                    <div class="w-px h-10 bg-white/10"></div>
                    <div>
                        <p class="font-body text-xs text-white/40">Masih dipinjam</p>
                        <p class="font-display text-xl sm:text-2xl font-bold text-sky-300"><span x-data="countUp" data-count="{{ $activeCount }}" x-text="displayed"></span></p>
                    </div>
                </div>
                <span class="glass-badge-gray hidden sm:inline-flex">Riwayat lengkap transaksi</span>
            </div>
        </div>

        {{-- Live Filters --}}
        <div class="glass p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div class="lg:col-span-1">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Cari</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center text-white/30 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" x-model="query" placeholder="Cari judul, nama, ISBN..." class="glass-input pl-10">
                    </div>
                </div>

                <div>
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Status</label>
                    <select x-model="status" class="glass-select w-full">
                        <option value="">Semua Status</option>
                        <option value="active">Dipinjam</option>
                        <option value="overdue">Terlambat</option>
                        <option value="returned_ontime">Dikembalikan (Tepat)</option>
                        <option value="returned_late">Dikembalikan (Telat)</option>
                        <option value="returned">Semua Dikembalikan</option>
                    </select>
                </div>

                <div>
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Dari</label>
                    <input type="date" x-model="dateFrom" class="glass-input w-full">
                </div>

                <div>
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Sampai</label>
                    <input type="date" x-model="dateTo" class="glass-input w-full">
                </div>

                <div>
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Urutkan</label>
                    <select x-model="sort" class="glass-select w-full">
                        <option value="recent">Terbaru</option>
                        <option value="title">Judul A-Z</option>
                        <option value="borrower">Peminjam A-Z</option>
                        <option value="due">Jatuh Tempo</option>
                        <option value="status">Status</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <p class="font-body text-xs text-white/40">
                    <span x-text="filtered.length" class="text-white/70 font-semibold"></span> transaksi ditemukan
                </p>
            </div>
        </div>

        {{-- Table --}}
        <div class="glass overflow-hidden">
            <div class="overflow-x-auto">
                <table class="glass-table w-full">
                    <thead>
                        <tr class="border-b border-white/[0.07] bg-white/[0.02]">
                            <th>Buku</th>
                            <th>Peminjam</th>
                            <th>NISN</th>
                            <th>Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                            @if ($isStaff)
                                <th>Diproses Oleh</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="loan in filtered" :key="loan.id">
                            <tr>
                                <td>
            <div class="flex items-center gap-3 flex-wrap">
                                        <template x-if="loan.cover_image">
                                            <img :src="loan.cover_image" alt="" class="h-10 w-8 object-cover rounded-md border border-white/10">
                                        </template>
                                        <template x-if="!loan.cover_image">
                                            <div class="h-10 w-8 rounded-md bg-white/[0.06] border border-white/10 flex items-center justify-center text-sm">📖</div>
                                        </template>
                                        <div class="min-w-0">
                                            <p class="font-display font-medium text-sm text-white truncate max-w-[150px]" :title="loan.book_title" x-text="loan.book_title"></p>
                                            <p class="font-body text-xs text-white/40 font-mono" x-text="loan.isbn"></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <template x-if="loan.user_url">
                                        <a :href="loan.user_url" class="glass-link font-medium" x-text="loan.borrower_name"></a>
                                    </template>
                                    <template x-if="!loan.user_url">
                                        <span class="font-medium" x-text="loan.borrower_name"></span>
                                    </template>
                                </td>
                                <td class="font-mono text-xs text-white/50" x-text="loan.borrower_nisn || '-'"></td>
                                <td class="text-white/60">
                                    <span x-text="loan.loan_date"></span>
                                    <span class="block font-body text-[11px] text-white/35" x-text="loan.duration_days + ' hari'"></span>
                                </td>
                                <td class="text-white/60" x-text="loan.due_date"></td>
                                <td class="text-white/60" x-text="loan.returned_at"></td>
                                <td>
                                    <span class="glass-badge" :class="{
                                        'glass-badge-red': loan.status_badge === 'red',
                                        'glass-badge-green': loan.status_badge === 'green',
                                        'glass-badge-yellow': loan.status_badge === 'yellow',
                                    }">
                                        <span x-text="loan.status_label"></span>
                                        <template x-if="loan.status_detail">
                                            <span x-text="'(' + loan.status_detail + ')'"></span>
                                        </template>
                                    </span>
                                </td>
                                <td>
                                    <template x-if="loan.denda_text !== '-'">
                                        <div>
                                            <p class="font-display font-semibold text-sm text-rose-300" x-text="loan.denda_text"></p>
                                            <template x-if="loan.denda_action">
                                                <form :action="loan.denda_action" method="POST" class="mt-1">
                                                    @csrf
                                                    <button type="submit" class="font-body text-xs text-sky-300 hover:text-sky-200 transition-colors">Tandai Lunas</button>
                                                </form>
                                            </template>
                                            <template x-if="!loan.denda_action && loan.denda_sub">
                                                <span class="font-body text-xs" :class="loan.denda_sub === 'Lunas' ? 'text-emerald-300' : 'text-white/40'" x-text="loan.denda_sub"></span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="loan.denda_text === '-'">
                                        <span class="text-white/40">-</span>
                                    </template>
                                </td>
                                <template x-if="isStaff">
                                    <td>
                                        <template x-if="loan.processor_name !== '-'">
                                            <div>
                                                <p class="font-body text-xs text-white/80" x-text="loan.processor_name"></p>
                                                <p class="font-body text-xs text-white/40" x-text="loan.processed_at"></p>
                                            </div>
                                        </template>
                                        <template x-if="loan.processor_name === '-'">
                                            <span class="text-white/40">-</span>
                                        </template>
                                    </td>
                                </template>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <template x-if="filtered.length === 0">
                            <tr>
                                <td :colspan="isStaff ? 9 : 8" class="py-14 text-center">
                                    <div class="text-4xl mb-3">📭</div>
                                    <p class="font-display font-semibold text-white">Tidak ada data peminjaman</p>
                                    <p class="font-body text-xs text-white/40 mt-1">Coba ubah filter atau lakukan peminjaman baru.</p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
