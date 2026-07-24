<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            📚 Peminjaman
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-primary text-white border-3 border-border shadow-neo px-6 py-4 font-heading font-semibold uppercase tracking-wide text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Summary Banner --}}
            <div class="bg-lemon border-3 border-border shadow-neo p-6 mb-6 relative overflow-hidden">
                <div class="absolute top-3 right-6 w-12 h-12 border-4 border-border rounded-full opacity-20"></div>
                <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-border uppercase tracking-wide">Riwayat Peminjaman</h3>
                        <p class="font-body text-sm text-muted mt-1">Kelola peminjaman dan pengembalian buku</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('loans.borrow.create') }}">
                            <button type="button" class="neo-btn-primary text-xs">📦 Pinjam</button>
                        </a>
                        <a href="{{ route('loans.return.create') }}">
                            <button type="button" class="neo-btn-secondary text-xs">🔄 Kembalikan</button>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Filter Pills --}}
            <div class="flex flex-wrap gap-2 mb-6">
                <a href="{{ route('loans.index') }}"
                   class="neo-badge {{ !request('status') ? 'bg-border text-white' : 'bg-white text-border hover:bg-gray-50' }} transition-all duration-150 cursor-pointer">
                    Semua
                </a>
                <a href="{{ route('loans.index', ['status' => 'active']) }}"
                   class="neo-badge {{ request('status') === 'active' ? 'bg-lemon text-border' : 'bg-white text-border hover:bg-gray-50' }} transition-all duration-150 cursor-pointer">
                    📦 Dipinjam
                </a>
                <a href="{{ route('loans.index', ['status' => 'overdue']) }}"
                   class="neo-badge {{ request('status') === 'overdue' ? 'bg-coral text-white' : 'bg-white text-border hover:bg-gray-50' }} transition-all duration-150 cursor-pointer">
                    ⚠ Terlambat
                </a>
                <a href="{{ route('loans.index', ['status' => 'returned']) }}"
                   class="neo-badge {{ request('status') === 'returned' ? 'bg-primary text-white' : 'bg-white text-border hover:bg-gray-50' }} transition-all duration-150 cursor-pointer">
                    ✓ Dikembalikan
                </a>
            </div>

            {{-- Loan Cards --}}
            <div class="space-y-3">
                @forelse ($loans as $loan)
                    <div class="bg-white border-3 border-border shadow-neo p-4 flex flex-col sm:flex-row sm:items-center gap-4 tilt-hover transition-all duration-150">
                        {{-- Cover --}}
                        @if ($loan->book && $loan->book->cover_image)
                            <img src="{{ asset($loan->book->cover_image) }}" alt="{{ $loan->book->title ?? '' }}" class="h-16 w-12 object-cover border-2 border-border flex-shrink-0">
                        @else
                            <div class="h-16 w-12 bg-gray-100 border-2 border-border flex items-center justify-center text-xl flex-shrink-0">📖</div>
                        @endif

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="font-heading font-bold text-base text-border truncate">{{ $loan->book->title ?? 'Buku dihapus' }}</h4>
                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                                <span class="font-body text-xs text-muted">📅 Pinjam: {{ $loan->loan_date->format('d/m/Y') }}</span>
                                <span class="font-body text-xs {{ $loan->due_date < \Carbon\Carbon::today() && !$loan->returned_at ? 'text-coral font-semibold' : 'text-muted' }}">
                                    ⏰ Tempo: {{ $loan->due_date->format('d/m/Y') }}
                                </span>
                                <span class="font-body text-xs text-muted">
                                    {{ $loan->returned_at ? '✅ Kembali: '.$loan->returned_at->format('d/m/Y') : '' }}
                                </span>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="flex-shrink-0">
                            @if ($loan->returned_at)
                                <span class="neo-badge bg-primary text-white">✓ Dikembalikan</span>
                            @elseif ($loan->due_date < \Carbon\Carbon::today())
                                <span class="neo-badge bg-coral text-white">⚠ Terlambat</span>
                            @else
                                <span class="neo-badge bg-lemon text-border">📦 Dipinjam</span>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div class="bg-white border-3 border-border shadow-neo p-12 text-center">
                        <div class="text-5xl mb-4">📭</div>
                        <p class="font-heading font-semibold text-lg text-border">Belum ada riwayat</p>
                        <p class="font-body text-sm text-muted mt-1 mb-4">Mulai pinjam buku untuk melihat riwayat di sini.</p>
                        <a href="{{ route('loans.borrow.create') }}">
                            <button type="button" class="neo-btn-primary">📦 Pinjam Buku Sekarang</button>
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $loans->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
