<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-gradient">Detail Pengguna</h2>
                <p class="font-body text-sm text-white/45 mt-1">Informasi lengkap pengguna</p>
            </div>
            <div class="flex items-center gap-3">
                @if($user->isMember())
                    <form method="POST" action="{{ route('users.reset-password', $user) }}" onsubmit="return confirmReset(event, this)">
                        @csrf
                        <button type="submit" class="glass-btn-danger" title="Set password sama dengan NISN">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            Reset Password
                        </button>
                    </form>
                @endif
                <a href="{{ route('users.edit', $user) }}" class="glass-btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <a href="{{ route('users.index') }}" class="glass-btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="reveal">
        {{-- User Info Card --}}
        <div>
            <div class="glass p-6">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-soft flex items-center justify-center mb-4 shadow-glow overflow-hidden">
                        @if($user->profile_image)
                            <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl sm:text-3xl font-display font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <h2 class="font-display text-xl font-semibold text-white">{{ $user->name }}</h2>
                    <p class="font-body text-sm text-white/45 mt-1">{{ $user->email }}</p>

                    <div class="mt-4">
                        @if($user->role == 'admin')
                            <span class="glass-badge-red">Admin</span>
                        @elseif($user->role == 'staff')
                            <span class="glass-badge-yellow">Staff</span>
                        @else
                            <span class="glass-badge-blue">Anggota</span>
                        @endif
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-white/10 space-y-4">
                    <div class="flex justify-between gap-4">
                        <span class="font-body text-sm text-white/45">NISN</span>
                        <span class="font-body text-sm font-medium text-white font-mono">{{ $user->nisn }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="font-body text-sm text-white/45">Email</span>
                        <span class="font-body text-sm font-medium text-white break-all text-right">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="font-body text-sm text-white/45">Role</span>
                        <span class="font-body text-sm font-medium text-white capitalize">{{ $user->role }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="font-body text-sm text-white/45">Terdaftar</span>
                        <span class="font-body text-sm font-medium text-white">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats & History --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="glass p-5">
                    <p class="font-body text-xs text-white/40">Total Pinjam</p>
                    <p class="font-display text-2xl font-bold text-white mt-1"><span x-data="countUp" data-count="{{ $totalLoans }}" x-text="displayed"></span></p>
                </div>
                <div class="glass p-5">
                    <p class="font-body text-xs text-white/40">Dipinjam</p>
                    <p class="font-display text-2xl font-bold text-sky-300 mt-1"><span x-data="countUp" data-count="{{ $activeLoans }}" x-text="displayed"></span></p>
                </div>
                <div class="glass p-5">
                    <p class="font-body text-xs text-white/40">Terlambat</p>
                    <p class="font-display text-2xl font-bold text-rose-300 mt-1"><span x-data="countUp" data-count="{{ $overdueLoans }}" x-text="displayed"></span></p>
                </div>
                <div class="glass p-5">
                    <p class="font-body text-xs text-white/40">Total Denda</p>
                    <p class="font-display text-2xl font-bold text-white mt-1">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="glass p-6">
                <h3 class="font-display text-lg font-semibold text-white mb-1">Riwayat Peminjaman</h3>
                <p class="font-body text-sm text-white/40 mb-4">Seluruh transaksi pengguna ini</p>

                <div class="overflow-x-auto">
                    <table class="glass-table w-full">
                        <thead>
                            <tr class="border-b border-white/[0.07] bg-white/[0.02]">
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="font-medium text-white">{{ $loan->book->title }}</div>
                                            <div class="font-body text-xs text-white/40">{{ $loan->book->author }}</div>
                                        </div>
                                    </td>
                                    <td class="text-white/60">{{ $loan->loan_date->format('d M Y') }}</td>
                                    <td class="text-white/60">{{ $loan->returned_at ? $loan->returned_at->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($loan->status == 'borrowed')
                                            <span class="glass-badge-blue">Dipinjam</span>
                                        @elseif($loan->status == 'returned')
                                            <span class="glass-badge-green">Dikembalikan</span>
                                        @elseif($loan->status == 'late')
                                            <span class="glass-badge-red">Terlambat</span>
                                        @else
                                            <span class="glass-badge-yellow">{{ ucfirst($loan->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 rounded-2xl bg-white/[0.04] border border-white/10 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                            </div>
                                            <p class="font-display font-semibold text-white">Belum ada riwayat peminjaman</p>
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
</x-app-layout>
