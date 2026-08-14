<x-app-layout>
    <x-slot name="header">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-white/40 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-white/70 transition-colors">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-white/60">Pinjam Buku</span>
            </nav>
            <h1 class="font-display text-2xl sm:text-[26px] font-bold tracking-tight text-white">Pinjam Buku</h1>
            <p class="text-sm text-white/45 mt-1">Scan QR atau pilih buku untuk membuat transaksi peminjaman.</p>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-5 glass rounded-glass-sm border-rose-400/25 px-4 py-3">
            <p class="font-display text-sm font-semibold text-rose-300 mb-1">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li class="font-body text-sm text-rose-200/80">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $isStaff = Auth::user()->isStaff();
        $booksJson = $books->map(fn ($b) => [
            'isbn' => $b->isbn,
            'title' => $b->title,
            'author' => $b->author,
            'kategori' => $b->kategori,
            'stock' => (int) $b->stock,
            'cover' => $b->cover_url,
        ])->values();
        $membersJson = $members->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'nisn' => $u->nisn,
            'email' => $u->email,
        ])->values();
        $initialMember = $isStaff
            ? ($members->firstWhere('id', old('user_id')) ?: null)
            : Auth::user();
        $initialMemberJson = $initialMember ? [
            'id' => $initialMember->id,
            'name' => $initialMember->name,
            'nisn' => $initialMember->nisn,
            'email' => $initialMember->email,
        ] : null;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 items-start"
         x-data="borrowFlow(@js($booksJson), @js($membersJson), @js($isStaff), @js($initialMemberJson))"
         x-on:book-selected.window="setBook($event.detail)">

        <form id="borrow-form" method="POST" action="{{ route('loans.borrow.store') }}"
              class="lg:col-span-2 space-y-5"
              x-data="loanOptions({{ old('duration_days', 7) }}, {{ old('denda_per_day', 500) }})">
            @csrf

            {{-- 1. PEMINJAM --}}
            <section id="peminjam-section" class="glass p-5 sm:p-6">
                <h3 class="font-display font-semibold text-base text-white">Peminjam</h3>
                <p class="text-[13px] text-white/45 mt-0.5" x-text="isStaff ? 'Pilih anggota yang akan meminjam buku.' : 'Kamu terdaftar sebagai peminjam.'"></p>

                <div x-show="!member && isStaff" x-cloak class="mt-4">
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/35 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" x-model="memberQuery" autocomplete="off"
                               class="glass-input pl-10"
                               placeholder="Cari nama atau NISN anggota...">
                    </div>

                    <div x-show="memberQuery && filteredMembers.length" class="mt-2 glass p-1.5 shadow-glass-lg max-h-56 overflow-y-auto rounded-glass-sm">
                        <template x-for="m in filteredMembers" :key="m.id">
                            <button type="button" @click="setMember(m)"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-[10px] hover:bg-white/[0.07] transition-colors text-left">
                                <span class="w-9 h-9 rounded-lg bg-gradient-soft flex items-center justify-center text-sm font-semibold text-white flex-shrink-0"
                                      x-text="m.name.split(' ').map(s => s.charAt(0)).slice(0, 2).join('').toUpperCase()"></span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-medium text-white truncate" x-text="m.name"></span>
                                    <span class="block text-xs text-white/45">NISN: <span class="font-mono" x-text="m.nisn || '-'"></span></span>
                                </span>
                            </button>
                        </template>
                        <p x-show="filteredMembers.length" x-cloak class="px-3 py-1 text-[11px] text-white/35" x-text="filteredMembers.length + ' anggota ditemukan'"></p>
                    </div>

                    <p x-show="!memberQuery" class="mt-2 text-xs text-white/35 px-1" x-text="members.length + ' anggota terdaftar. Ketik nama atau NISN untuk memilih.'"></p>
                </div>

                <div x-show="!member && !isStaff" x-cloak class="mt-4 text-sm text-white/40">
                    Belum ada peminjam ditentukan.
                </div>

                <div x-show="member" x-cloak class="mt-4 flex items-center gap-3.5 glass-inset rounded-[12px] p-3.5">
                    <span class="w-11 h-11 rounded-[10px] bg-gradient-soft flex items-center justify-center text-base font-semibold text-white flex-shrink-0"
                          x-text="member.name.split(' ').map(s => s.charAt(0)).slice(0, 2).join('').toUpperCase()"></span>
                    <div class="flex-1 min-w-0">
                        <p class="font-display font-semibold text-[15px] text-white truncate" x-text="member.name"></p>
                        <p class="text-[13px] text-white/45 mt-0.5">NISN: <span class="font-mono text-white/70" x-text="member.nisn || '-'"></span></p>
                    </div>
                    <button type="button" @click="clearMember()" x-show="isStaff" x-cloak class="glass-btn-ghost text-xs px-3 py-1.5">Ganti</button>
                </div>

                @if ($errors->has('user_id'))
                    <p class="text-xs text-rose-300 mt-2">{{ $errors->first('user_id') }}</p>
                @endif
                <p x-show="memberError" x-cloak class="text-xs text-rose-300 mt-2" x-text="memberError"></p>
            </section>

            {{-- 2. SCAN QR BUKU --}}
            <section class="glass p-5 sm:p-6">
                <h3 class="font-display font-semibold text-base text-white">Scan QR Buku</h3>
                <p class="text-[13px] text-white/45 mt-0.5">Arahkan kamera ke QR Code pada buku.</p>

                <div id="scan-area" class="scanner-viewport mt-4 h-[260px] sm:h-[280px] rounded-[14px] border border-white/10">
                    <div id="reader" class="absolute inset-0 hidden"></div>

                    <div id="scan-idle" class="flex flex-col items-center justify-center gap-2 px-6 text-center absolute inset-0">
                        <div class="w-14 h-14 rounded-2xl bg-white/[0.06] border border-white/10 flex items-center justify-center">
                            <svg class="w-7 h-7 text-primary-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9zm6 5a3 3 0 116 0 3 3 0 01-6 0z"/>
                            </svg>
                        </div>
                        <p class="font-display font-semibold text-white text-base mt-1">Scan QR Buku</p>
                        <p class="text-sm text-white/45">Arahkan kamera ke QR Code</p>
                        <button type="button" id="btn-start-camera" class="glass-btn-primary mt-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Mulai Scan
                        </button>
                    </div>

                    <div id="scan-active" class="hidden flex-col items-center justify-center absolute inset-0">
                        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-52 h-52 sm:w-60 sm:h-60 rounded-2xl">
                            <span class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-primary rounded-tl-lg"></span>
                            <span class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-primary rounded-tr-lg"></span>
                            <span class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-primary rounded-bl-lg"></span>
                            <span class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-primary rounded-br-lg"></span>
                            <div class="scan-line"></div>
                        </div>
                        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-2 px-3 py-1.5 rounded-full border border-white/10 bg-night/80 backdrop-blur text-xs text-white/70">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                            Mencari QR Code...
                        </div>
                    </div>

                    <div id="scan-success" class="hidden flex-col items-center justify-center gap-2 px-6 text-center absolute inset-0">
                        <div class="w-12 h-12 rounded-full bg-emerald-400/15 border border-emerald-400/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="font-display font-semibold text-emerald-300">QR Code ditemukan</p>
                        <p id="scan-success-text" class="text-xs text-white/50 font-mono"></p>
                        <button type="button" id="btn-rescan" class="glass-btn-ghost text-xs px-3 py-1.5 mt-1">Scan Lagi</button>
                    </div>

                    <div id="scan-error" class="hidden flex-col items-center justify-center gap-2 px-6 text-center absolute inset-0">
                        <div class="w-12 h-12 rounded-full bg-amber-400/15 border border-amber-400/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <p class="font-display font-semibold text-white">QR Code tidak dikenali</p>
                        <p id="scan-error-text" class="text-xs text-white/50"></p>
                        <button type="button" id="btn-retry-scan" class="glass-btn-secondary text-xs px-3 py-1.5 mt-2">Coba Lagi</button>
                    </div>
                </div>

                <div class="mt-3 flex justify-end">
                    <button type="button" id="btn-stop-camera" class="glass-btn-ghost text-xs px-3 py-1.5 hidden">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Matikan Kamera
                    </button>
                </div>

                <div class="flex items-center gap-3 my-5">
                    <span class="flex-1 h-px bg-white/[0.08]"></span>
                    <span class="text-xs text-white/35">atau</span>
                    <span class="flex-1 h-px bg-white/[0.08]"></span>
                </div>

                <div>
                    <h4 class="font-display font-medium text-[13px] text-white/70">Masukkan ISBN Manual</h4>
                    <div class="mt-2 flex flex-col sm:flex-row gap-2">
                        <input type="text" x-model="isbnInput" autocomplete="off" placeholder="Masukkan ISBN buku..." class="glass-input sm:flex-1 font-mono">
                        <button type="button" @click="addManual()" class="glass-btn-secondary whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Buku
                        </button>
                    </div>
                    <p x-show="isbnError" x-cloak class="text-xs text-rose-300 mt-2" x-text="isbnError"></p>
                    @if ($errors->has('isbn'))
                        <p class="text-xs text-rose-300 mt-2">{{ $errors->first('isbn') }}</p>
                    @endif
                </div>
            </section>

            {{-- Buku Tersedia (mobile: setelah scanner) --}}
            <div class="lg:hidden">
                <x-book-picker />
            </div>

            {{-- 3. BUKU DIPILIH --}}
            <section id="selected-book-section" class="glass p-5 sm:p-6">
                <h3 class="font-display font-semibold text-base text-white">Buku Dipilih</h3>
                <p class="text-[13px] text-white/45 mt-0.5">Buku yang akan dipinjam pada transaksi ini.</p>

                <template x-if="!book">
                    <div class="mt-4 flex items-center gap-3 text-sm text-white/40">
                        <span class="w-9 h-9 rounded-[10px] bg-white/[0.05] border border-white/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </span>
                        <span>Belum ada buku dipilih. Scan QR atau pilih dari daftar <span class="text-white/60">Buku Tersedia</span>.</span>
                    </div>
                </template>

                <template x-if="book">
                    <div class="mt-4 flex items-start gap-4">
                        <img :src="book.cover" :alt="book.title" class="w-14 h-[74px] rounded-lg object-cover border border-white/10 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-display font-semibold text-[15px] text-white" x-text="book.title"></p>
                            <p class="text-[13px] text-white/50 mt-0.5" x-text="book.author"></p>
                            <p class="text-xs text-white/40 mt-1">ISBN: <span class="font-mono text-white/60" x-text="book.isbn"></span></p>
                            <span class="inline-flex items-center gap-1.5 mt-2.5 glass-badge-green">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Buku tersedia · Stok <span x-text="book.stock"></span>
                            </span>
                        </div>
                        <button type="button" @click="clearBook()" class="glass-btn-ghost text-xs px-3 py-1.5">Ganti Buku</button>
                    </div>
                </template>

                <p x-show="bookError" x-cloak class="text-xs text-rose-300 mt-3" x-text="bookError"></p>
            </section>

            {{-- 4. DURASI & DENDA --}}
            <section class="glass p-5 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <h4 class="font-display font-semibold text-[13px] text-white/70">Durasi Peminjaman</h4>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <template x-for="opt in durationOptions()" :key="opt">
                                <button type="button" @click="setDuration(opt)"
                                        class="px-4 py-2 rounded-[10px] text-sm font-medium border transition-all duration-150"
                                        :class="duration === opt
                                            ? 'border-primary bg-primary/15 text-white font-semibold'
                                            : 'border-white/10 bg-white/[0.04] text-white/50 hover:bg-white/[0.08] hover:text-white'">
                                    <span x-text="opt"></span> Hari
                                </button>
                            </template>
                        </div>
                        @if ($errors->has('duration_days'))
                            <p class="text-xs text-rose-300 mt-2">{{ $errors->first('duration_days') }}</p>
                        @endif
                    </div>

                    <div>
                        <h4 class="font-display font-semibold text-[13px] text-white/70">Denda Keterlambatan</h4>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <template x-for="rate in rates" :key="rate">
                                <button type="button" @click="setRate(rate)"
                                        class="px-3 py-1.5 rounded-full text-[13px] font-medium border transition-all duration-150"
                                        :class="denda === rate
                                            ? 'border-primary bg-primary/15 text-white font-semibold'
                                            : 'border-white/10 bg-white/[0.04] text-white/50 hover:bg-white/[0.08] hover:text-white'">
                                    <span x-text="'Rp ' + rate.toLocaleString('id-ID')"></span>
                                </button>
                            </template>
                        </div>
                        @if ($errors->has('denda_per_day'))
                            <p class="text-xs text-rose-300 mt-2">{{ $errors->first('denda_per_day') }}</p>
                        @endif
                    </div>
                </div>

                <div class="mt-5 flex items-start gap-2.5 text-xs text-white/45 leading-relaxed">
                    <svg class="w-4 h-4 text-white/35 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p>
                        Peminjaman <span class="text-white/70" x-text="duration"></span> hari — jatuh tempo
                        <span class="text-white/70" x-text="new Date(Date.now() + duration * 864e5).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></span>.
                        Denda <span class="text-white/70" x-text="'Rp ' + denda.toLocaleString('id-ID')"></span>/hari bila terlambat. Durasi maksimal <span class="text-white/70" x-text="maxDuration()"></span> hari.
                    </p>
                </div>
            </section>

            {{-- 5. RINGKASAN --}}
            <section class="summary-card p-5 sm:p-6">
                <h3 class="font-display font-semibold text-base text-white">Ringkasan Peminjaman</h3>
                <p class="text-[13px] text-white/45 mt-0.5">Periksa kembali sebelum mengonfirmasi.</p>

                <template x-if="member || book">
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-white/40">Peminjam</p>
                            <p class="mt-1 text-sm text-white" x-text="member ? member.name + ' (NISN ' + (member.nisn || '-') + ')' : '-'"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-white/40">Buku</p>
                            <p class="mt-1 text-sm text-white" x-text="book ? book.title : '-'"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-white/40">Durasi</p>
                            <p class="mt-1 text-sm text-white"><span x-text="duration"></span> hari</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-white/40">Tanggal Pinjam</p>
                            <p class="mt-1 text-sm text-white" x-text="new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-white/40">Jatuh Tempo</p>
                            <p class="mt-1 text-sm text-white" x-text="new Date(Date.now() + duration * 864e5).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-white/40">Denda</p>
                            <p class="mt-1 text-sm text-white">Rp <span x-text="denda.toLocaleString('id-ID')"></span> / hari keterlambatan</p>
                        </div>
                    </div>
                </template>

                <template x-if="!member && !book">
                    <div class="mt-4 flex items-center gap-2.5 text-[13px] text-white/40 py-2">
                        <svg class="w-4 h-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Lengkapi peminjam & buku untuk melihat ringkasan.
                    </div>
                </template>
            </section>

            {{-- 6. KONFIRMASI --}}
            <div>
                <button type="submit" @click.prevent="confirm()" :disabled="submitting"
                        class="glass-btn-primary w-full py-3.5 text-base font-semibold">
                    <template x-if="!submitting">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Konfirmasi Peminjaman
                        </span>
                    </template>
                    <template x-if="submitting">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Memproses peminjaman...
                        </span>
                    </template>
                </button>
                <p class="mt-2.5 text-center text-xs text-white/35">Dengan mengonfirmasi, Anda menyetujui ketentuan peminjaman perpustakaan.</p>
            </div>

            <input type="hidden" name="isbn" :value="book ? book.isbn : ''">
            <input type="hidden" name="user_id" :value="member ? member.id : ''">
            <input type="hidden" name="duration_days" :value="duration">
            <input type="hidden" name="denda_per_day" :value="denda">
        </form>

        {{-- Buku Tersedia (desktop) --}}
        <aside class="hidden lg:block lg:sticky lg:top-24">
            <x-book-picker />
        </aside>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        window.__borrowBooks = @js($booksJson);

        window.borrowFlow = (books, members, isStaff, initialMember) => ({
            books: books || [],
            members: members || [],
            isStaff,
            member: initialMember || null,
            book: null,
            memberQuery: '',
            bookQuery: '',
            bookKategori: '',
            isbnInput: @js(old('isbn', '')) || '',
            memberError: '',
            bookError: '',
            isbnError: '',
            submitting: false,

            get filteredMembers() {
                const q = this.memberQuery.trim().toLowerCase();
                return this.members.filter((m) =>
                    (m.name + ' ' + (m.nisn || '') + ' ' + m.email).toLowerCase().includes(q)
                );
            },
            get filteredBooks() {
                const q = this.bookQuery.trim().toLowerCase();
                return this.books.filter((b) =>
                    (!q ||
                        (b.title + ' ' + b.author + ' ' + b.isbn + ' ' + (b.kategori || '')).toLowerCase().includes(q)) &&
                    (!this.bookKategori || b.kategori === this.bookKategori)
                );
            },
            get categories() {
                return [...new Set(this.books.map((b) => b.kategori).filter(Boolean))];
            },

            setMember(m) {
                this.member = m;
                this.memberError = '';
                this.memberQuery = '';
            },
            clearMember() {
                this.member = null;
            },
            setBook(b) {
                this.book = b;
                this.bookError = '';
                this.isbnError = '';
                this.isbnInput = b.isbn;
                if (window.innerWidth < 1024) {
                    const el = document.getElementById('selected-book-section');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            },
            clearBook() {
                this.book = null;
            },
            addManual() {
                const isbn = this.isbnInput.trim();
                if (!isbn) {
                    this.isbnError = 'Masukkan ISBN buku terlebih dahulu.';
                    return;
                }
                const b = this.books.find((x) => x.isbn === isbn);
                if (!b) {
                    this.isbnError = 'Buku dengan ISBN tersebut tidak ditemukan.';
                    return;
                }
                this.setBook(b);
                window.toast('Buku dipilih: ' + b.title, 'success');
            },
            confirm() {
                if (this.submitting) return;
                this.memberError = '';
                this.bookError = '';
                let ok = true;
                if (!this.member) {
                    this.memberError = 'Silakan pilih anggota terlebih dahulu.';
                    ok = false;
                }
                if (!this.book) {
                    this.bookError = 'Silakan pilih buku terlebih dahulu.';
                    ok = false;
                }
                if (!ok) {
                    this.$nextTick(() => {
                        const target = this.memberError ? 'peminjam-section' : 'selected-book-section';
                        const el = document.getElementById(target);
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }
                this.submitting = true;
                this.$nextTick(() => {
                    const form = document.getElementById('borrow-form');
                    if (form) form.submit();
                });
            },
        });
    </script>
    <script>
        (function () {
            const readerDiv = document.getElementById('reader');
            const btnStart = document.getElementById('btn-start-camera');
            const btnStop = document.getElementById('btn-stop-camera');
            const btnRetry = document.getElementById('btn-retry-scan');
            const btnRescan = document.getElementById('btn-rescan');
            const scanSuccessText = document.getElementById('scan-success-text');
            const scanErrorText = document.getElementById('scan-error-text');

            let html5QrCode = null;

            function setScanner(state) {
                const map = { idle: 'scan-idle', active: 'scan-active', success: 'scan-success', error: 'scan-error' };
                Object.keys(map).forEach((key) => {
                    const el = document.getElementById(map[key]);
                    const show = key === state;
                    el.classList.toggle('hidden', !show);
                    el.classList.toggle('flex', show);
                });
            }

            function stopCamera() {
                if (html5QrCode) {
                    html5QrCode.stop().catch(() => {});
                    html5QrCode = null;
                }
                readerDiv.classList.add('hidden');
                btnStop.classList.add('hidden');
            }

            function startWith(config) {
                html5QrCode = new Html5Qrcode('reader');
                return html5QrCode.start(
                    config,
                    {
                        fps: 15,
                        qrbox: function (vw, vh) {
                            const size = Math.floor(Math.min(vw, vh) * 0.7);
                            return { width: size, height: size };
                        },
                        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                    },
                    onScanSuccess,
                    function () {}
                );
            }

            function startCamera() {
                readerDiv.classList.remove('hidden');
                btnStop.classList.remove('hidden');
                setScanner('active');
                startWith({ facingMode: 'environment' })
                    .catch(function () {
                        return startWith({ facingMode: 'user' });
                    })
                    .catch(function () {
                        return startWith(true);
                    })
                    .catch(function () {
                        scanErrorText.textContent = 'Tidak dapat mengakses kamera. Periksa izin browser lalu coba lagi.';
                        stopCamera();
                        setScanner('error');
                    });
            }

            function onScanSuccess(decodedText) {
                const book = (window.__borrowBooks || []).find((b) => b.isbn === decodedText);
                if (book) {
                    window.dispatchEvent(new CustomEvent('book-selected', { detail: book }));
                    scanSuccessText.textContent = 'ISBN ' + decodedText;
                    stopCamera();
                    setScanner('success');
                } else {
                    scanErrorText.textContent = 'ISBN ' + decodedText + ' tidak ditemukan di katalog atau sedang tidak tersedia.';
                    stopCamera();
                    setScanner('error');
                }
            }

            btnStart.addEventListener('click', startCamera);
            btnRetry.addEventListener('click', startCamera);
            btnRescan.addEventListener('click', startCamera);
            btnStop.addEventListener('click', function () {
                stopCamera();
                setScanner('idle');
            });
        })();
    </script>
</x-app-layout>
