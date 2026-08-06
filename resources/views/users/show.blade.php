<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-display text-text">Detail Pengguna</h1>
                <p class="mt-1 text-text-tertiary">Informasi lengkap pengguna</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('users.edit', $user) }}" class="apple-btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('users.index') }}" class="apple-btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-apple-lg">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Info Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-apple-lg p-6">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-20 h-20 bg-surface-light rounded-full flex items-center justify-center mb-4">
                                <span class="text-2xl font-display font-semibold text-text">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <h2 class="text-xl font-display font-semibold text-text">{{ $user->name }}</h2>
                            <p class="text-text-tertiary text-sm mt-1">{{ $user->email }}</p>

                            <div class="mt-4">
                                @if($user->role == 'admin')
                                    <span class="apple-badge-red">Admin</span>
                                @elseif($user->role == 'staff')
                                    <span class="apple-badge-yellow">Staff</span>
                                @else
                                    <span class="apple-badge-blue">Anggota</span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-surface-lighter space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-text-tertiary">NISN</span>
                                <span class="text-sm font-medium text-text">{{ $user->nisn }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-text-tertiary">Email</span>
                                <span class="text-sm font-medium text-text">{{ $user->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-text-tertiary">Role</span>
                                <span class="text-sm font-medium text-text capitalize">{{ $user->role }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-text-tertiary">Terdaftar</span>
                                <span class="text-sm font-medium text-text">{{ $user->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats & History -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-apple-lg p-5 shadow-sm">
                            <div class="text-sm text-text-tertiary">Total Pinjam</div>
                            <div class="text-2xl font-display font-semibold text-text mt-1">{{ $totalPinjam }}</div>
                        </div>
                        <div class="bg-white rounded-apple-lg p-5 shadow-sm">
                            <div class="text-sm text-text-tertiary">Dipinjam</div>
                            <div class="text-2xl font-display font-semibold text-primary mt-1">{{ $dipinjam }}</div>
                        </div>
                        <div class="bg-white rounded-apple-lg p-5 shadow-sm">
                            <div class="text-sm text-text-tertiary">Terlambat</div>
                            <div class="text-2xl font-display font-semibold text-danger mt-1">{{ $terlambat }}</div>
                        </div>
                        <div class="bg-white rounded-apple-lg p-5 shadow-sm">
                            <div class="text-sm text-text-tertiary">Total Denda</div>
                            <div class="text-2xl font-display font-semibold text-text mt-1">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <!-- Loan History -->
                    <div class="bg-white rounded-apple-lg p-6">
                        <h3 class="text-lg font-display font-semibold text-text mb-4">Riwayat Peminjaman</h3>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-surface-lighter">
                                        <th class="text-left py-3 px-4 text-sm font-display font-semibold text-text-tertiary">Buku</th>
                                        <th class="text-left py-3 px-4 text-sm font-display font-semibold text-text-tertiary">Tgl Pinjam</th>
                                        <th class="text-left py-3 px-4 text-sm font-display font-semibold text-text-tertiary">Tgl Kembali</th>
                                        <th class="text-left py-3 px-4 text-sm font-display font-semibold text-text-tertiary">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loans as $loan)
                                        <tr class="border-b border-surface-lighter hover:bg-surface-light transition-colors">
                                            <td class="py-4 px-4">
                                                <div>
                                                    <div class="text-sm font-medium text-text">{{ $loan->book->title }}</div>
                                                    <div class="text-xs text-text-tertiary">{{ $loan->book->author }}</div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-sm text-text-secondary">{{ $loan->borrow_date->format('d M Y') }}</td>
                                            <td class="py-4 px-4 text-sm text-text-secondary">{{ $loan->return_date ? $loan->return_date->format('d M Y') : '-' }}</td>
                                            <td class="py-4 px-4">
                                                @if($loan->status == 'borrowed')
                                                    <span class="apple-badge-blue">Dipinjam</span>
                                                @elseif($loan->status == 'returned')
                                                    <span class="apple-badge-green">Dikembalikan</span>
                                                @elseif($loan->status == 'late')
                                                    <span class="apple-badge-red">Terlambat</span>
                                                @else
                                                    <span class="apple-badge-yellow">{{ ucfirst($loan->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-12 text-center text-text-tertiary">
                                                <div class="flex flex-col items-center gap-2">
                                                    <svg class="w-12 h-12 text-text-quaternary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                    <span>Belum ada riwayat peminjaman</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($loans->hasPages())
                            <div class="mt-4">
                                {{ $loans->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
