<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="font-display text-2xl lg:text-[26px] font-bold tracking-tight text-white leading-tight">
                        Dashboard
                    </h2>
                    @if (Auth::user()->isStaff())
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 border border-emerald-500/20 text-emerald-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse-soft"></span>
                            Sistem aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-violet-500/10 border border-violet-500/20 text-violet-300">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Dashboard Anggota
                        </span>
                    @endif
                </div>
                <p class="font-body text-sm text-white/50 mt-1.5">
                    @if (Auth::user()->isStaff())
                        Selamat datang kembali, {{ Auth::user()->name }} 👋
                    @else
                        Halo {{ Auth::user()->name }} — pantau pinjamanmu di sini 👋
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $statIcons = [
            'book'  => ['M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            'book-open' => ['M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z', 'M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z'],
            'clock' => ['M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            'check' => ['M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            'alert' => ['M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
            'history' => ['M3 3v5h5', 'M3.05 13A9 9 0 106 5.3L3 8'],
        ];
    @endphp

    <div class="space-y-6" x-data="reveal">
        @if (Auth::user()->isStaff())
            {{-- ============ STAFF / ADMIN ============ --}}

            {{-- Summary Cards --}}
            @php
                $staffStats = [
                    ['label' => 'Total Buku', 'value' => $totalBooks, 'sub' => 'Koleksi tersedia', 'icon' => $statIcons['book'], 'tint' => 'text-violet-300 bg-violet-500/10 border-violet-500/20'],
                    ['label' => 'Dipinjam', 'value' => $activeLoans, 'sub' => 'Sedang dipinjam', 'icon' => $statIcons['book-open'], 'tint' => 'text-sky-300 bg-sky-500/10 border-sky-500/20'],
                    ['label' => 'Terlambat', 'value' => $overdueLoans, 'sub' => 'Melewati jatuh tempo', 'icon' => $statIcons['clock'], 'tint' => 'text-rose-300 bg-rose-500/10 border-rose-500/20'],
                    ['label' => 'Dikembalikan', 'value' => $returnedLoans, 'sub' => 'Total pengembalian', 'icon' => $statIcons['check'], 'tint' => 'text-emerald-300 bg-emerald-500/10 border-emerald-500/20'],
                ];
            @endphp
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach ($staffStats as $stat)
                    <div class="glass p-4 sm:p-5" style="animation-delay: {{ $loop->index * 60 }}ms;">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-body text-[11px] font-semibold uppercase tracking-wider text-white/50 truncate">{{ $stat['label'] }}</p>
                                <p x-data="countUp"
                                   data-count="{{ $stat['value'] }}"
                                   class="font-display text-[28px] sm:text-[32px] font-bold leading-none mt-2 text-white tabular-nums"
                                   x-text="displayed.toLocaleString('id-ID')"></p>
                                <p class="font-body text-xs text-white/40 mt-2 truncate">{{ $stat['sub'] }}</p>
                            </div>
                            <span class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl border flex items-center justify-center flex-shrink-0 {{ $stat['tint'] }}">
                                <svg class="w-5 h-5 sm:w-[22px] sm:h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    @foreach ($stat['icon'] as $d)<path d="{{ $d }}"/>@endforeach
                                </svg>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Quick Actions --}}
            <div>
                <h3 class="font-display text-base font-semibold text-white mb-3">Akses Cepat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <a href="{{ route('books.index') }}" class="quick-action group">
                        <span class="w-11 h-11 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-300 flex items-center justify-center flex-shrink-0 transition-colors duration-200 group-hover:bg-violet-500/15">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                @foreach ($statIcons['book'] as $d)<path d="{{ $d }}"/>@endforeach
                            </svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-display text-sm font-semibold text-white">Katalog Buku</span>
                            <span class="block font-body text-xs text-white/40 mt-0.5 truncate">Lihat & kelola koleksi buku</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/60 transition-colors duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('loans.borrow.create') }}" class="quick-action group">
                        <span class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-300 flex items-center justify-center flex-shrink-0 transition-colors duration-200 group-hover:bg-sky-500/15">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v16m8-8H4"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-display text-sm font-semibold text-white">Pinjam Buku</span>
                            <span class="block font-body text-xs text-white/40 mt-0.5 truncate">Scan & pinjam buku baru</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/60 transition-colors duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('loans.return.create') }}" class="quick-action group">
                        <span class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 flex items-center justify-center flex-shrink-0 transition-colors duration-200 group-hover:bg-emerald-500/15">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-display text-sm font-semibold text-white">Kembalikan Buku</span>
                            <span class="block font-body text-xs text-white/40 mt-0.5 truncate">Scan & proses pengembalian</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/60 transition-colors duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                    </a>
                </div>
            </div>

            {{-- Statistics --}}
            <div>
                <div class="flex items-center justify-between gap-4 mb-3">
                    <div>
                        <h3 class="font-display text-lg font-semibold text-white leading-tight">Statistik</h3>
                        <p class="font-body text-xs text-white/40 mt-0.5">Ringkasan aktivitas perpustakaan</p>
                    </div>
                    <span class="glass-badge-gray hidden sm:inline-flex">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 106 5.3L3 8"/></svg>
                        6 bulan terakhir
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">
                    {{-- Buku Terpopuler --}}
                    <div class="glass p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-display text-[15px] font-semibold text-white leading-tight">Buku Terpopuler</h4>
                                <p class="font-body text-xs text-white/40 mt-1">Paling sering dipinjam</p>
                            </div>
                            <span class="w-9 h-9 rounded-lg bg-white/[0.05] border border-white/[0.08] text-white/40 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                            </span>
                        </div>
                        <div class="relative mt-4" style="height: 230px;">
                            <canvas id="chartTopBooks"></canvas>
                            <div id="emptyTopBooks" class="empty-chart hidden">
                                <span class="w-12 h-12 rounded-full bg-white/[0.05] border border-white/[0.08] text-white/30 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                                </span>
                                <p class="font-display font-semibold text-sm text-white/75 mt-3">Belum cukup data</p>
                                <p class="font-body text-xs text-white/40 mt-1 max-w-[230px] leading-relaxed">Statistik akan muncul setelah ada aktivitas peminjaman.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Peminjaman / Bulan --}}
                    <div class="glass p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-display text-[15px] font-semibold text-white leading-tight">Peminjaman / Bulan</h4>
                                <p class="font-body text-xs text-white/40 mt-1">Tren aktivitas perpustakaan</p>
                            </div>
                            <span class="w-9 h-9 rounded-lg bg-white/[0.05] border border-white/[0.08] text-white/40 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 7l-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/></svg>
                            </span>
                        </div>
                        <div class="relative mt-4" style="height: 230px;">
                            <canvas id="chartMonthly"></canvas>
                            <div id="emptyMonthly" class="empty-chart hidden">
                                <span class="w-12 h-12 rounded-full bg-white/[0.05] border border-white/[0.08] text-white/30 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 7l-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/></svg>
                                </span>
                                <p class="font-display font-semibold text-sm text-white/75 mt-3">Belum cukup data</p>
                                <p class="font-body text-xs text-white/40 mt-1 max-w-[230px] leading-relaxed">Statistik akan muncul setelah ada aktivitas peminjaman.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Status Buku --}}
                    <div class="glass p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-display text-[15px] font-semibold text-white leading-tight">Status Buku</h4>
                                <p class="font-body text-xs text-white/40 mt-1">Ketersediaan koleksi</p>
                            </div>
                            <span class="w-9 h-9 rounded-lg bg-white/[0.05] border border-white/[0.08] text-white/40 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 118 2.83"/><path d="M22 12A10 10 0 0012 2v10z"/></svg>
                            </span>
                        </div>
                        <div class="relative mt-4" style="height: 230px;">
                            <canvas id="chartStatus"></canvas>
                            <div id="emptyStatus" class="empty-chart hidden">
                                <span class="w-12 h-12 rounded-full bg-white/[0.05] border border-white/[0.08] text-white/30 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 118 2.83"/><path d="M22 12A10 10 0 0012 2v10z"/></svg>
                                </span>
                                <p class="font-display font-semibold text-sm text-white/75 mt-3">Belum cukup data</p>
                                <p class="font-body text-xs text-white/40 mt-1 max-w-[230px] leading-relaxed">Statistik akan muncul setelah ada aktivitas peminjaman.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="glass p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h4 class="font-display text-[15px] font-semibold text-white">Aktivitas Terakhir</h4>
                        <p class="font-body text-xs text-white/40 mt-1">Riwayat peminjaman terbaru</p>
                    </div>
                    <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-sky-300 hover:text-sky-200 transition-colors shrink-0">
                        Lihat riwayat
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                    </a>
                </div>
                <div class="space-y-1">
                    @forelse ($recentActivity as $loan)
                        <div class="flex items-center gap-3 rounded-[10px] px-2 py-2.5 hover:bg-white/[0.03] transition-colors">
                            @if ($loan->book->cover_url)
                                <img src="{{ $loan->book->cover_url }}" alt="" class="h-10 w-8 object-cover rounded-md border border-white/10 flex-shrink-0">
                            @else
                                <div class="h-10 w-8 rounded-md bg-white/[0.05] border border-white/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">@foreach ($statIcons['book'] as $d)<path d="{{ $d }}"/>@endforeach</svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-display font-medium text-sm text-white truncate">{{ $loan->book->title ?? '-' }}</p>
                                <p class="font-body text-xs text-white/40 mt-0.5 truncate">{{ $loan->user->name ?? '-' }} · {{ $loan->loan_date->format('d/m/Y') }}</p>
                            </div>
                            <span class="glass-badge flex-shrink-0 {{ $loan->isReturned() ? 'glass-badge-green' : ($loan->isOverdue() ? 'glass-badge-red' : 'glass-badge-yellow') }}">
                                {{ $loan->isReturned() ? 'Kembali' : ($loan->isOverdue() ? 'Telat' : 'Dipinjam') }}
                            </span>
                        </div>
                    @empty
                        <div class="py-10 flex flex-col items-center justify-center text-center">
                            <span class="w-12 h-12 rounded-full bg-white/[0.05] border border-white/[0.08] text-white/30 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 106 5.3L3 8"/></svg>
                            </span>
                            <p class="font-body text-sm text-white/40">Belum ada aktivitas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            {{-- ============ MEMBER ============ --}}

            {{-- Personal Summary --}}
            @php
                $memberStats = [
                    ['label' => 'Sedang Dipinjam', 'value' => $activeCount, 'money' => false, 'sub' => 'Buku yang kamu pinjam', 'icon' => $statIcons['book-open'], 'tint' => 'text-sky-300 bg-sky-500/10 border-sky-500/20'],
                    ['label' => 'Terlambat', 'value' => $overdueCount, 'money' => false, 'sub' => 'Melewati jatuh tempo', 'icon' => $statIcons['clock'], 'tint' => 'text-rose-300 bg-rose-500/10 border-rose-500/20'],
                    ['label' => 'Denda Belum Bayar', 'value' => $totalDenda, 'money' => true, 'sub' => 'Jumlah denda aktif', 'icon' => $statIcons['alert'], 'tint' => 'text-amber-300 bg-amber-500/10 border-amber-500/20'],
                    ['label' => 'Dipinjam Bulan Ini', 'value' => $totalThisMonth, 'money' => false, 'sub' => 'Sepanjang bulan berjalan', 'icon' => $statIcons['book'], 'tint' => 'text-violet-300 bg-violet-500/10 border-violet-500/20'],
                ];
            @endphp
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach ($memberStats as $stat)
                    <div class="glass p-4 sm:p-5" style="animation-delay: {{ $loop->index * 60 }}ms;">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-body text-[11px] font-semibold uppercase tracking-wider text-white/50 truncate">{{ $stat['label'] }}</p>
                                <p x-data="countUp"
                                   data-count="{{ $stat['value'] }}"
                                   class="font-display text-[28px] sm:text-[32px] font-bold leading-none mt-2 text-white tabular-nums"
                                   x-text="'{{ $stat['money'] ? 'Rp ' : '' }}' + displayed.toLocaleString('id-ID')"></p>
                                <p class="font-body text-xs text-white/40 mt-2 truncate">{{ $stat['sub'] }}</p>
                            </div>
                            <span class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl border flex items-center justify-center flex-shrink-0 {{ $stat['tint'] }}">
                                <svg class="w-5 h-5 sm:w-[22px] sm:h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    @foreach ($stat['icon'] as $d)<path d="{{ $d }}"/>@endforeach
                                </svg>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Recent Books --}}
            <div>
                <div class="flex items-center justify-between gap-4 mb-3">
                    <div>
                        <h3 class="font-display text-base font-semibold text-white leading-tight">Koleksi Buku Terbaru</h3>
                        <p class="font-body text-xs text-white/40 mt-0.5">Temukan bacaan terbaru di perpustakaan</p>
                    </div>
                    <a href="{{ route('books.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-sky-300 hover:text-sky-200 transition-colors shrink-0">
                        Jelajahi semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
                    @foreach ($recentBooks as $book)
                        <a href="{{ route('books.show', $book) }}" class="glass glass-hover p-3 sm:p-4 group">
                            <div class="relative mb-3">
                                @if ($book->cover_url)
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full aspect-[3/4] object-cover rounded-lg border border-white/10">
                                @else
                                    <div class="w-full aspect-[3/4] rounded-lg bg-white/[0.05] border border-white/10 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">@foreach ($statIcons['book'] as $d)<path d="{{ $d }}"/>@endforeach</svg>
                                    </div>
                                @endif
                                @if ($book->stock > 0)
                                    <span class="absolute top-2 right-2 glass-badge glass-badge-green">Tersedia</span>
                                @else
                                    <span class="absolute top-2 right-2 glass-badge glass-badge-red">Habis</span>
                                @endif
                            </div>
                            <p class="font-display font-semibold text-sm text-white truncate group-hover:text-violet-300 transition-colors">{{ $book->title }}</p>
                            <p class="font-body text-xs text-white/40 truncate mt-0.5">{{ $book->author }}</p>
                            @if ($book->kategori)
                                <p class="font-body text-xs text-white/30 mt-1">{{ $book->kategori }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Due Soon --}}
            @if ($dueSoon->isNotEmpty())
                <div class="glass p-4 sm:p-5 border-amber-500/25">
                    <div class="flex items-center gap-2.5 mb-4">
                        <span class="w-9 h-9 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <h3 class="font-display text-base font-semibold text-white leading-tight">Jatuh Tempo dalam 7 Hari</h3>
                            <p class="font-body text-xs text-white/40 mt-0.5">Jangan lupa kembalikan buku tepat waktu</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                        @foreach ($dueSoon as $loan)
                            @php
                                $daysLeft = \Carbon\Carbon::today()->diffInDays($loan->due_date, false);
                            @endphp
                            <div class="glass-inset rounded-glass p-3 flex items-center gap-3">
                                @if ($loan->book->cover_url)
                                    <img src="{{ $loan->book->cover_url }}" alt="{{ $loan->book->title }}" class="h-12 w-9 object-cover rounded-lg flex-shrink-0 border border-white/10">
                                @else
                                    <div class="h-12 w-9 rounded-lg bg-white/[0.05] border border-white/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">@foreach ($statIcons['book'] as $d)<path d="{{ $d }}"/>@endforeach</svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-display font-medium text-sm text-white truncate">{{ $loan->book->title }}</p>
                                    <p class="font-body text-xs text-white/40 mt-0.5">{{ $loan->due_date->format('d M Y') }}</p>
                                </div>
                                <span class="glass-badge flex-shrink-0 {{ $daysLeft <= 2 ? 'glass-badge-red' : 'glass-badge-yellow' }}">
                                    H-{{ $daysLeft }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Active Loans --}}
            <div>
                <div class="flex items-center justify-between gap-4 mb-3">
                    <div>
                        <h3 class="font-display text-base font-semibold text-white leading-tight">Pinjaman Aktif Saya</h3>
                        <p class="font-body text-xs text-white/40 mt-0.5">Buku yang sedang kamu pinjam</p>
                    </div>
                    <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-sky-300 hover:text-sky-200 transition-colors shrink-0">
                        Lihat riwayat
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                    </a>
                </div>

                @if ($activeLoans->isEmpty())
                    <div class="glass p-10 sm:p-12 flex flex-col items-center justify-center text-center">
                        <span class="w-14 h-14 rounded-full bg-white/[0.05] border border-white/[0.08] text-white/30 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                        <p class="font-display font-semibold text-lg text-white">Belum ada pinjaman aktif</p>
                        <p class="font-body text-sm text-white/40 mt-1">Temukan buku favoritmu dan mulai meminjam.</p>
                        <a href="{{ route('loans.borrow.create') }}" class="glass-btn-primary mt-6">Pinjam Buku Sekarang</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                        @foreach ($activeLoans as $loan)
                            @php
                                $daysLeft = \Carbon\Carbon::today()->diffInDays($loan->due_date, false);
                            @endphp
                            <div class="glass glass-hover p-5 {{ $loan->isOverdue() ? 'border-rose-500/25' : '' }}">
                                <div class="flex items-start gap-4">
                                    @if ($loan->book->cover_url)
                                        <img src="{{ $loan->book->cover_url }}" alt="{{ $loan->book->title }}" class="h-16 w-12 object-cover rounded-lg flex-shrink-0 border border-white/10">
                                    @else
                                        <div class="h-16 w-12 rounded-lg bg-white/[0.05] border border-white/10 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">@foreach ($statIcons['book'] as $d)<path d="{{ $d }}"/>@endforeach</svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="font-display font-semibold text-sm text-white truncate">{{ $loan->book->title }}</p>
                                        <p class="font-body text-xs text-white/40 mt-0.5 truncate">{{ $loan->book->author }}</p>
                                        <span class="glass-badge mt-2 {{ $loan->isOverdue() ? 'glass-badge-red' : ($daysLeft <= 3 ? 'glass-badge-yellow' : 'glass-badge-green') }}">
                                            {{ $loan->isOverdue() ? $loan->getDaysLate() . 'h telat' : ($daysLeft == 0 ? 'Jatuh tempo hari ini' : 'H-' . $daysLeft) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-white/[0.07] flex items-center justify-between">
                                    <span class="font-body text-xs text-white/40">Tempo: {{ $loan->due_date->format('d M Y') }}</span>
                                    @if ($loan->isOverdue())
                                        <span class="font-body text-xs text-rose-300 font-semibold">Rp{{ number_format($loan->getPotentialDenda(), 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Recent History --}}
            @if ($recentHistory->isNotEmpty())
                <div class="glass p-5">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <h4 class="font-display text-[15px] font-semibold text-white">Riwayat Terakhir</h4>
                            <p class="font-body text-xs text-white/40 mt-1">Buku yang baru kamu kembalikan</p>
                        </div>
                        <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-sky-300 hover:text-sky-200 transition-colors shrink-0">
                            Semua
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                        </a>
                    </div>
                    <div class="space-y-1">
                        @foreach ($recentHistory as $loan)
                            <div class="flex items-center gap-3 rounded-[10px] px-2 py-2.5 hover:bg-white/[0.03] transition-colors">
                                @if ($loan->book->cover_url)
                                    <img src="{{ $loan->book->cover_url }}" alt="" class="h-10 w-8 object-cover rounded-md border border-white/10 flex-shrink-0">
                                @else
                                    <div class="h-10 w-8 rounded-md bg-white/[0.05] border border-white/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">@foreach ($statIcons['book'] as $d)<path d="{{ $d }}"/>@endforeach</svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-display font-medium text-sm text-white truncate">{{ $loan->book->title ?? '-' }}</p>
                                    <p class="font-body text-xs text-white/40">Dikembalikan {{ $loan->returned_at->format('d/m/Y') }}</p>
                                </div>
                                <span class="glass-badge flex-shrink-0 {{ $loan->denda > 0 ? 'glass-badge-red' : 'glass-badge-green' }}">
                                    {{ $loan->denda > 0 ? 'Rp' . number_format($loan->denda, 0, ',', '.') : 'Tepat waktu' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Member Quick Actions --}}
            <div>
                <h3 class="font-display text-base font-semibold text-white mb-3">Akses Cepat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <a href="{{ route('books.index') }}" class="quick-action group">
                        <span class="w-11 h-11 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-300 flex items-center justify-center flex-shrink-0 transition-colors duration-200 group-hover:bg-violet-500/15">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">@foreach ($statIcons['book'] as $d)<path d="{{ $d }}"/>@endforeach</svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-display text-sm font-semibold text-white">Katalog Buku</span>
                            <span class="block font-body text-xs text-white/40 mt-0.5 truncate">Jelajahi koleksi perpustakaan</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/60 transition-colors duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('loans.borrow.create') }}" class="quick-action group">
                        <span class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-300 flex items-center justify-center flex-shrink-0 transition-colors duration-200 group-hover:bg-sky-500/15">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v16m8-8H4"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-display text-sm font-semibold text-white">Pinjam Buku</span>
                            <span class="block font-body text-xs text-white/40 mt-0.5 truncate">Scan & pinjam buku baru</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/60 transition-colors duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('loans.index') }}" class="quick-action group">
                        <span class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 flex items-center justify-center flex-shrink-0 transition-colors duration-200 group-hover:bg-emerald-500/15">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">@foreach ($statIcons['history'] as $d)<path d="{{ $d }}"/>@endforeach</svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-display text-sm font-semibold text-white">Riwayat Peminjaman</span>
                            <span class="block font-body text-xs text-white/40 mt-0.5 truncate">Pantau status & denda pinjaman</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/60 transition-colors duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7-7 7M21 12H3"/></svg>
                    </a>
                </div>
            </div>
        @endif
    </div>

    @if (Auth::user()->isStaff())
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const palette = {
                    primary: '#6d5cff',
                    violet: '#8b5cf6',
                    cyan: '#22d3ee',
                    rose: '#fb5e63',
                    emerald: '#34d399',
                    card: '#131a2a',
                    border: 'rgba(255, 255, 255, 0.08)',
                    grid: 'rgba(255, 255, 255, 0.06)',
                    text: '#ffffff',
                    muted: 'rgba(255, 255, 255, 0.45)',
                    label: 'rgba(255, 255, 255, 0.65)',
                };

                const tooltip = {
                    backgroundColor: '#0f1422',
                    borderColor: 'rgba(255, 255, 255, 0.12)',
                    borderWidth: 1,
                    titleColor: '#ffffff',
                    titleFont: { weight: '600' },
                    bodyColor: 'rgba(255, 255, 255, 0.75)',
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 10,
                    boxPadding: 4,
                    usePointStyle: true,
                };

                const showEmpty = (emptyId, canvasId) => {
                    document.getElementById(canvasId).classList.add('hidden');
                    document.getElementById(emptyId).classList.remove('hidden');
                };
                const hasData = (arr) => arr.some((v) => Number(v) > 0);

                Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
                Chart.defaults.color = palette.muted;
                Chart.defaults.borderColor = palette.border;

                fetch('{{ route("dashboard.stats") }}')
                    .then(res => res.json())
                    .then(data => {
                        // 1. Buku Terpopuler — horizontal bar
                        if (hasData(data.topBooks.data)) {
                            new Chart(document.getElementById('chartTopBooks'), {
                                type: 'bar',
                                data: {
                                    labels: data.topBooks.labels,
                                    datasets: [{
                                        label: 'Peminjaman',
                                        data: data.topBooks.data,
                                        backgroundColor: 'rgba(124, 108, 246, 0.7)',
                                        hoverBackgroundColor: 'rgba(124, 108, 246, 0.95)',
                                        borderRadius: 6,
                                        borderSkipped: false,
                                        barThickness: 14,
                                        maxBarThickness: 18,
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    indexAxis: 'y',
                                    plugins: { legend: { display: false }, tooltip: tooltip },
                                    scales: {
                                        x: { border: { display: false }, grid: { color: palette.grid }, ticks: { precision: 0, stepSize: 1, font: { size: 10 } }, beginAtZero: true },
                                        y: { border: { display: false }, grid: { display: false }, ticks: { color: palette.label, font: { size: 11 } } },
                                    },
                                },
                            });
                        } else {
                            showEmpty('emptyTopBooks', 'chartTopBooks');
                        }

                        // 2. Peminjaman / Bulan — line chart
                        if (hasData(data.monthlyLoans.data)) {
                            new Chart(document.getElementById('chartMonthly'), {
                                type: 'line',
                                data: {
                                    labels: data.monthlyLoans.labels,
                                    datasets: [{
                                        label: 'Peminjaman',
                                        data: data.monthlyLoans.data,
                                        borderColor: palette.primary,
                                        backgroundColor: (ctx) => {
                                            const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 230);
                                            g.addColorStop(0, 'rgba(109, 92, 255, 0.22)');
                                            g.addColorStop(1, 'rgba(109, 92, 255, 0)');
                                            return g;
                                        },
                                        borderWidth: 2,
                                        fill: true,
                                        tension: 0.35,
                                        pointRadius: 3,
                                        pointBackgroundColor: palette.card,
                                        pointBorderColor: palette.primary,
                                        pointBorderWidth: 1.5,
                                        hoverPointRadius: 5,
                                        hoverPointBorderWidth: 2,
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false }, tooltip: tooltip },
                                    scales: {
                                        x: { border: { display: false }, grid: { display: false }, ticks: { font: { size: 10 } } },
                                        y: { border: { display: false }, grid: { color: palette.grid }, ticks: { precision: 0, stepSize: 1, font: { size: 10 } }, beginAtZero: true },
                                    },
                                },
                            });
                        } else {
                            showEmpty('emptyMonthly', 'chartMonthly');
                        }

                        // 3. Status Buku — donut
                        const statusTotal = data.bookStatus.data.reduce((a, b) => a + Number(b), 0);
                        if (statusTotal > 0) {
                            new Chart(document.getElementById('chartStatus'), {
                                type: 'doughnut',
                                data: {
                                    labels: data.bookStatus.labels,
                                    datasets: [{
                                        data: data.bookStatus.data,
                                        backgroundColor: [palette.emerald, palette.primary, palette.rose],
                                        borderColor: palette.card,
                                        borderWidth: 3,
                                        hoverOffset: 6,
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '70%',
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: { color: palette.label, font: { size: 11 }, padding: 16, usePointStyle: true, pointStyleWidth: 9 },
                                        },
                                        tooltip: tooltip,
                                    },
                                },
                            });
                        } else {
                            showEmpty('emptyStatus', 'chartStatus');
                        }
                    })
                    .catch(err => {
                        console.error('Gagal memuat data statistik:', err);
                        window.toast('Gagal memuat data statistik', 'error');
                    });
            });
        </script>
    @endif
</x-app-layout>
