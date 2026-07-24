<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            📚 Riwayat Peminjaman
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-primary text-white border-3 border-border shadow-neo px-6 py-4 font-heading font-semibold uppercase tracking-wide text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Summary --}}
            <div class="bg-lemon border-3 border-border shadow-neo p-6 mb-6 relative overflow-hidden">
                <div class="absolute top-3 right-6 w-12 h-12 border-4 border-border rounded-full opacity-20"></div>
                <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-border uppercase tracking-wide">Riwayat Peminjaman</h3>
                        <p class="font-body text-sm text-muted mt-1">Total bulan ini: <strong class="text-border">{{ $totalThisMonth }}</strong> · Masih dipinjam: <strong class="text-border">{{ $activeCount }}</strong></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('loans.borrow.create') }}">
                            <button type="button" class="neo-btn-primary text-xs">📦 Pinjam</button>
                        </a>
                        <a href="{{ route('loans.return.create') }}">
                            <button type="button" class="neo-btn-secondary text-xs">🔄 Kembalikan</button>
                        </a>
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('loans.export', array_filter(['status' => request('status'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}">
                                <button type="button" class="neo-btn-secondary text-xs">📥 Export CSV</button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="neo-card mb-6">
                <form method="GET" action="{{ route('loans.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari judul, nama, ISBN..."
                        class="neo-input flex-1">
                    <select name="status" class="neo-input w-full sm:w-48">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                        <option value="returned_ontime" {{ request('status') === 'returned_ontime' ? 'selected' : '' }}>Dikembalikan (Tepat)</option>
                        <option value="returned_late" {{ request('status') === 'returned_late' ? 'selected' : '' }}>Dikembalikan (Telat)</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Semua Dikembalikan</option>
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="neo-input w-full sm:w-40" placeholder="Dari">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="neo-input w-full sm:w-40" placeholder="Sampai">
                    <div class="flex gap-2">
                        <button type="submit" class="neo-btn-primary text-xs">Cari</button>
                        <a href="{{ route('loans.index') }}">
                            <button type="button" class="neo-btn-secondary text-xs">Reset</button>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Loan Table --}}
            <div class="neo-card overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-3 border-border">
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Buku</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Peminjam</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">NISN</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Pinjam</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Jatuh Tempo</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Kembali</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Status</th>
                            <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($loans as $loan)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4">
                                    <p class="font-heading font-semibold text-sm text-border truncate max-w-[150px]" title="{{ $loan->book->title ?? '-' }}">{{ $loan->book->title ?? '-' }}</p>
                                    <p class="font-body text-xs text-muted">{{ $loan->book->isbn ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <a href="{{ Auth::user()->isAdmin() ? route('users.show', $loan->user) : '#' }}" class="font-body text-sm text-border {{ Auth::user()->isAdmin() ? 'hover:text-primary underline' : '' }}">
                                        {{ $loan->user->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 font-mono text-xs text-muted">{{ $loan->user->nisn ?? '-' }}</td>
                                <td class="py-3 px-4 font-body text-sm text-muted">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 font-body text-sm text-muted">{{ $loan->due_date->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 font-body text-sm text-muted">{{ $loan->returned_at ? $loan->returned_at->format('d/m/Y') : '-' }}</td>
                                <td class="py-3 px-4">
                                    @if ($loan->isReturned())
                                        @if ($loan->denda > 0)
                                            <span class="neo-badge bg-coral text-white text-xs">Telat</span>
                                        @else
                                            <span class="neo-badge bg-primary text-white text-xs">Tepat</span>
                                        @endif
                                    @elseif ($loan->isOverdue())
                                        <span class="neo-badge bg-coral text-white text-xs">Terlambat ({{ $loan->getDaysLate() }}h)</span>
                                    @else
                                        <span class="neo-badge bg-lemon text-border text-xs">Dipinjam</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($loan->denda > 0)
                                        <p class="font-heading font-bold text-sm text-coral">Rp{{ number_format($loan->denda, 0, ',', '.') }}</p>
                                        @if ($loan->status_denda === 'belum_bayar')
                                            <form method="POST" action="{{ route('loans.pay-denda', $loan) }}" class="mt-1">
                                                @csrf
                                                <button type="submit" class="text-xs font-heading font-semibold text-primary hover:underline">Tandai Lunas</button>
                                            </form>
                                        @else
                                            <span class="text-xs font-heading font-semibold text-primary">Lunas ✓</span>
                                        @endif
                                    @elseif (!$loan->isReturned() && $loan->isOverdue())
                                        <p class="font-heading font-bold text-sm text-coral">Rp{{ number_format($loan->getPotentialDenda(), 0, ',', '.') }}</p>
                                        <p class="font-body text-xs text-muted">estimasi</p>
                                    @else
                                        <span class="font-body text-sm text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center">
                                    <div class="text-4xl mb-3">📭</div>
                                    <p class="font-heading font-semibold text-border">Tidak ada data peminjaman</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-6">
                    {{ $loans->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
