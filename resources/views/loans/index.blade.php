<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-white">Riwayat Peminjaman</h2>
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

    <style>
        html.dark .loans-table thead th {
            padding-top: 0.65rem;
            padding-bottom: 0.65rem;
            color: #747C82;
            letter-spacing: 0.05em;
        }
        html.dark .loans-table tbody td {
            padding-top: 0.65rem;
            padding-bottom: 0.65rem;
            padding-left: 0.9rem;
            padding-right: 0.9rem;
            font-size: 13px;
        }
        html.dark .loans-table tbody tr {
            border-color: rgba(255, 255, 255, 0.045);
        }
        html.dark .loans-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.025);
        }
        html.dark .sb-overdue {
            background-color: rgba(224, 107, 115, 0.12);
            color: #E76B73;
        }
        html.dark .sb-returned {
            background-color: rgba(76, 175, 125, 0.12);
            color: #4CAF7D;
        }
        html.dark .sb-active {
            background-color: rgba(92, 159, 232, 0.12);
            color: #5C9FE8;
        }
    </style>

    <div class="space-y-5" x-data="loanTable(@js($loansData), '{{ route('loans.export') }}', {{ $isStaff ? 'true' : 'false' }})">
        {{-- Summary --}}
        <div class="glass p-5">
            <div class="flex items-center gap-6">
                <div>
                    <p class="font-body text-xs text-[#747C82]">Total bulan ini</p>
                    <p class="font-display text-[26px] font-bold text-white leading-tight mt-0.5"><span x-data="countUp" data-count="{{ $totalThisMonth }}" x-text="displayed"></span></p>
                </div>
                <div class="w-px h-11 bg-white/[0.08]"></div>
                <div>
                    <p class="font-body text-xs text-[#747C82]">Masih dipinjam</p>
                    <p class="font-display text-[26px] font-bold text-sky-300 leading-tight mt-0.5"><span x-data="countUp" data-count="{{ $activeCount }}" x-text="displayed"></span></p>
                </div>
                <div class="ml-auto hidden sm:block">
                    <p class="font-body text-xs text-[#747C82]">Riwayat lengkap transaksi</p>
                </div>
            </div>
        </div>

        {{-- Live Filters --}}
        <div class="glass p-5 relative z-20">
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

                <div @selectbox:change="status = $event.detail">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Status</label>
                    <x-select-box :options="[
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'active', 'label' => 'Dipinjam'],
                        ['value' => 'overdue', 'label' => 'Terlambat'],
                        ['value' => 'returned_ontime', 'label' => 'Dikembalikan (Tepat)'],
                        ['value' => 'returned_late', 'label' => 'Dikembalikan (Telat)'],
                        ['value' => 'returned', 'label' => 'Semua Dikembalikan'],
                    ]" placeholder="Pilih Status" />
                </div>

                <div @datepicker:change="dateFrom = $event.detail">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Dari</label>
                    <x-date-picker placeholder="Dari tanggal" align="left" />
                </div>

                <div @datepicker:change="dateTo = $event.detail">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Sampai</label>
                    <x-date-picker placeholder="Sampai tanggal" align="right" />
                </div>

                <div @selectbox:change="sort = $event.detail">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Urutkan</label>
                    <x-select-box :options="[
                        ['value' => 'recent', 'label' => 'Terbaru'],
                        ['value' => 'title', 'label' => 'Judul A-Z'],
                        ['value' => 'borrower', 'label' => 'Peminjam A-Z'],
                        ['value' => 'due', 'label' => 'Jatuh Tempo'],
                        ['value' => 'status', 'label' => 'Status'],
                    ]" placeholder="Urutkan" />
                </div>
            </div>
            <div class="mt-3">
                <p class="font-body text-xs text-[#747C82]">
                    <span x-text="filtered.length" class="text-white/70 font-semibold"></span> transaksi ditemukan
                </p>
            </div>
        </div>

        {{-- Table --}}
        <div class="glass overflow-hidden">
            <div class="overflow-x-auto">
                <table class="glass-table loans-table w-full min-w-[880px]">
                    <thead>
                        <tr class="border-b border-white/[0.06]">
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
                                    <div class="flex items-center gap-3">
                                        <template x-if="loan.cover_image">
                                            <img :src="loan.cover_image" alt="" class="h-10 w-8 object-cover rounded-md border border-white/10 flex-shrink-0">
                                        </template>
                                        <template x-if="!loan.cover_image">
                                            <div class="h-10 w-8 rounded-md border border-white/10 flex-shrink-0" :style="'background-color: ' + (['#2E3B4E','#3A5A53','#4E3A44','#52543A','#39425C','#5A4636','#4A4359','#3E4A48'])[Math.abs(loan.id) % 8]"></div>
                                        </template>
                                        <div class="min-w-0">
                                            <p class="font-body font-semibold text-[13px] text-white truncate max-w-[170px]" :title="loan.book_title" x-text="loan.book_title"></p>
                                            <p class="font-body text-[11px] text-[#747C82] font-mono mt-0.5" x-text="loan.isbn"></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <template x-if="loan.user_url">
                                        <a :href="loan.user_url" class="glass-link font-medium text-[13px]" x-text="loan.borrower_name"></a>
                                    </template>
                                    <template x-if="!loan.user_url">
                                        <span class="font-medium text-[13px] text-white" x-text="loan.borrower_name"></span>
                                    </template>
                                </td>
                                <td class="font-mono text-[11px] text-[#747C82]" x-text="loan.borrower_nisn || '-'"></td>
                                <td>
                                    <span class="text-[13px] text-white" x-text="loan.loan_date"></span>
                                    <span class="block font-body text-[11px] text-[#747C82] mt-0.5" x-text="loan.duration_days + ' hari'"></span>
                                </td>
                                <td>
                                    <span class="text-[13px] text-white" x-text="loan.due_date"></span>
                                </td>
                                <td>
                                    <span class="text-[13px]" :class="loan.returned_at === '-' ? 'text-[#747C82]' : 'text-white'" x-text="loan.returned_at"></span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-medium whitespace-nowrap" :class="{
                                        'sb-active': loan.status_key === 'active',
                                        'sb-overdue': loan.status_key === 'overdue' || loan.status_key === 'returned_late',
                                        'sb-returned': loan.status_key === 'returned_ontime',
                                    }">
                                        <span x-text="loan.status_label"></span>
                                        <template x-if="loan.status_detail">
                                            <span class="opacity-70" x-text="' (' + loan.status_detail + ')'"></span>
                                        </template>
                                    </span>
                                </td>
                                <td>
                                    <template x-if="loan.denda_text !== '-'">
                                        <div class="whitespace-nowrap">
                                            <p class="font-body font-semibold text-[13px] text-[#E76B73]" x-text="loan.denda_text"></p>
                                            <template x-if="loan.denda_action">
                                                <form :action="loan.denda_action" method="POST" class="mt-0.5">
                                                    @csrf
                                                    <button type="submit" data-loading-text="Memproses..." class="font-body text-[11px] text-sky-300 hover:text-sky-200 transition-colors">Tandai Lunas</button>
                                                </form>
                                            </template>
                                            <template x-if="!loan.denda_action && loan.denda_sub">
                                                <span class="font-body text-[11px]" :class="loan.denda_sub === 'Lunas' ? 'text-[#4CAF7D]' : 'text-[#747C82]'" x-text="loan.denda_sub"></span>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="loan.denda_text === '-'">
                                        <span class="text-[#747C82]">-</span>
                                    </template>
                                </td>
                                <template x-if="isStaff">
                                    <td>
                                        <template x-if="loan.processor_name !== '-'">
                                            <div class="whitespace-nowrap">
                                                <p class="font-body text-[13px] text-white/85" x-text="loan.processor_name"></p>
                                                <p class="font-body text-[11px] text-[#747C82] mt-0.5" x-text="loan.processed_at"></p>
                                            </div>
                                        </template>
                                        <template x-if="loan.processor_name === '-'">
                                            <span class="text-[#747C82]">-</span>
                                        </template>
                                    </td>
                                </template>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <tr x-show="filtered.length === 0">
                            <td :colspan="isStaff ? 9 : 8" class="py-14 text-center">
                                <svg class="w-9 h-9 mx-auto mb-3 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="font-display font-semibold text-[15px] text-white" x-text="loans.length === 0 ? 'Tidak ada riwayat peminjaman' : 'Tidak ada peminjaman ditemukan'"></p>
                                <p class="font-body text-xs text-[#747C82] mt-1" x-text="loans.length === 0 ? 'Belum ada aktivitas peminjaman buku.' : 'Coba ubah kata pencarian atau filter yang digunakan.'"></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>