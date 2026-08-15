<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-[24px] font-semibold tracking-tight text-white">Detail Pengguna</h2>
                <p class="font-body text-[13px] text-[#8B949E] mt-1">Informasi lengkap pengguna</p>
            </div>
            <div class="flex items-center gap-2.5">
                @if($user->isMember())
                    <form method="POST" action="{{ route('users.reset-password', $user) }}" onsubmit="return confirmReset(event, this)">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 h-[38px] px-4 rounded-[8px] bg-[#E76B73]/[0.10] border border-[#E76B73]/25 text-[#E7A0A5] text-sm font-medium hover:bg-[#E76B73]/[0.16] transition-colors" title="Set password sama dengan NISN">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            Reset Password
                        </button>
                    </form>
                @endif
                <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-1.5 h-[38px] px-4 rounded-[8px] bg-[#2DB7A8]/[0.10] border border-[#2DB7A8]/30 text-[#2DB7A8] text-sm font-medium hover:bg-[#2DB7A8]/[0.16] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 h-[38px] px-4 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#A5ADB3] text-sm font-medium hover:bg-[#252A2E] hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        html.dark .detail-table thead {
            background-color: #202428;
        }
        html.dark .detail-table thead th {
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
            padding-left: 0.9rem;
            padding-right: 0.9rem;
            color: #747C82;
            font-size: 12px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        html.dark .detail-table thead tr {
            border-color: rgba(255, 255, 255, 0.06);
        }
        html.dark .detail-table tbody td {
            padding-top: 0.7rem;
            padding-bottom: 0.7rem;
            padding-left: 0.9rem;
            padding-right: 0.9rem;
            font-size: 13px;
        }
        html.dark .detail-table tbody tr {
            border-color: rgba(255, 255, 255, 0.045);
        }
        html.dark .detail-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.025);
        }
    </style>

    <div class="grid grid-cols-1 lg:grid-cols-[5fr_8fr] gap-5" x-data="reveal">
        {{-- Profile Card --}}
        <div class="glass rounded-[12px] p-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-[#2DB7A8]/25 flex items-center justify-center overflow-hidden flex-shrink-0 border border-white/10">
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-xl font-display font-semibold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <h2 class="font-display text-[20px] font-semibold text-white truncate">{{ $user->name }}</h2>
                    <p class="font-body text-[13px] text-[#A5ADB3] truncate mt-0.5">{{ $user->email }}</p>
                    <div class="mt-2">
                        @if($user->role == 'admin')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#E76B73]/[0.10] text-[#E7A0A5] border border-white/[0.06]">Admin</span>
                        @elseif($user->role == 'staff')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#D9A441]/[0.12] text-[#D9A441] border border-white/[0.06]">Staff</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#5C9FE8]/[0.12] text-[#5C9FE8] border border-white/[0.06]">Anggota</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-5 border-t border-white/[0.06] space-y-3">
                <div class="flex gap-3">
                    <span class="font-body text-xs text-[#747C82] w-20 shrink-0 pt-0.5">NISN</span>
                    <span class="font-body text-[13px] font-medium text-[#F1F3F4] font-mono">{{ $user->nisn }}</span>
                </div>
                <div class="flex gap-3">
                    <span class="font-body text-xs text-[#747C82] w-20 shrink-0 pt-0.5">Email</span>
                    <span class="font-body text-[13px] font-medium text-[#F1F3F4] break-all">{{ $user->email }}</span>
                </div>
                <div class="flex gap-3">
                    <span class="font-body text-xs text-[#747C82] w-20 shrink-0 pt-0.5">Role</span>
                    <span class="font-body text-[13px] font-medium text-[#F1F3F4] capitalize">{{ $user->role }}</span>
                </div>
                <div class="flex gap-3">
                    <span class="font-body text-xs text-[#747C82] w-20 shrink-0 pt-0.5">Terdaftar</span>
                    <span class="font-body text-[13px] font-medium text-[#F1F3F4]">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Summary & History --}}
        <div class="space-y-5">
            <div class="glass rounded-[12px] p-5">
                <div class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-white/[0.045]">
                    <div class="py-3 md:py-0 md:px-5 first:md:pl-0 last:md:pr-0">
                        <p class="font-body text-xs text-[#747C82]">Total Pinjam</p>
                        <p class="font-display text-[24px] font-semibold text-[#F1F3F4] mt-1"><span x-data="countUp" data-count="{{ $totalLoans }}" x-text="displayed"></span></p>
                    </div>
                    <div class="py-3 md:py-0 md:px-5">
                        <p class="font-body text-xs text-[#747C82]">Dipinjam</p>
                        <p class="font-display text-[24px] font-semibold text-[#5C9FE8] mt-1"><span x-data="countUp" data-count="{{ $activeLoans }}" x-text="displayed"></span></p>
                    </div>
                    <div class="py-3 md:py-0 md:px-5">
                        <p class="font-body text-xs text-[#747C82]">Terlambat</p>
                        <p class="font-display text-[24px] font-semibold text-[#E76B73] mt-1"><span x-data="countUp" data-count="{{ $overdueLoans }}" x-text="displayed"></span></p>
                    </div>
                    <div class="py-3 md:py-0 md:px-5 last:md:pr-0">
                        <p class="font-body text-xs text-[#747C82]">Total Denda</p>
                        <p class="font-display text-[24px] font-semibold {{ $totalDenda > 0 ? 'text-[#E76B73]' : 'text-[#F1F3F4]' }} mt-1">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="glass rounded-[12px] p-6">
                <h3 class="font-display text-[16px] font-semibold text-white">Riwayat Peminjaman</h3>
                <p class="font-body text-[13px] text-[#747C82] mt-0.5 mb-4">Seluruh transaksi pengguna ini</p>

                <div class="overflow-x-auto">
                    <table class="glass-table detail-table w-full min-w-[560px]">
                        <thead>
                            <tr>
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
                                            <div class="font-body text-xs text-[#747C82]">{{ $loan->book->author }}</div>
                                        </div>
                                    </td>
                                    <td class="text-[#A5ADB3]">{{ $loan->loan_date->format('d M Y') }}</td>
                                    <td class="text-[#A5ADB3]">{{ $loan->returned_at ? $loan->returned_at->format('d M Y') : '-' }}</td>
                                    <td>
                                        <span class="glass-badge {{ $loan->status_color === 'coral' ? 'glass-badge-red' : ($loan->status_color === 'primary' ? 'glass-badge-green' : 'glass-badge-yellow') }}">{{ $loan->status_label }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="h-[150px] flex flex-col items-center justify-center gap-2 text-center">
                                            <div class="w-10 h-10 rounded-lg bg-white/[0.04] border border-white/[0.06] flex items-center justify-center">
                                                <svg class="w-5 h-5 text-[#747C82]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                            </div>
                                            <p class="font-body text-sm font-medium text-[#F1F3F4]">Belum ada riwayat peminjaman</p>
                                            <p class="font-body text-xs text-[#747C82]">Pengguna ini belum memiliki transaksi peminjaman.</p>
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