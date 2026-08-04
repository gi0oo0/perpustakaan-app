<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-2xl text-text leading-tight">
            Riwayat Peminjaman
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-apple-lg">

            @if (session('success'))
                <div class="mb-6 bg-apple-blue text-white px-6 py-4 font-display text-sm rounded-apple-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Summary --}}
            <div class="bg-surface-light rounded-apple-lg p-6 mb-6">
                <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-display font-semibold text-lg text-text">Riwayat Peminjaman</h3>
                        <p class="text-sm text-text-tertiary mt-1">Total bulan ini: <strong class="text-text">{{ $totalThisMonth }}</strong> · Masih dipinjam: <strong class="text-text">{{ $activeCount }}</strong></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('loans.borrow.create') }}">
                            <button type="button" class="apple-btn-primary text-xs">Pinjam</button>
                        </a>
                        <a href="{{ route('loans.return.create') }}">
                            <button type="button" class="apple-btn-secondary text-xs">Kembalikan</button>
                        </a>
                        @if (Auth::user()->isStaff())
                            <a href="{{ route('loans.export', array_filter(['status' => request('status'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}">
                                <button type="button" class="apple-btn-secondary text-xs">Export CSV</button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-apple-lg p-6 mb-6">
                <form method="GET" action="{{ route('loans.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, nama, ISBN..."
                        class="apple-input flex-1">
                    <select name="status" class="apple-input w-full sm:w-48">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                        <option value="returned_ontime" {{ request('status') === 'returned_ontime' ? 'selected' : '' }}>Dikembalikan (Tepat)</option>
                        <option value="returned_late" {{ request('status') === 'returned_late' ? 'selected' : '' }}>Dikembalikan (Telat)</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Semua Dikembalikan</option>
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="apple-input w-full sm:w-40" placeholder="Dari">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="apple-input w-full sm:w-40" placeholder="Sampai">
                    <div class="flex gap-2">
                        <button type="submit" class="apple-btn-primary text-xs">Cari</button>
                        <a href="{{ route('loans.index') }}">
                            <button type="button" class="apple-btn-secondary text-xs">Reset</button>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Loan Table --}}
            <div class="bg-white rounded-apple-lg overflow-hidden shadow-apple">
                <div class="overflow-x-auto">
                    <table class="apple-table w-full text-left">
                        <thead>
                            <tr class="border-b border-surface-lighter">
                                <th class="py-3 px-4 font-display text-xs text-text-tertiary">Buku</th>
                                <th class="py-3 px-4 font-display text-xs text-text-tertiary">Peminjam</th>
                                <th class="py-3 px-4 font-display text-xs text-text-tertiary">NISN</th>
                                <th class="py-3 px-4 font-display text-xs text-text-tertiary">Pinjam</th>
                                <th class="py-3 px-4 font-display text-xs text-text-tertiary">Jatuh Tempo</th>
                                <th class="py-3 px-4 font-display text-xs text-text-tertiary">Kembali</th>
                                <th class="py-3 px-4 font-display text-xs text-text-tertiary">Status</th>
                                <th class="py-3 px-4 font-display text-xs text-text-tertiary">Denda</th>
                                @if (Auth::user()->isStaff())
                                    <th class="py-3 px-4 font-display text-xs text-text-tertiary">Diproses Oleh</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($loans as $loan)
                                <tr class="border-b border-surface-lighter last:border-0 hover:bg-surface-light transition-colors">
                                    <td class="py-3 px-4">
                                        <p class="font-display font-semibold text-sm text-text truncate max-w-[150px]" title="{{ $loan->book->title ?? '-' }}">{{ $loan->book->title ?? '-' }}</p>
                                        <p class="text-xs text-text-tertiary">{{ $loan->book->isbn ?? '-' }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <a href="{{ Auth::user()->isAdmin() ? route('users.show', $loan->user) : '#' }}" class="text-sm text-text {{ Auth::user()->isAdmin() ? 'hover:text-apple-blue underline' : '' }}">
                                            {{ $loan->user->name ?? '-' }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-xs text-text-tertiary">{{ $loan->user->nisn ?? '-' }}</td>
                                    <td class="py-3 px-4 text-sm text-text-tertiary">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-sm text-text-tertiary">{{ $loan->due_date->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-sm text-text-tertiary">{{ $loan->returned_at ? $loan->returned_at->format('d/m/Y') : '-' }}</td>
                                    <td class="py-3 px-4">
                                        @if ($loan->isReturned())
                                            @if ($loan->denda > 0)
                                                <span class="apple-badge apple-badge-red text-xs">Telat</span>
                                            @else
                                                <span class="apple-badge apple-badge-green text-xs">Tepat</span>
                                            @endif
                                        @elseif ($loan->isOverdue())
                                            <span class="apple-badge apple-badge-red text-xs">Terlambat ({{ $loan->getDaysLate() }}h)</span>
                                        @else
                                            <span class="apple-badge apple-badge-yellow text-xs">Dipinjam</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if ($loan->denda > 0)
                                            <p class="font-display font-semibold text-sm text-danger">Rp{{ number_format($loan->denda, 0, ',', '.') }}</p>
                                            @if ($loan->status_denda === 'belum_bayar' && Auth::user()->isStaff())
                                                <form method="POST" action="{{ route('loans.pay-denda', $loan) }}" class="mt-1">
                                                    @csrf
                                                    <button type="submit" class="text-xs font-display text-apple-blue hover:underline">Tandai Lunas</button>
                                                </form>
                                            @else
                                                <span class="text-xs font-display text-apple-blue">Lunas</span>
                                            @endif
                                        @elseif (!$loan->isReturned() && $loan->isOverdue())
                                            <p class="font-display font-semibold text-sm text-danger">Rp{{ number_format($loan->getPotentialDenda(), 0, ',', '.') }}</p>
                                            <p class="text-xs text-text-tertiary">estimasi</p>
                                        @else
                                            <span class="text-sm text-text-tertiary">-</span>
                                        @endif
                                    </td>
                                    @if (Auth::user()->isStaff())
                                        <td class="py-3 px-4">
                                            @if ($loan->processor)
                                                <p class="text-xs text-text">{{ $loan->processor->name }}</p>
                                                <p class="text-xs text-text-tertiary">{{ $loan->returned_at ? $loan->returned_at->format('d/m H:i') : '' }}</p>
                                            @else
                                                <span class="text-xs text-text-tertiary">-</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center">
                                        <div class="text-4xl mb-3">📭</div>
                                        <p class="font-display font-semibold text-text">Tidak ada data peminjaman</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 pb-4 mt-4">
                    {{ $loans->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
