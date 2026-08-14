<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-[25px] font-semibold tracking-tight text-white leading-tight">Dashboard</h2>
            <p class="font-body text-[13px] text-white/30 mt-1">
                @if (Auth::user()->isStaff())
                    Selamat datang kembali, {{ Auth::user()->name }}.
                @else
                    Halo {{ Auth::user()->name }} — pantau pinjamanmu di sini.
                @endif
            </p>
        </div>
    </x-slot>

    @php
        $icon = [
            'book' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'book-open' => 'M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z',
            'plus' => 'M12 4v16m8-8H4',
            'arrow' => 'M14 5l7 7-7 7M21 12H3',
            'history' => 'M3 3v5h5M3.05 13A9 9 0 106 5.3L3 8',
        ];

        $activityBadge = fn ($loan) => $loan->isReturned()
            ? ['Kembali', 'text-emerald-300 bg-emerald-500/10']
            : ($loan->isOverdue() ? ['Telat', 'text-rose-300 bg-rose-500/10'] : ['Dipinjam', 'text-sky-300 bg-sky-500/10']);
    @endphp

    <div class="space-y-6" x-data="reveal">
        @if (Auth::user()->isStaff())
            {{-- ============ STAFF / ADMIN ============ --}}

            {{-- Ringkasan --}}
            @php
                $staffStats = [
                    ['label' => 'Total Buku', 'value' => $totalBooks, 'sub' => 'Koleksi tersedia', 'red' => false],
                    ['label' => 'Sedang Dipinjam', 'value' => $activeLoans, 'sub' => 'Buku aktif dipinjam', 'red' => false],
                    ['label' => 'Terlambat', 'value' => $overdueLoans, 'sub' => 'Melewati jatuh tempo', 'red' => true],
                    ['label' => 'Dikembalikan', 'value' => $returnedLoans, 'sub' => 'Total pengembalian', 'red' => false],
                ];
            @endphp
            <div class="panel overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-4 divide-y divide-white/[0.05] lg:divide-y-0 lg:divide-x">
                    @foreach ($staffStats as $stat)
                        <div class="px-6 py-5">
                            <p class="font-body text-[11px] font-medium uppercase tracking-wider text-white/35 truncate">{{ $stat['label'] }}</p>
                            <p x-data="countUp"
                               data-count="{{ $stat['value'] }}"
                               class="mt-2.5 font-display text-[30px] leading-none font-bold tabular-nums {{ $stat['red'] ? 'text-rose-300' : 'text-white' }}"
                               x-text="displayed.toLocaleString('id-ID')"></p>
                            <p class="mt-2 font-body text-xs text-white/30 truncate">{{ $stat['sub'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Akses Cepat --}}
            <div class="panel">
                <div class="px-5 py-3.5 border-b border-white/[0.06]">
                    <h3 class="font-display text-[15px] font-semibold text-white">Akses Cepat</h3>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 divide-y divide-white/[0.05] lg:divide-y-0 lg:divide-x">
                    <a href="{{ route('books.index') }}" class="group flex items-center gap-3 px-5 py-4 transition-colors">
                        <span class="qa-icon w-9 h-9 rounded-[8px] border flex items-center justify-center transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['book'] }}"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-body text-sm font-medium text-white">Katalog Buku</span>
                            <span class="block font-body text-xs text-white/30 mt-0.5 truncate">Kelola koleksi buku</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/50 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['arrow'] }}"/></svg>
                    </a>
                    <a href="{{ route('loans.borrow.create') }}" class="group flex items-center gap-3 px-5 py-4 transition-colors">
                        <span class="qa-icon w-9 h-9 rounded-[8px] border flex items-center justify-center transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['plus'] }}"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-body text-sm font-medium text-white">Pinjam Buku</span>
                            <span class="block font-body text-xs text-white/30 mt-0.5 truncate">Scan & pinjam buku</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/50 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['arrow'] }}"/></svg>
                    </a>
                    <a href="{{ route('loans.return.create') }}" class="group flex items-center gap-3 px-5 py-4 transition-colors">
                        <span class="qa-icon w-9 h-9 rounded-[8px] border flex items-center justify-center transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['book-open'] }}"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-body text-sm font-medium text-white">Kembalikan Buku</span>
                            <span class="block font-body text-xs text-white/30 mt-0.5 truncate">Proses pengembalian</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/50 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['arrow'] }}"/></svg>
                    </a>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="panel overflow-hidden">
                <div class="flex items-center justify-between gap-4 px-5 py-4 border-b border-white/[0.06]">
                    <div>
                        <h3 class="font-display text-[15px] font-semibold text-white leading-tight">Statistik</h3>
                        <p class="font-body text-xs text-white/30 mt-0.5">Aktivitas perpustakaan 6 bulan terakhir</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 divide-y divide-white/[0.05] lg:divide-y-0 lg:divide-x">
                    <div class="p-5 lg:col-span-2">
                        <h4 class="font-body text-sm font-medium text-white">Peminjaman per Bulan</h4>
                        <div class="relative mt-4 h-[240px]">
                            <canvas id="chartMonthly"></canvas>
                            <div id="emptyMonthly" class="empty-chart hidden">
                                <p class="font-body text-sm text-white/45">Belum ada data</p>
                                <p class="font-body text-xs text-white/30 mt-1">Data muncul setelah ada peminjaman</p>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <div class="p-5">
                            <h4 class="font-body text-sm font-medium text-white">Buku Terpopuler</h4>
                            <div class="relative mt-4 h-[110px]">
                                <canvas id="chartTopBooks"></canvas>
                                <div id="emptyTopBooks" class="empty-chart hidden">
                                    <p class="font-body text-sm text-white/45">Belum ada data</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-5 border-t border-white/[0.05]">
                            <h4 class="font-body text-sm font-medium text-white">Status Buku</h4>
                            <div class="relative mt-4 h-[90px]">
                                <canvas id="chartStatus"></canvas>
                                <div id="emptyStatus" class="empty-chart hidden">
                                    <p class="font-body text-sm text-white/45">Belum ada data</p>
                                </div>
                            </div>
                            <div id="statusLegend" class="mt-3 flex flex-wrap items-center justify-center gap-x-5 gap-y-1.5"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aktivitas Terakhir --}}
            <div>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-display text-[15px] font-semibold text-white">Aktivitas Terakhir</h3>
                        <p class="font-body text-xs text-white/30 mt-0.5">Riwayat peminjaman terbaru</p>
                    </div>
                    <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-white/50 hover:text-white transition-colors shrink-0">
                        Lihat semua riwayat
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['arrow'] }}"/></svg>
                    </a>
                </div>
                <div class="panel overflow-hidden mt-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[560px]">
                        <thead>
                            <tr class="bg-white/[0.04] border-b border-white/[0.05] text-left font-body text-[11px] uppercase tracking-wider text-white/30">
                                <th class="px-5 py-3 font-medium">Buku</th>
                                <th class="px-5 py-3 font-medium">Anggota</th>
                                <th class="px-5 py-3 font-medium hidden sm:table-cell">Aktivitas</th>
                                <th class="px-5 py-3 font-medium hidden md:table-cell">Tanggal</th>
                                <th class="px-5 py-3 font-medium text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentActivity as $loan)
                                @php [$badgeLabel, $badgeClass] = $activityBadge($loan); @endphp
                                <tr class="border-b border-white/[0.04] last:border-0 hover:bg-white/[0.05] transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3 min-w-0">
                                            @if ($loan->book?->cover_url)
                                                <img src="{{ $loan->book->cover_url }}" alt="" class="h-10 w-8 object-cover rounded-[6px] border border-white/10 flex-shrink-0">
                                            @else
                                                <div class="h-10 w-8 rounded-[6px] bg-white/[0.04] border border-white/10 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['book'] }}"/></svg>
                                                </div>
                                            @endif
                                            <span class="font-body font-medium text-white truncate max-w-[220px]">{{ $loan->book->title ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="font-body text-white/85">{{ $loan->user->name ?? '-' }}</span>
                                        @if ($loan->user?->nisn)
                                            <span class="block font-body text-xs text-white/35 mt-0.5">{{ $loan->user->nisn }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 hidden sm:table-cell font-body text-white/55">{{ $loan->isReturned() ? 'Pengembalian' : 'Peminjaman' }}</td>
                                    <td class="px-5 py-3.5 hidden md:table-cell font-body text-white/45">{{ $loan->loan_date->format('d M Y') }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full font-body text-[11px] font-medium {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center">
                                        <p class="font-body text-sm text-white/45">Belum ada aktivitas</p>
                                        <p class="font-body text-xs text-white/30 mt-1">Aktivitas peminjaman akan tampil di sini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        @else
            {{-- ============ MEMBER ============ --}}

            {{-- Ringkasan --}}
            @php
                $memberStats = [
                    ['label' => 'Sedang Dipinjam', 'value' => $activeCount, 'sub' => 'Buku yang kamu pinjam', 'red' => false, 'money' => false],
                    ['label' => 'Terlambat', 'value' => $overdueCount, 'sub' => 'Melewati jatuh tempo', 'red' => true, 'money' => false],
                    ['label' => 'Denda Belum Bayar', 'value' => $totalDenda, 'sub' => 'Jumlah denda aktif', 'red' => $totalDenda > 0, 'money' => true],
                    ['label' => 'Dipinjam Bulan Ini', 'value' => $totalThisMonth, 'sub' => 'Sepanjang bulan berjalan', 'red' => false, 'money' => false],
                ];
            @endphp
            <div class="panel overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-4 divide-y divide-white/[0.05] lg:divide-y-0 lg:divide-x">
                    @foreach ($memberStats as $stat)
                        <div class="px-6 py-5">
                            <p class="font-body text-[11px] font-medium uppercase tracking-wider text-white/35 truncate">{{ $stat['label'] }}</p>
                            <p x-data="countUp"
                               data-count="{{ $stat['value'] }}"
                               class="mt-2.5 font-display text-[30px] leading-none font-bold tabular-nums {{ $stat['red'] ? 'text-rose-300' : 'text-white' }}"
                               x-text="'{{ $stat['money'] ? 'Rp ' : '' }}' + displayed.toLocaleString('id-ID')"></p>
                            <p class="mt-2 font-body text-xs text-white/30 truncate">{{ $stat['sub'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Akses Cepat --}}
            <div class="panel">
                <div class="px-5 py-3.5 border-b border-white/[0.06]">
                    <h3 class="font-display text-[15px] font-semibold text-white">Akses Cepat</h3>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 divide-y divide-white/[0.05] lg:divide-y-0 lg:divide-x">
                    <a href="{{ route('books.index') }}" class="group flex items-center gap-3 px-5 py-4 transition-colors">
                        <span class="qa-icon w-9 h-9 rounded-[8px] border flex items-center justify-center transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['book'] }}"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-body text-sm font-medium text-white">Katalog Buku</span>
                            <span class="block font-body text-xs text-white/30 mt-0.5 truncate">Jelajahi koleksi perpustakaan</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/50 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['arrow'] }}"/></svg>
                    </a>
                    <a href="{{ route('loans.borrow.create') }}" class="group flex items-center gap-3 px-5 py-4 transition-colors">
                        <span class="qa-icon w-9 h-9 rounded-[8px] border flex items-center justify-center transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['plus'] }}"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-body text-sm font-medium text-white">Pinjam Buku</span>
                            <span class="block font-body text-xs text-white/30 mt-0.5 truncate">Scan & pinjam buku</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/50 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['arrow'] }}"/></svg>
                    </a>
                    <a href="{{ route('loans.index') }}" class="group flex items-center gap-3 px-5 py-4 transition-colors">
                        <span class="qa-icon w-9 h-9 rounded-[8px] border flex items-center justify-center transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['history'] }}"/></svg>
                        </span>
                        <span class="flex-1 min-w-0">
                            <span class="block font-body text-sm font-medium text-white">Riwayat Peminjaman</span>
                            <span class="block font-body text-xs text-white/30 mt-0.5 truncate">Pantau status & denda</span>
                        </span>
                        <svg class="w-4 h-4 text-white/25 group-hover:text-white/50 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['arrow'] }}"/></svg>
                    </a>
                </div>
            </div>

            {{-- Jatuh tempo --}}
            @if ($dueSoon->isNotEmpty())
                <div class="panel border-amber-500/25 overflow-hidden">
                    <div class="flex items-center gap-2.5 px-5 py-3.5 border-b border-amber-500/15">
                        <h3 class="font-display text-[15px] font-semibold text-white">Jatuh Tempo dalam 7 Hari</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 divide-y divide-white/[0.05] md:divide-y-0 md:divide-x">
                        @foreach ($dueSoon as $loan)
                            @php
                                $daysLeft = \Carbon\Carbon::today()->diffInDays($loan->due_date, false);
                            @endphp
                            <div class="flex items-center gap-3 px-5 py-3.5">
                                @if ($loan->book?->cover_url)
                                    <img src="{{ $loan->book->cover_url }}" alt="{{ $loan->book->title }}" class="h-12 w-9 object-cover rounded-[6px] flex-shrink-0 border border-white/10">
                                @else
                                    <div class="h-12 w-9 rounded-[6px] bg-white/[0.04] border border-white/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['book'] }}"/></svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-body font-medium text-sm text-white truncate">{{ $loan->book->title }}</p>
                                    <p class="font-body text-xs text-white/30 mt-0.5">{{ $loan->due_date->format('d M Y') }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full font-body text-[11px] font-medium flex-shrink-0 {{ $daysLeft <= 2 ? 'text-rose-300 bg-rose-500/10' : 'text-amber-300 bg-amber-500/10' }}">
                                    H-{{ $daysLeft }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Pinjaman aktif --}}
            <div class="panel overflow-hidden">
                <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-white/[0.06]">
                    <div>
                        <h3 class="font-display text-[15px] font-semibold text-white">Pinjaman Aktif Saya</h3>
                        <p class="font-body text-xs text-white/30 mt-0.5">Buku yang sedang kamu pinjam</p>
                    </div>
                    <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-white/50 hover:text-white transition-colors shrink-0">
                        Lihat riwayat
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['arrow'] }}"/></svg>
                    </a>
                </div>

                @if ($activeLoans->isEmpty())
                    <div class="px-5 py-12 flex flex-col items-center justify-center text-center">
                        <p class="font-display font-semibold text-base text-white">Belum ada pinjaman aktif</p>
                        <p class="font-body text-sm text-white/40 mt-1">Temukan buku favoritmu dan mulai meminjam.</p>
                        <a href="{{ route('loans.borrow.create') }}" class="mt-5 inline-flex items-center gap-2 h-10 px-4 rounded-[8px] bg-primary text-white text-sm font-medium hover:bg-primary-hover transition-colors">Pinjam Buku</a>
                    </div>
                @else
                    <div class="divide-y divide-white/[0.06]">
                        @foreach ($activeLoans as $loan)
                            @php
                                $daysLeft = \Carbon\Carbon::today()->diffInDays($loan->due_date, false);
                            @endphp
                            <div class="flex items-center gap-4 px-5 py-4">
                                @if ($loan->book?->cover_url)
                                    <img src="{{ $loan->book->cover_url }}" alt="{{ $loan->book->title }}" class="h-14 w-10 object-cover rounded-[6px] flex-shrink-0 border border-white/10">
                                @else
                                    <div class="h-14 w-10 rounded-[6px] bg-white/[0.04] border border-white/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['book'] }}"/></svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-body font-medium text-sm text-white truncate">{{ $loan->book->title }}</p>
                                    <p class="font-body text-xs text-white/40 mt-0.5 truncate">{{ $loan->book->author }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-body text-xs text-white/40">Tempo: {{ $loan->due_date->format('d M Y') }}</p>
                                    <div class="mt-1">
                                        @if ($loan->isOverdue())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full font-body text-[11px] font-medium text-rose-300 bg-rose-500/10">
                                                {{ $loan->getDaysLate() }}h telat · Rp{{ number_format($loan->getPotentialDenda(), 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full font-body text-[11px] font-medium {{ $daysLeft <= 3 ? 'text-amber-300 bg-amber-500/10' : 'text-emerald-300 bg-emerald-500/10' }}">
                                                {{ $daysLeft == 0 ? 'Jatuh tempo hari ini' : 'H-' . $daysLeft }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Koleksi terbaru --}}
            <div>
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="font-display text-[15px] font-semibold text-white leading-tight">Koleksi Buku Terbaru</h3>
                        <p class="font-body text-xs text-white/30 mt-0.5">Temukan bacaan terbaru di perpustakaan</p>
                    </div>
                    <a href="{{ route('books.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-white/50 hover:text-white transition-colors shrink-0">
                        Jelajahi semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['arrow'] }}"/></svg>
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3">
                    @foreach ($recentBooks as $book)
                        <a href="{{ route('books.show', $book) }}" class="group block border border-white/[0.05] rounded-[10px] p-2.5 hover:border-white/[0.14] transition-colors duration-150">
                            <div class="relative mb-2.5">
                                @if ($book->cover_url)
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full aspect-[3/4] object-cover rounded-[6px] border border-white/10">
                                @else
                                    <div class="w-full aspect-[3/4] rounded-[6px] bg-white/[0.04] border border-white/10 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['book'] }}"/></svg>
                                    </div>
                                @endif
                                @if ($book->stock > 0)
                                    <span class="absolute top-1.5 right-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium text-emerald-300 bg-emerald-500/10 border border-emerald-500/20">Tersedia</span>
                                @else
                                    <span class="absolute top-1.5 right-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium text-rose-300 bg-rose-500/10 border border-rose-500/20">Habis</span>
                                @endif
                            </div>
                            <p class="font-body font-medium text-sm text-white truncate group-hover:text-primary transition-colors">{{ $book->title }}</p>
                            <p class="font-body text-xs text-white/40 truncate mt-0.5">{{ $book->author }}</p>
                            @if ($book->kategori)
                                <p class="font-body text-xs text-white/30 mt-0.5">{{ $book->kategori }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Riwayat terakhir --}}
            @if ($recentHistory->isNotEmpty())
                <div class="panel overflow-hidden">
                    <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-white/[0.06]">
                        <div>
                            <h3 class="font-display text-[15px] font-semibold text-white">Riwayat Terakhir</h3>
                            <p class="font-body text-xs text-white/30 mt-0.5">Buku yang baru kamu kembalikan</p>
                        </div>
                        <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-white/50 hover:text-white transition-colors shrink-0">
                            Semua
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['arrow'] }}"/></svg>
                        </a>
                    </div>
                    <div class="divide-y divide-white/[0.06]">
                        @foreach ($recentHistory as $loan)
                            <div class="flex items-center gap-3 px-5 py-3.5">
                                @if ($loan->book?->cover_url)
                                    <img src="{{ $loan->book->cover_url }}" alt="" class="h-10 w-8 object-cover rounded-[6px] border border-white/10 flex-shrink-0">
                                @else
                                    <div class="h-10 w-8 rounded-[6px] bg-white/[0.04] border border-white/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon['book'] }}"/></svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-body font-medium text-sm text-white truncate">{{ $loan->book->title ?? '-' }}</p>
                                    <p class="font-body text-xs text-white/30 mt-0.5">Dikembalikan {{ $loan->returned_at->format('d M Y') }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full font-body text-[11px] font-medium flex-shrink-0 {{ $loan->denda > 0 ? 'text-rose-300 bg-rose-500/10' : 'text-emerald-300 bg-emerald-500/10' }}">
                                    {{ $loan->denda > 0 ? 'Rp' . number_format($loan->denda, 0, ',', '.') : 'Tepat waktu' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>

    @if (Auth::user()->isStaff())
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dark = document.documentElement.classList.contains('dark');
                const palette = {
                    primary: '#35B8A5',
                    primaryDark: '#35B8A5',
                    emerald: '#35B8A5',
                    blue: '#5C9FE8',
                    rose: '#E06B73',
                    card: dark ? '#1D2124' : '#FFFFFF',
                    border: dark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(15, 23, 42, 0.08)',
                    grid: dark ? '#282D31' : '#E2E8F0',
                    muted: dark ? '#747C82' : '#64748B',
                    label: dark ? '#A5ADB3' : '#334155',
                    bar: dark ? '#35B8A5' : 'rgba(15, 118, 110, 0.55)',
                    barHover: dark ? '#2FA794' : '#115E59',
                };

                const tooltip = {
                    backgroundColor: dark ? '#1D2124' : '#FFFFFF',
                    borderColor: dark ? '#30363B' : '#E2E8F0',
                    borderWidth: 1,
                    titleColor: dark ? '#F1F3F4' : '#0F172A',
                    titleFont: { weight: '600' },
                    bodyColor: dark ? '#A5ADB3' : '#475569',
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 8,
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
                        // 1. Peminjaman per bulan — line chart (utama)
                        if (hasData(data.monthlyLoans.data)) {
                            new Chart(document.getElementById('chartMonthly'), {
                                type: 'line',
                                data: {
                                    labels: data.monthlyLoans.labels,
                                    datasets: [{
                                        label: 'Peminjaman',
                                        data: data.monthlyLoans.data,
                                        borderColor: dark ? palette.primaryDark : palette.primary,
                                        backgroundColor: dark ? palette.primaryDark : palette.primary,
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 0.3,
                                        pointRadius: 2.5,
                                        pointBorderWidth: 0,
                                        pointBackgroundColor: dark ? palette.primaryDark : palette.primary,
                                        pointHoverRadius: 4,
                                        pointHoverBorderWidth: 0,
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false }, tooltip: tooltip },
                                    scales: {
                                        x: { border: { display: false }, grid: { display: false }, ticks: { font: { size: 11 } } },
                                        y: { border: { display: false }, grid: { color: palette.grid }, ticks: { precision: 0, stepSize: 1, font: { size: 11 } }, beginAtZero: true },
                                    },
                                },
                            });
                        } else {
                            showEmpty('emptyMonthly', 'chartMonthly');
                        }

                        // 2. Buku terpopuler — horizontal bar (sekunder)
                        if (hasData(data.topBooks.data)) {
                            new Chart(document.getElementById('chartTopBooks'), {
                                type: 'bar',
                                data: {
                                    labels: data.topBooks.labels,
                                    datasets: [{
                                        label: 'Peminjaman',
                                        data: data.topBooks.data,
                                        backgroundColor: palette.bar,
                                        hoverBackgroundColor: palette.barHover,
                                        borderRadius: 4,
                                        borderSkipped: false,
                                        barThickness: 8,
                                        maxBarThickness: 8,
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    indexAxis: 'y',
                                    plugins: { legend: { display: false }, tooltip: tooltip },
                                    scales: {
                                        x: { border: { display: false }, grid: { display: false }, ticks: { precision: 0, stepSize: 1, font: { size: 10 } }, beginAtZero: true },
                                        y: { border: { display: false }, grid: { display: false }, ticks: { color: palette.label, font: { size: 11 } } },
                                    },
                                },
                            });
                        } else {
                            showEmpty('emptyTopBooks', 'chartTopBooks');
                        }

                        // 3. Status buku — donut compact + legend manual
                        const statusColors = [palette.emerald, palette.blue, palette.rose];
                        const statusTotal = data.bookStatus.data.reduce((a, b) => a + Number(b), 0);
                        const legend = document.getElementById('statusLegend');
                        legend.innerHTML = '';
                        if (statusTotal > 0) {
                            new Chart(document.getElementById('chartStatus'), {
                                type: 'doughnut',
                                data: {
                                    labels: data.bookStatus.labels,
                                    datasets: [{
                                        data: data.bookStatus.data,
                                        backgroundColor: statusColors,
                                        borderColor: palette.card,
                                        borderWidth: 2,
                                        hoverOffset: 3,
                                    }],
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '74%',
                                    plugins: { legend: { display: false }, tooltip: tooltip },
                                },
                            });
                            data.bookStatus.labels.forEach((label, i) => {
                                const item = document.createElement('span');
                                item.className = 'inline-flex items-center gap-1.5 font-body text-[11px] text-white/55';
                                const dot = document.createElement('span');
                                dot.className = 'w-2 h-2 rounded-full flex-shrink-0';
                                dot.style.backgroundColor = statusColors[i];
                                item.appendChild(dot);
                                item.appendChild(document.createTextNode(label));
                                legend.appendChild(item);
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
