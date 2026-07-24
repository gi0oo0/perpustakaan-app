<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Summary Banner --}}
            <div class="bg-lemon border-3 border-border shadow-neo p-8 relative overflow-hidden">
                <div class="absolute top-4 right-8 w-16 h-16 border-4 border-border rounded-full opacity-20"></div>
                <div class="absolute bottom-4 right-24 w-0 h-0 border-l-[20px] border-l-transparent border-r-[20px] border-r-transparent border-b-[30px] border-b-primary opacity-20"></div>
                <h3 class="font-heading font-bold text-xl text-border mb-2">Selamat Datang! 👋</h3>
                <p class="font-body text-muted">Kelola perpustakaan Anda dengan mudah dan menyenangkan.</p>

                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white border-3 border-border shadow-neo-sm p-4 text-center">
                        <div class="font-heading font-bold text-3xl text-primary">{{ $totalBooks }}</div>
                        <div class="font-body text-xs text-muted mt-1 uppercase tracking-wide">Total Buku</div>
                    </div>
                    <div class="bg-white border-3 border-border shadow-neo-sm p-4 text-center">
                        <div class="font-heading font-bold text-3xl text-border">{{ $activeLoans }}</div>
                        <div class="font-body text-xs text-muted mt-1 uppercase tracking-wide">Dipinjam</div>
                    </div>
                    <div class="bg-white border-3 border-border shadow-neo-sm p-4 text-center">
                        <div class="font-heading font-bold text-3xl text-coral">{{ $overdueLoans }}</div>
                        <div class="font-body text-xs text-muted mt-1 uppercase tracking-wide">Terlambat</div>
                    </div>
                    <div class="bg-white border-3 border-border shadow-neo-sm p-4 text-center">
                        <div class="font-heading font-bold text-3xl text-primary">{{ $returnedLoans }}</div>
                        <div class="font-body text-xs text-muted mt-1 uppercase tracking-wide">Dikembalikan</div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div>
                <h3 class="font-heading font-semibold text-lg text-border mb-4 uppercase tracking-wide">Akses Cepat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="{{ route('books.index') }}" class="book-card neo-card text-center group">
                        <div class="w-14 h-14 bg-primary border-3 border-border shadow-neo-sm mx-auto flex items-center justify-center text-2xl mb-3 group-hover:shadow-neo-hover transition-all duration-150">📖</div>
                        <div class="font-heading font-semibold text-border uppercase tracking-wide text-sm">Daftar Buku</div>
                        <p class="font-body text-xs text-muted mt-1">Lihat & kelola koleksi buku</p>
                    </a>
                    <a href="{{ route('loans.borrow.create') }}" class="book-card neo-card text-center group">
                        <div class="w-14 h-14 bg-lemon border-3 border-border shadow-neo-sm mx-auto flex items-center justify-center text-2xl mb-3 group-hover:shadow-neo-hover transition-all duration-150">📦</div>
                        <div class="font-heading font-semibold text-border uppercase tracking-wide text-sm">Pinjam Buku</div>
                        <p class="font-body text-xs text-muted mt-1">Scan & pinjam buku baru</p>
                    </a>
                    <a href="{{ route('loans.return.create') }}" class="book-card neo-card text-center group">
                        <div class="w-14 h-14 bg-coral border-3 border-border shadow-neo-sm mx-auto flex items-center justify-center text-2xl mb-3 group-hover:shadow-neo-hover transition-all duration-150">🔄</div>
                        <div class="font-heading font-semibold text-border uppercase tracking-wide text-sm">Kembalikan Buku</div>
                        <p class="font-body text-xs text-muted mt-1">Scan & kembalikan buku</p>
                    </a>
                </div>
            </div>

            {{-- Charts Section --}}
            <div>
                <h3 class="font-heading font-semibold text-lg text-border mb-4 uppercase tracking-wide">Statistik</h3>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Bar Chart: Buku Terpopuler --}}
                    <div class="neo-card">
                        <h4 class="font-heading font-bold text-sm text-border uppercase tracking-wide mb-4">📖 Buku Terpopuler</h4>
                        <div class="relative" style="height: 250px;">
                            <canvas id="chartTopBooks"></canvas>
                        </div>
                    </div>

                    {{-- Line Chart: Peminjaman Per Bulan --}}
                    <div class="neo-card">
                        <h4 class="font-heading font-bold text-sm text-border uppercase tracking-wide mb-4">📈 Peminjaman / Bulan</h4>
                        <div class="relative" style="height: 250px;">
                            <canvas id="chartMonthly"></canvas>
                        </div>
                    </div>

                    {{-- Doughnut Chart: Status Buku --}}
                    <div class="neo-card">
                        <h4 class="font-heading font-bold text-sm text-border uppercase tracking-wide mb-4">📊 Status Buku</h4>
                        <div class="relative" style="height: 250px;">
                            <canvas id="chartStatus"></canvas>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const neoColors = {
                primary: '#0D9488',
                coral: '#FF6B6B',
                lemon: '#FDE047',
                border: '#111827',
                muted: '#6B7280',
            };

            const chartDefaults = {
                font: { family: 'Space Grotesk, sans-serif' },
                color: neoColors.border,
            };

            Chart.defaults.font.family = 'Space Grotesk, sans-serif';
            Chart.defaults.color = neoColors.border;

            // Fetch stats from API
            fetch('{{ route("dashboard.stats") }}')
                .then(res => res.json())
                .then(data => {
                    // Bar Chart - Buku Terpopuler
                    new Chart(document.getElementById('chartTopBooks'), {
                        type: 'bar',
                        data: {
                            labels: data.topBooks.labels,
                            datasets: [{
                                label: 'Peminjaman',
                                data: data.topBooks.data,
                                backgroundColor: neoColors.primary,
                                borderColor: neoColors.border,
                                borderWidth: 3,
                                borderRadius: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                            },
                            scales: {
                                x: {
                                    border: { color: neoColors.border, width: 3 },
                                    ticks: {
                                        font: { size: 10, weight: '600' },
                                        maxRotation: 45,
                                    },
                                },
                                y: {
                                    border: { color: neoColors.border, width: 3 },
                                    ticks: { stepSize: 1, font: { weight: '600' } },
                                },
                            },
                        },
                    });

                    // Line Chart - Peminjaman Per Bulan
                    new Chart(document.getElementById('chartMonthly'), {
                        type: 'line',
                        data: {
                            labels: data.monthlyLoans.labels,
                            datasets: [{
                                label: 'Peminjaman',
                                data: data.monthlyLoans.data,
                                borderColor: neoColors.primary,
                                backgroundColor: 'rgba(13, 148, 136, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0,
                                pointRadius: 6,
                                pointBackgroundColor: neoColors.lemon,
                                pointBorderColor: neoColors.border,
                                pointBorderWidth: 3,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                            },
                            scales: {
                                x: {
                                    border: { color: neoColors.border, width: 3 },
                                    ticks: { font: { size: 10, weight: '600' } },
                                },
                                y: {
                                    border: { color: neoColors.border, width: 3 },
                                    ticks: { stepSize: 1, font: { weight: '600' } },
                                    beginAtZero: true,
                                },
                            },
                        },
                    });

                    // Doughnut Chart - Status Buku
                    new Chart(document.getElementById('chartStatus'), {
                        type: 'doughnut',
                        data: {
                            labels: data.bookStatus.labels,
                            datasets: [{
                                data: data.bookStatus.data,
                                backgroundColor: [neoColors.primary, neoColors.lemon, neoColors.coral],
                                borderColor: neoColors.border,
                                borderWidth: 4,
                                hoverOffset: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '55%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        font: { size: 11, weight: '600' },
                                        padding: 12,
                                        usePointStyle: true,
                                        pointStyleWidth: 14,
                                    },
                                },
                            },
                        },
                    });
                })
                .catch(err => {
                    console.error('Gagal memuat data statistik:', err);
                });
        });
    </script>
</x-app-layout>
