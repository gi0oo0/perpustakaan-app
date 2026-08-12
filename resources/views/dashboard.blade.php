<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-gradient">
                    Dashboard
                </h2>
                <p class="font-body text-sm text-white/45 mt-1">
                    @if (Auth::user()->isStaff())
                        Selamat datang kembali, {{ Auth::user()->name }} 👋
                    @else
                        Halo {{ Auth::user()->name }} — pantau pinjamanmu di sini 👋
                    @endif
                </p>
            </div>
            @if (Auth::user()->isStaff())
                <span class="glass-badge-blue">Live stats</span>
            @else
                <span class="glass-badge-violet">Dashboard Anggota</span>
            @endif
        </div>
    </x-slot>

    <div class="space-y-8" x-data="reveal">
        @if (Auth::user()->isStaff())
            {{-- ============ STAFF / ADMIN ============ --}}
            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach ([
                    ['label' => 'Total Buku', 'value' => $totalBooks, 'icon' => '📚', 'tint' => 'text-violet-300 bg-violet-400/10 border-violet-400/20', 'glow' => 'shadow-glow'],
                    ['label' => 'Dipinjam', 'value' => $activeLoans, 'icon' => '📦', 'tint' => 'text-sky-300 bg-sky-400/10 border-sky-400/20', 'glow' => 'shadow-glow-cyan'],
                    ['label' => 'Terlambat', 'value' => $overdueLoans, 'icon' => '⏰', 'tint' => 'text-rose-300 bg-rose-400/10 border-rose-400/20', 'glow' => 'shadow-glow-rose'],
                    ['label' => 'Dikembalikan', 'value' => $returnedLoans, 'icon' => '✅', 'tint' => 'text-emerald-300 bg-emerald-400/10 border-emerald-400/20', 'glow' => ''],
                ] as $stat)
                    <div class="glass glass-hover p-5" style="animation-delay: {{ $loop->index * 80 }}ms;">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-body text-xs font-medium uppercase tracking-wider text-white/40">{{ $stat['label'] }}</p>
                                <p x-data="countUp"
                                   data-count="{{ $stat['value'] }}"
                                   class="font-display text-3xl font-bold mt-2 tabular-nums"
                                   x-text="displayed.toLocaleString('id-ID')"></p>
                            </div>
                            <span class="w-11 h-11 rounded-glass-sm flex items-center justify-center text-xl border {{ $stat['tint'] }} {{ $stat['glow'] }}">{{ $stat['icon'] }}</span>
                        </div>
                        <div class="mt-3 h-1 rounded-full bg-white/[0.06] overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-accent opacity-80" style="width: {{ min(100, max(8, ($stat['value'] / max(1, $totalBooks)) * 100)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Quick Actions --}}
            <div>
                <h3 class="font-display text-lg font-semibold text-white mb-4">Akses Cepat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                    <a href="{{ route('books.index') }}" class="glass glass-hover p-6 group">
                        <div class="w-12 h-12 rounded-glass-sm bg-violet-400/10 border border-violet-400/20 flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform duration-300">📖</div>
                        <div class="font-display font-semibold text-white text-sm">Katalog Buku</div>
                        <p class="font-body text-xs text-white/40 mt-1">Lihat & kelola koleksi buku</p>
                    </a>
                    <a href="{{ route('loans.borrow.create') }}" class="glass glass-hover p-6 group">
                        <div class="w-12 h-12 rounded-glass-sm bg-sky-400/10 border border-sky-400/20 flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform duration-300">📦</div>
                        <div class="font-display font-semibold text-white text-sm">Pinjam Buku</div>
                        <p class="font-body text-xs text-white/40 mt-1">Scan & pinjam buku baru</p>
                    </a>
                    <a href="{{ route('loans.return.create') }}" class="glass glass-hover p-6 group">
                        <div class="w-12 h-12 rounded-glass-sm bg-rose-400/10 border border-rose-400/20 flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform duration-300">🔄</div>
                        <div class="font-display font-semibold text-white text-sm">Kembalikan Buku</div>
                        <p class="font-body text-xs text-white/40 mt-1">Scan & proses pengembalian</p>
                    </a>
                </div>
            </div>

            {{-- Charts Row 1 --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display text-lg font-semibold text-white">Statistik</h3>
                    <span class="glass-badge-gray hidden sm:inline-flex">6 bulan terakhir</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">
                    <div class="glass p-6">
                        <h4 class="font-display font-semibold text-sm text-white mb-1">📖 Buku Terpopuler</h4>
                        <p class="font-body text-xs text-white/40 mb-4">Paling sering dipinjam</p>
                        <div class="relative" style="height: 240px;">
                            <canvas id="chartTopBooks"></canvas>
                        </div>
                    </div>

                    <div class="glass p-6">
                        <h4 class="font-display font-semibold text-sm text-white mb-1">📈 Peminjaman / Bulan</h4>
                        <p class="font-body text-xs text-white/40 mb-4">Tren aktivitas perpustakaan</p>
                        <div class="relative" style="height: 240px;">
                            <canvas id="chartMonthly"></canvas>
                        </div>
                    </div>

                    <div class="glass p-6">
                        <h4 class="font-display font-semibold text-sm text-white mb-1">📊 Status Buku</h4>
                        <p class="font-body text-xs text-white/40 mb-4">Ketersediaan koleksi</p>
                        <div class="relative" style="height: 240px;">
                            <canvas id="chartStatus"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row 2 --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">
                <div class="glass p-6">
                    <h4 class="font-display font-semibold text-sm text-white mb-1">🏷️ Peminjaman per Kategori</h4>
                    <p class="font-body text-xs text-white/40 mb-4">Distribusi koleksi yang dipinjam</p>
                    <div class="relative" style="height: 240px;">
                        <canvas id="chartKategori"></canvas>
                    </div>
                </div>

                <div class="glass p-6">
                    <h4 class="font-display font-semibold text-sm text-white mb-1">⚡ Aktivitas 7 Hari</h4>
                    <p class="font-body text-xs text-white/40 mb-4">Peminjaman harian terakhir</p>
                    <div class="relative" style="height: 240px;">
                        <canvas id="chartWeekly"></canvas>
                    </div>
                </div>

                <div class="glass p-6">
                    <h4 class="font-display font-semibold text-sm text-white mb-4">🕐 Aktivitas Terakhir</h4>
                    <div class="space-y-3">
                        @forelse ($recentActivity as $loan)
                            <div class="flex items-center gap-3">
                                @if ($loan->book->cover_url)
                                    <img src="{{ $loan->book->cover_url }}" alt="" class="h-10 w-8 object-cover rounded-md border border-white/10 flex-shrink-0">
                                @else
                                    <div class="h-10 w-8 rounded-md bg-white/[0.06] border border-white/10 flex items-center justify-center text-sm flex-shrink-0">📖</div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-display font-medium text-sm text-white truncate">{{ $loan->book->title ?? '-' }}</p>
                                    <p class="font-body text-xs text-white/40 truncate">{{ $loan->user->name ?? '-' }} · {{ $loan->loan_date->format('d/m/Y') }}</p>
                                </div>
                                <span class="glass-badge flex-shrink-0 {{ $loan->isReturned() ? 'glass-badge-green' : ($loan->isOverdue() ? 'glass-badge-red' : 'glass-badge-yellow') }}">
                                    {{ $loan->isReturned() ? 'Kembali' : ($loan->isOverdue() ? 'Telat' : 'Dipinjam') }}
                                </span>
                            </div>
                        @empty
                            <p class="font-body text-sm text-white/40 py-6 text-center">Belum ada aktivitas</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @else
            {{-- ============ MEMBER ============ --}}
            {{-- Personal Summary --}}
            @php
                $memberStats = [
                    ['label' => 'Sedang Dipinjam', 'value' => $activeCount, 'icon' => '📦', 'money' => false, 'tint' => 'text-sky-300 bg-sky-400/10 border-sky-400/20', 'glow' => 'shadow-glow-cyan'],
                    ['label' => 'Terlambat', 'value' => $overdueCount, 'icon' => '⏰', 'money' => false, 'tint' => 'text-rose-300 bg-rose-400/10 border-rose-400/20', 'glow' => 'shadow-glow-rose'],
                    ['label' => 'Denda Belum Bayar', 'value' => $totalDenda, 'icon' => '💸', 'money' => true, 'tint' => 'text-amber-300 bg-amber-400/10 border-amber-400/20', 'glow' => 'shadow-glow'],
                    ['label' => 'Dipinjam Bulan Ini', 'value' => $totalThisMonth, 'icon' => '📚', 'money' => false, 'tint' => 'text-violet-300 bg-violet-400/10 border-violet-400/20', 'glow' => 'shadow-glow'],
                ];
            @endphp
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach ($memberStats as $stat)
                    <div class="glass glass-hover p-5" style="animation-delay: {{ $loop->index * 80 }}ms;">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-body text-xs font-medium uppercase tracking-wider text-white/40">{{ $stat['label'] }}</p>
                                <p x-data="countUp"
                                   data-count="{{ $stat['value'] }}"
                                   class="font-display text-3xl font-bold mt-2 tabular-nums"
                                   x-text="'{{ $stat['money'] ? 'Rp ' : '' }}' + displayed.toLocaleString('id-ID')"></p>
                            </div>
                            <span class="w-11 h-11 rounded-glass-sm flex items-center justify-center text-xl border {{ $stat['tint'] }} {{ $stat['glow'] }}">{{ $stat['icon'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Recent Books --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display text-lg font-semibold text-white">Koleksi Buku Terbaru</h3>
                    <a href="{{ route('books.index') }}" class="glass-link text-sm font-medium">Jelajahi semua →</a>
                </div>
                <div class="grid grid-cols-[repeat(auto-fit,minmax(min(150px,100%),1fr))] gap-4">
                    @foreach ($recentBooks as $book)
                        <a href="{{ route('books.show', $book) }}" class="glass glass-hover p-4 group">
                            <div class="relative mb-3">
                                @if ($book->cover_url)
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full aspect-[3/4] object-cover rounded-lg border border-white/10">
                                @else
                                    <div class="w-full aspect-[3/4] rounded-lg bg-white/[0.06] border border-white/10 flex items-center justify-center text-3xl">📖</div>
                                @endif
                                @if ($book->stock > 0)
                                    <span class="absolute top-2 right-2 glass-badge glass-badge-green">Tersedia</span>
                                @else
                                    <span class="absolute top-2 right-2 glass-badge glass-badge-red">Habis</span>
                                @endif
                            </div>
                            <p class="font-display font-semibold text-sm text-white truncate group-hover:text-primary-300 transition-colors">{{ $book->title }}</p>
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
                <div class="glass p-5 border-amber-400/20">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-lg">⏳</span>
                        <h3 class="font-display text-lg font-semibold text-white">Jatuh Tempo dalam 7 Hari</h3>
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
                                    <div class="h-12 w-9 rounded-lg bg-white/[0.06] border border-white/10 flex items-center justify-center text-lg flex-shrink-0">📖</div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-display font-medium text-sm text-white truncate">{{ $loan->book->title }}</p>
                                    <p class="font-body text-xs text-white/40">{{ $loan->due_date->format('d M Y') }}</p>
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
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display text-lg font-semibold text-white">Pinjaman Aktif Saya</h3>
                    <a href="{{ route('loans.index') }}" class="glass-link text-sm font-medium">Lihat riwayat →</a>
                </div>

                @if ($activeLoans->isEmpty())
                    <div class="glass p-12 flex flex-col items-center justify-center text-center">
                        <div class="text-5xl mb-4">📭</div>
                        <p class="font-display font-semibold text-lg text-white">Belum ada pinjaman aktif</p>
                        <p class="font-body text-sm text-white/40 mt-1">Temukan buku favoritmu dan mulai meminjam.</p>
                        <a href="{{ route('loans.borrow.create') }}" class="glass-btn-primary mt-6">Pinjam Buku Sekarang</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
                        @foreach ($activeLoans as $loan)
                            @php
                                $daysLeft = \Carbon\Carbon::today()->diffInDays($loan->due_date, false);
                            @endphp
                            <div class="glass glass-hover p-5 {{ $loan->isOverdue() ? 'border-rose-400/25' : '' }}">
                                <div class="flex items-start gap-4">
                                    @if ($loan->book->cover_url)
                                        <img src="{{ $loan->book->cover_url }}" alt="{{ $loan->book->title }}" class="h-16 w-12 object-cover rounded-lg flex-shrink-0 border border-white/10">
                                    @else
                                        <div class="h-16 w-12 rounded-lg bg-white/[0.06] border border-white/10 flex items-center justify-center text-xl flex-shrink-0">📖</div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="font-display font-semibold text-sm text-white truncate">{{ $loan->book->title }}</p>
                                        <p class="font-body text-xs text-white/40 mt-0.5 truncate">{{ $loan->book->author }}</p>
                                        <span class="glass-badge mt-2 {{ $loan->isOverdue() ? 'glass-badge-red' : ($daysLeft <= 3 ? 'glass-badge-yellow' : 'glass-badge-green') }}">
                                            {{ $loan->isOverdue() ? $loan->getDaysLate() . 'h telat' : ($daysLeft == 0 ? 'Jatuh tempo hari ini' : 'H-' . $daysLeft) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between">
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
                <div class="glass p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-display text-lg font-semibold text-white">Riwayat Terakhir</h3>
                        <a href="{{ route('loans.index') }}" class="glass-link text-sm font-medium">Semua →</a>
                    </div>
                    <div class="space-y-3">
                        @foreach ($recentHistory as $loan)
                            <div class="flex items-center gap-3">
                                @if ($loan->book->cover_url)
                                    <img src="{{ $loan->book->cover_url }}" alt="" class="h-10 w-8 object-cover rounded-md border border-white/10 flex-shrink-0">
                                @else
                                    <div class="h-10 w-8 rounded-md bg-white/[0.06] border border-white/10 flex items-center justify-center text-sm flex-shrink-0">📖</div>
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
                <h3 class="font-display text-lg font-semibold text-white mb-4">Akses Cepat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                    <a href="{{ route('books.index') }}" class="glass glass-hover p-6 group">
                        <div class="w-12 h-12 rounded-glass-sm bg-violet-400/10 border border-violet-400/20 flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform duration-300">📖</div>
                        <div class="font-display font-semibold text-white text-sm">Katalog Buku</div>
                        <p class="font-body text-xs text-white/40 mt-1">Jelajahi koleksi perpustakaan</p>
                    </a>
                    <a href="{{ route('loans.borrow.create') }}" class="glass glass-hover p-6 group">
                        <div class="w-12 h-12 rounded-glass-sm bg-sky-400/10 border border-sky-400/20 flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform duration-300">📦</div>
                        <div class="font-display font-semibold text-white text-sm">Pinjam Buku</div>
                        <p class="font-body text-xs text-white/40 mt-1">Scan & pinjam buku baru</p>
                    </a>
                    <a href="{{ route('loans.index') }}" class="glass glass-hover p-6 group">
                        <div class="w-12 h-12 rounded-glass-sm bg-emerald-400/10 border border-emerald-400/20 flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform duration-300">🔄</div>
                        <div class="font-display font-semibold text-white text-sm">Riwayat Peminjaman</div>
                        <p class="font-body text-xs text-white/40 mt-1">Pantau status & denda pinjaman</p>
                    </a>
                </div>
            </div>
        @endif
    </div>

    @if (Auth::user()->isStaff())
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const glassColors = {
                    primary: '#6d5cff',
                    violet: '#a855f7',
                    cyan: '#22d3ee',
                    rose: '#fb5e63',
                    warning: '#fbbf24',
                    border: 'rgba(255,255,255,0.08)',
                    text: '#ffffff',
                    muted: 'rgba(255,255,255,0.45)',
                };

                Chart.defaults.font.family = 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
                Chart.defaults.color = glassColors.muted;
                Chart.defaults.borderColor = glassColors.border;

                fetch('{{ route("dashboard.stats") }}')
                    .then(res => res.json())
                    .then(data => {
                        new Chart(document.getElementById('chartTopBooks'), {
                            type: 'bar',
                            data: {
                                labels: data.topBooks.labels,
                                datasets: [{
                                    label: 'Peminjaman',
                                    data: data.topBooks.data,
                                    backgroundColor: 'rgba(109, 92, 255, 0.75)',
                                    hoverBackgroundColor: 'rgba(168, 85, 247, 0.9)',
                                    borderRadius: 8,
                                    borderSkipped: false,
                                    maxBarThickness: 42,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(11,18,32,0.95)', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1, titleColor: '#fff', bodyColor: 'rgba(255,255,255,0.7)', padding: 12, cornerRadius: 12 } },
                                scales: {
                                    x: { border: { display: false }, grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45 } },
                                    y: { border: { display: false }, grid: { color: glassColors.border, lineWidth: 1 }, ticks: { stepSize: 1, precision: 0, font: { size: 10 } }, beginAtZero: true },
                                },
                            },
                        });

                        new Chart(document.getElementById('chartMonthly'), {
                            type: 'line',
                            data: {
                                labels: data.monthlyLoans.labels,
                                datasets: [{
                                    label: 'Peminjaman',
                                    data: data.monthlyLoans.data,
                                    borderColor: '#6d5cff',
                                    backgroundColor: (ctx) => {
                                        const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 240);
                                        g.addColorStop(0, 'rgba(109, 92, 255, 0.35)');
                                        g.addColorStop(1, 'rgba(109, 92, 255, 0)');
                                        return g;
                                    },
                                    borderWidth: 2.5,
                                    fill: true,
                                    tension: 0.45,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#0b1220',
                                    pointBorderColor: '#a855f7',
                                    pointBorderWidth: 2,
                                    hoverPointRadius: 6,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(11,18,32,0.95)', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1, titleColor: '#fff', bodyColor: 'rgba(255,255,255,0.7)', padding: 12, cornerRadius: 12 } },
                                scales: {
                                    x: { border: { display: false }, grid: { display: false }, ticks: { font: { size: 10 } } },
                                    y: { border: { display: false }, grid: { color: glassColors.border, lineWidth: 1 }, ticks: { stepSize: 1, precision: 0, font: { size: 10 } }, beginAtZero: true },
                                },
                            },
                        });

                        new Chart(document.getElementById('chartStatus'), {
                            type: 'doughnut',
                            data: {
                                labels: data.bookStatus.labels,
                                datasets: [{
                                    data: data.bookStatus.data,
                                    backgroundColor: ['#34d399', '#6d5cff', '#fb5e63'],
                                    borderColor: '#0b1220',
                                    borderWidth: 4,
                                    hoverOffset: 10,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '68%',
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { color: glassColors.muted, font: { size: 11, family: 'Inter' }, padding: 16, usePointStyle: true, pointStyleWidth: 8 },
                                    },
                                    tooltip: { backgroundColor: 'rgba(11,18,32,0.95)', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1, titleColor: '#fff', bodyColor: 'rgba(255,255,255,0.7)', padding: 12, cornerRadius: 12 },
                                },
                            },
                        });

                        new Chart(document.getElementById('chartKategori'), {
                            type: 'doughnut',
                            data: {
                                labels: data.kategoriLoans.labels,
                                datasets: [{
                                    data: data.kategoriLoans.data,
                                    backgroundColor: ['#6d5cff', '#a855f7', '#22d3ee', '#fb5e63', '#fbbf24', '#34d399', '#60a5fa', '#f472b6', '#94a3b8', '#f97316'],
                                    borderColor: '#0b1220',
                                    borderWidth: 4,
                                    hoverOffset: 10,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '68%',
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { color: glassColors.muted, font: { size: 10, family: 'Inter' }, padding: 12, usePointStyle: true, pointStyleWidth: 8 },
                                    },
                                    tooltip: { backgroundColor: 'rgba(11,18,32,0.95)', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1, titleColor: '#fff', bodyColor: 'rgba(255,255,255,0.7)', padding: 12, cornerRadius: 12 },
                                },
                            },
                        });

                        new Chart(document.getElementById('chartWeekly'), {
                            type: 'bar',
                            data: {
                                labels: data.weeklyActivity.labels,
                                datasets: [{
                                    label: 'Peminjaman',
                                    data: data.weeklyActivity.data,
                                    backgroundColor: 'rgba(34, 211, 238, 0.55)',
                                    hoverBackgroundColor: 'rgba(34, 211, 238, 0.85)',
                                    borderRadius: 8,
                                    borderSkipped: false,
                                    maxBarThickness: 42,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(11,18,32,0.95)', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1, titleColor: '#fff', bodyColor: 'rgba(255,255,255,0.7)', padding: 12, cornerRadius: 12 } },
                                scales: {
                                    x: { border: { display: false }, grid: { display: false }, ticks: { font: { size: 10 } } },
                                    y: { border: { display: false }, grid: { color: glassColors.border, lineWidth: 1 }, ticks: { stepSize: 1, precision: 0, font: { size: 10 } }, beginAtZero: true },
                                },
                            },
                        });
                    })
                    .catch(err => {
                        console.error('Gagal memuat data statistik:', err);
                        window.toast('Gagal memuat data statistik', 'error');
                    });
            });
        </script>
    @endif
</x-app-layout>
