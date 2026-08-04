<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-2xl text-text leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-apple-lg space-y-8">

            {{-- Summary Banner --}}
            <div class="bg-surface-light rounded-apple-lg p-8 relative overflow-hidden">
                <div class="absolute top-4 right-8 w-16 h-16 rounded-full opacity-10 bg-text"></div>
                <div class="absolute bottom-4 right-24 w-0 h-0 border-l-[20px] border-l-transparent border-r-[20px] border-r-transparent border-b-[30px] border-b-primary opacity-10"></div>
                <h3 class="font-display font-semibold text-xl text-text mb-2">Selamat Datang! 👋</h3>
                <p class="font-body text-text-tertiary">Kelola perpustakaan Anda dengan mudah dan menyenangkan.</p>

                <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white rounded-apple-lg p-4 text-center shadow-sm">
                        <div class="font-display font-semibold text-3xl text-primary">{{ $totalBooks }}</div>
                        <div class="font-body text-xs text-text-tertiary mt-1">Total Buku</div>
                    </div>
                    <div class="bg-white rounded-apple-lg p-4 text-center shadow-sm">
                        <div class="font-display font-semibold text-3xl text-text">{{ $activeLoans }}</div>
                        <div class="font-body text-xs text-text-tertiary mt-1">Dipinjam</div>
                    </div>
                    <div class="bg-white rounded-apple-lg p-4 text-center shadow-sm">
                        <div class="font-display font-semibold text-3xl text-danger">{{ $overdueLoans }}</div>
                        <div class="font-body text-xs text-text-tertiary mt-1">Terlambat</div>
                    </div>
                    <div class="bg-white rounded-apple-lg p-4 text-center shadow-sm">
                        <div class="font-display font-semibold text-3xl text-primary">{{ $returnedLoans }}</div>
                        <div class="font-body text-xs text-text-tertiary mt-1">Dikembalikan</div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div>
                <h3 class="font-display font-semibold text-lg text-text mb-4">Akses Cepat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="{{ route('books.index') }}" class="bg-white rounded-apple-lg p-6 text-center group hover:shadow-md transition-all duration-200">
                        <div class="w-14 h-14 bg-primary mx-auto flex items-center justify-center text-2xl mb-3 group-hover:scale-105 transition-transform duration-200 rounded-full">📖</div>
                        <div class="font-display font-semibold text-text text-sm">Daftar Buku</div>
                        <p class="font-body text-xs text-text-tertiary mt-1">Lihat & kelola koleksi buku</p>
                    </a>
                    <a href="{{ route('loans.borrow.create') }}" class="bg-white rounded-apple-lg p-6 text-center group hover:shadow-md transition-all duration-200">
                        <div class="w-14 h-14 bg-surface-light mx-auto flex items-center justify-center text-2xl mb-3 group-hover:scale-105 transition-transform duration-200 rounded-full">📦</div>
                        <div class="font-display font-semibold text-text text-sm">Pinjam Buku</div>
                        <p class="font-body text-xs text-text-tertiary mt-1">Scan & pinjam buku baru</p>
                    </a>
                    @if (Auth::user()->isStaff())
                        <a href="{{ route('loans.return.create') }}" class="bg-white rounded-apple-lg p-6 text-center group hover:shadow-md transition-all duration-200">
                            <div class="w-14 h-14 bg-danger mx-auto flex items-center justify-center text-2xl mb-3 group-hover:scale-105 transition-transform duration-200 rounded-full">🔄</div>
                            <div class="font-display font-semibold text-text text-sm">Kembalikan Buku</div>
                            <p class="font-body text-xs text-text-tertiary mt-1">Scan & kembalikan buku</p>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Charts Section --}}
            <div>
                <h3 class="font-display font-semibold text-lg text-text mb-4">Statistik</h3>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Bar Chart: Buku Terpopuler --}}
                    <div class="bg-white rounded-apple-lg p-6">
                        <h4 class="font-display font-semibold text-sm text-text mb-4">📖 Buku Terpopuler</h4>
                        <div class="relative" style="height: 250px;">
                            <canvas id="chartTopBooks"></canvas>
                        </div>
                    </div>

                    {{-- Line Chart: Peminjaman Per Bulan --}}
                    <div class="bg-white rounded-apple-lg p-6">
                        <h4 class="font-display font-semibold text-sm text-text mb-4">📈 Peminjaman / Bulan</h4>
                        <div class="relative" style="height: 250px;">
                            <canvas id="chartMonthly"></canvas>
                        </div>
                    </div>

                    {{-- Doughnut Chart: Status Buku --}}
                    <div class="bg-white rounded-apple-lg p-6">
                        <h4 class="font-display font-semibold text-sm text-text mb-4">📊 Status Buku</h4>
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
            const appleColors = {
                primary: '#0071E3',
                danger: '#FF3B30',
                warning: '#FF9500',
                border: '#EDEDF2',
                text: '#1D1D1F',
                muted: '#86868B',
            };

            Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Helvetica Neue", Helvetica, Arial, sans-serif';
            Chart.defaults.color = appleColors.muted;

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
                                backgroundColor: appleColors.primary,
                                borderColor: 'transparent',
                                borderWidth: 0,
                                borderRadius: 6,
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
                                    border: { display: false },
                                    grid: { display: false },
                                    ticks: {
                                        font: { size: 10, weight: '500' },
                                        maxRotation: 45,
                                    },
                                },
                                y: {
                                    border: { display: false },
                                    grid: { color: appleColors.border, lineWidth: 1 },
                                    ticks: { stepSize: 1, font: { weight: '500' } },
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
                                borderColor: appleColors.primary,
                                backgroundColor: 'rgba(0, 113, 227, 0.08)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#FFFFFF',
                                pointBorderColor: appleColors.primary,
                                pointBorderWidth: 2,
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
                                    border: { display: false },
                                    grid: { display: false },
                                    ticks: { font: { size: 10, weight: '500' } },
                                },
                                y: {
                                    border: { display: false },
                                    grid: { color: appleColors.border, lineWidth: 1 },
                                    ticks: { stepSize: 1, font: { weight: '500' } },
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
                                backgroundColor: [appleColors.primary, appleColors.warning, appleColors.danger],
                                borderColor: '#FFFFFF',
                                borderWidth: 3,
                                hoverOffset: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        font: { size: 11, weight: '500', family: '-apple-system, BlinkMacSystemFont, "SF Pro Display", sans-serif' },
                                        padding: 16,
                                        usePointStyle: true,
                                        pointStyleWidth: 8,
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
