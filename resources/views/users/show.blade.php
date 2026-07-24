<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            👤 Detail Anggota
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('loans.index') }}" class="neo-btn-secondary inline-flex items-center gap-2 text-xs">
                    ← Kembali ke Riwayat
                </a>
            </div>

            {{-- User Info Card --}}
            <div class="neo-card mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="w-16 h-16 bg-lemon border-3 border-border shadow-neo rounded-full flex items-center justify-center text-3xl font-heading font-bold text-border flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="font-heading font-bold text-xl text-border">{{ $user->name }}</h3>
                        @if ($user->nisn)
                            <p class="font-mono text-sm text-muted mt-1">NISN: {{ $user->nisn }}</p>
                        @endif
                        <p class="font-body text-sm text-muted">{{ $user->email }}</p>
                        @if ($user->isAdmin())
                            <span class="neo-badge bg-coral text-white text-xs mt-1">Admin</span>
                        @else
                            <span class="neo-badge bg-primary text-white text-xs mt-1">Anggota</span>
                        @endif
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 border-t-3 border-border pt-6">
                    <div class="bg-lemon border-2 border-border p-3 text-center" style="box-shadow: 3px 3px 0px #111827;">
                        <p class="font-heading font-bold text-2xl text-border">{{ $totalLoans }}</p>
                        <p class="font-body text-xs text-muted uppercase">Total Pinjam</p>
                    </div>
                    <div class="bg-yellow-50 border-2 border-border p-3 text-center" style="box-shadow: 3px 3px 0px #111827;">
                        <p class="font-heading font-bold text-2xl text-border">{{ $activeLoans }}</p>
                        <p class="font-body text-xs text-muted uppercase">Dipinjam</p>
                    </div>
                    <div class="bg-red-50 border-2 border-coral p-3 text-center" style="box-shadow: 3px 3px 0px #111827;">
                        <p class="font-heading font-bold text-2xl text-coral">{{ $overdueLoans }}</p>
                        <p class="font-body text-xs text-muted uppercase">Terlambat</p>
                    </div>
                    <div class="bg-white border-2 {{ $totalDenda > 0 ? 'border-coral' : 'border-border' }} p-3 text-center" style="box-shadow: 3px 3px 0px #111827;">
                        <p class="font-heading font-bold text-2xl {{ $totalDenda > 0 ? 'text-coral' : 'text-border' }}">Rp{{ number_format($totalDenda, 0, ',', '.') }}</p>
                        <p class="font-body text-xs text-muted uppercase">Total Denda</p>
                    </div>
                </div>
            </div>

            {{-- Loan History --}}
            <div class="neo-card">
                <h3 class="font-heading font-bold text-lg text-border uppercase tracking-wide mb-4">📚 Riwayat Peminjaman</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b-3 border-border">
                                <th class="py-3 px-4 font-heading font-bold text-xs uppercase tracking-wide text-border">Buku</th>
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
                                        <p class="font-heading font-semibold text-sm text-border truncate max-w-[200px]" title="{{ $loan->book->title ?? '-' }}">{{ $loan->book->title ?? 'Buku dihapus' }}</p>
                                    </td>
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
                                        @else
                                            <span class="font-body text-sm text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center">
                                        <div class="text-4xl mb-3">📭</div>
                                        <p class="font-heading font-semibold text-border">Belum ada riwayat peminjaman</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $loans->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
