<nav x-data="{
    sidebarOpen: false,
    booksUrl: '{{ route('books.index') }}',
    loansUrl: '{{ route('loans.index') }}',
    borrowUrl: '{{ route('loans.borrow.create') }}',
    handleGlobalKey(e) {
        const tag = (document.activeElement.tagName || '').toLowerCase();
        const typing = tag === 'input' || tag === 'textarea' || tag === 'select' || document.activeElement.isContentEditable;
        if (typing) return;
        if (e.key === '/') {
            e.preventDefault();
            let input = null;
            for (const el of document.querySelectorAll('[data-global-search-input]')) {
                if (el.offsetParent !== null) {
                    input = el;
                    break;
                }
            }
            if (input) {
                input.focus();
                input.select();
            }
        } else if (e.key === 'k') {
            window.location.href = this.booksUrl;
        } else if (e.key === 'l') {
            window.location.href = this.loansUrl;
        } else if (e.key === 'b') {
            window.location.href = this.borrowUrl;
        }
    }
}" x-on:keydown.window="handleGlobalKey($event)" class="relative z-40">
    {{-- ===== Top bar ===== --}}
    <header class="sticky top-0 z-40 lg:pl-[240px]">
        <div class="flex items-center gap-3 h-14 px-4 sm:px-6 border-b border-white/[0.06] bg-night/70 backdrop-blur-xl">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden -ml-1 p-2 rounded-[8px] text-white/70 hover:text-white hover:bg-white/[0.06] transition-colors" aria-label="Buka menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="lg:hidden flex items-center gap-2 flex-1">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Perpustakaan" class="w-7 h-7 rounded-[8px] object-cover">
                <span class="font-display font-semibold tracking-tight text-[15px] text-white">Perpustakaan</span>
            </div>

            <div class="hidden lg:block w-full max-w-sm">
                <div x-data="globalSearch" class="relative w-full">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-white/35 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input data-global-search-input x-ref="input" type="text" x-model="query" @input="doSearch"
                               @keydown.escape="reset" autocomplete="off"
                               placeholder="Cari buku..."
                               class="w-full h-9 rounded-[8px] bg-white/[0.04] border border-white/[0.08] pl-9 pr-9 font-body text-sm text-white placeholder:text-white/30 focus:outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition-all duration-200">
                        <kbd class="absolute inset-y-0 right-3 hidden sm:flex items-center text-[11px] font-medium text-white/25 border border-white/10 rounded px-1.5 pointer-events-none">/</kbd>
                    </div>

                    {{-- Results dropdown --}}
                    <div x-show="open" @click.outside="reset()"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute left-0 right-0 top-full mt-2 glass rounded-[10px] shadow-glass-lg overflow-hidden z-50"
                         style="display: none;">
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="loading">
                                <div class="px-4 py-3 space-y-2">
                                    <div class="search-skeleton h-12 rounded-[8px]"></div>
                                    <div class="search-skeleton h-12 rounded-[8px]"></div>
                                    <div class="search-skeleton h-12 rounded-[8px]"></div>
                                </div>
                            </template>
                            <template x-if="!loading && results.length === 0">
                                <div class="px-4 py-8 text-center">
                                    <div class="text-3xl mb-2">🔍</div>
                                    <p class="font-body text-sm text-white/50">Buku tidak ditemukan</p>
                                </div>
                            </template>
                            <template x-for="r in results" :key="r.id">
                                <button @click="go(r.url)" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white/[0.06] transition-colors text-start">
                                    <template x-if="r.cover_image">
                                        <img :src="r.cover_image" :alt="r.title" class="h-11 w-8 object-cover rounded-md border border-white/10 flex-shrink-0">
                                    </template>
                                    <template x-if="!r.cover_image">
                                        <div class="h-11 w-8 rounded-md bg-white/[0.06] border border-white/10 flex items-center justify-center text-base flex-shrink-0">📖</div>
                                    </template>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-body font-medium text-sm text-white truncate" x-text="r.title"></p>
                                        <p class="font-body text-xs text-white/40 truncate" x-text="r.author + ' · ' + (r.isbn || '-')"></p>
                                    </div>
                                    <span class="glass-badge flex-shrink-0" :class="r.available ? 'glass-badge-green' : 'glass-badge-red'">
                                        <span x-text="'Stok ' + r.stock"></span>
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 lg:hidden"></div>

            @if (Auth::user()->isAdmin())
                <span class="glass-badge-violet hidden sm:inline-flex">Admin</span>
            @elseif (Auth::user()->role === 'staff')
                <span class="glass-badge-yellow hidden sm:inline-flex">Staff</span>
            @endif

            {{-- User dropdown --}}
            <x-dropdown align="right" width="56">
                <x-slot name="trigger">
                    <button class="flex items-center gap-2.5 p-1.5 pr-2 pl-1.5 rounded-full hover:bg-white/[0.06] transition-colors duration-200 group">
                        @if (Auth::user()->profile_image)
                            <img src="{{ Auth::user()->profile_image_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <span class="w-8 h-8 rounded-full bg-primary flex items-center justify-center font-display font-semibold text-sm text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        @endif
                        <span class="hidden sm:flex items-center gap-1.5">
                            <span class="font-body text-sm text-white/85 group-hover:text-white transition-colors">{{ Auth::user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-white/40 group-hover:text-white/70 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-3 border-b border-white/10">
                        <div class="font-body text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
                        <div class="font-body text-xs text-white/45 mt-0.5 truncate">{{ Auth::user()->email }}</div>
                        @if (Auth::user()->nisn)
                            <div class="font-mono text-xs text-white/40 mt-1">NISN: {{ Auth::user()->nisn }}</div>
                        @endif
                        @if (Auth::user()->isAdmin())
                            <span class="glass-badge-violet mt-2">Admin</span>
                        @elseif (Auth::user()->role === 'staff')
                            <span class="glass-badge-yellow mt-2">Staff</span>
                        @else
                            <span class="glass-badge-gray mt-2">Anggota</span>
                        @endif
                    </div>

                    @php
                        $activeLoans = Auth::user()->loans()->whereNull('returned_at')->get();
                        $overdueCount = $activeLoans->filter(fn ($l) => $l->due_date->isPast())->count();
                        $totalDenda = Auth::user()->loans()->sum('denda');
                    @endphp
                    <div class="px-4 py-3 border-b border-white/10">
                        <div class="flex items-center gap-1.5 text-[11px] font-semibold tracking-widest uppercase text-white/40 mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Pinjaman Saya
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="glass-inset rounded-[8px] px-2 py-1.5 text-center">
                                <div class="font-display font-bold text-sm text-white">{{ $activeLoans->count() }}</div>
                                <div class="text-[10px] text-white/45 font-medium mt-0.5">Dipinjam</div>
                            </div>
                            <div class="glass-inset rounded-[8px] px-2 py-1.5 text-center">
                                <div class="font-display font-bold text-sm {{ $overdueCount > 0 ? 'text-rose-300' : 'text-white' }}">{{ $overdueCount }}</div>
                                <div class="text-[10px] text-white/45 font-medium mt-0.5">Terlambat</div>
                            </div>
                            <div class="glass-inset rounded-[8px] px-2 py-1.5 text-center">
                                <div class="font-display font-bold text-sm text-emerald-300">{{ number_format($totalDenda, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-white/45 font-medium mt-0.5">Denda</div>
                            </div>
                        </div>
                    </div>
                    <x-dropdown-link :href="route('profile.edit')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil Saya
                    </x-dropdown-link>
                    <div x-data="themeToggle" class="p-1.5 border-t border-white/10">
                        <button @click="toggle" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-[8px] text-white/70 hover:text-white hover:bg-white/[0.06] transition-colors font-body text-sm">
                            <template x-if="dark">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            </template>
                            <template x-if="!dark">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </template>
                            <span x-text="dark ? 'Mode Gelap' : 'Mode Terang'"></span>
                            <span class="ml-auto text-xs text-white/35" x-text="dark ? '🌙' : '☀️'"></span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        {{-- Mobile search (full-width below top bar) --}}
        <div class="lg:hidden px-4 sm:px-6 pb-3 bg-night/70 backdrop-blur-xl">
            <div x-data="globalSearch" class="relative w-full">
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-white/35 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input data-global-search-input x-ref="input" type="text" x-model="query" @input="doSearch"
                           @keydown.escape="reset" autocomplete="off"
                           placeholder="Cari buku, penulis, ISBN..."
                           class="w-full h-9 rounded-[8px] bg-white/[0.04] border border-white/[0.08] pl-9 pr-9 font-body text-sm text-white placeholder:text-white/30 focus:outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/10 transition-all duration-200">
                    <kbd class="absolute inset-y-0 right-3 hidden sm:flex items-center text-[11px] font-medium text-white/25 border border-white/10 rounded px-1.5 pointer-events-none">/</kbd>
                </div>

                <div x-show="open" @click.outside="reset()"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute left-0 right-0 top-full mt-2 glass rounded-[10px] shadow-glass-lg overflow-hidden z-50"
                     style="display: none;">
                    <div class="max-h-96 overflow-y-auto">
                        <template x-if="loading">
                            <div class="px-4 py-3 space-y-2">
                                <div class="search-skeleton h-12 rounded-[8px]"></div>
                                <div class="search-skeleton h-12 rounded-[8px]"></div>
                                <div class="search-skeleton h-12 rounded-[8px]"></div>
                            </div>
                        </template>
                        <template x-if="!loading && results.length === 0">
                            <div class="px-4 py-8 text-center">
                                <div class="text-3xl mb-2">🔍</div>
                                <p class="font-body text-sm text-white/50">Buku tidak ditemukan</p>
                            </div>
                        </template>
                        <template x-for="r in results" :key="r.id">
                            <button @click="go(r.url)" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white/[0.06] transition-colors text-start">
                                <template x-if="r.cover_image">
                                    <img :src="r.cover_image" :alt="r.title" class="h-11 w-8 object-cover rounded-md border border-white/10 flex-shrink-0">
                                </template>
                                <template x-if="!r.cover_image">
                                    <div class="h-11 w-8 rounded-md bg-white/[0.06] border border-white/10 flex items-center justify-center text-base flex-shrink-0">📖</div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <p class="font-body font-medium text-sm text-white truncate" x-text="r.title"></p>
                                    <p class="font-body text-xs text-white/40 truncate" x-text="r.author + ' · ' + (r.isbn || '-')"></p>
                                </div>
                                <span class="glass-badge flex-shrink-0" :class="r.available ? 'glass-badge-green' : 'glass-badge-red'">
                                    <span x-text="'Stok ' + r.stock"></span>
                                </span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ===== Sidebar (desktop) ===== --}}
    <aside class="hidden lg:flex fixed inset-y-0 left-0 w-[240px] z-40 flex-col border-r border-white/[0.06] bg-night-deep">
        <div class="flex items-center gap-3 px-5 h-14 border-b border-white/[0.06]">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Perpustakaan" class="w-8 h-8 rounded-[8px] object-cover">
            <div>
                <div class="font-display font-semibold tracking-tight leading-none text-[15px] text-white">Perpustakaan</div>
                <div class="text-[11px] text-white/40 mt-1">Sistem Manajemen</div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            <p class="px-3 pb-1.5 pt-1 text-[11px] font-medium tracking-wider uppercase text-white/30">Menu Utama</p>

            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" label="Dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </x-sidebar-link>

            <x-sidebar-link :href="route('books.index')" :active="request()->routeIs('books.*')" label="Katalog Buku">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </x-sidebar-link>

            <x-sidebar-link :href="route('loans.index')" :active="request()->routeIs('loans.index')" label="Riwayat Peminjaman">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </x-sidebar-link>

            <x-sidebar-link :href="route('loans.borrow.create')" :active="request()->routeIs('loans.borrow.*')" label="Pinjam Buku">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>
            </x-sidebar-link>

            @if (Auth::user()->isStaff())
                <x-sidebar-link :href="route('loans.return.create')" :active="request()->routeIs('loans.return.*')" label="Kembalikan Buku">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </x-sidebar-link>
            @endif

            @if (Auth::user()->isAdmin())
                <p class="px-3 pt-4 pb-1.5 text-[11px] font-medium tracking-wider uppercase text-white/30">Administrasi</p>
                <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')" label="Manajemen Anggota">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </x-sidebar-link>
            @endif

            <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" label="Pengaturan Akun">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </x-sidebar-link>
        </div>

        <div class="px-3 py-3 border-t border-white/[0.06]">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-[8px] font-body text-sm text-white/50 hover:text-white hover:bg-white/[0.04] transition-colors duration-150">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== Mobile drawer ===== --}}
    <div x-cloak
         x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"
         @click="sidebarOpen = false"></div>

    <aside x-cloak
           x-show="sidebarOpen"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 w-[280px] z-50 flex flex-col bg-night-deep border-r border-white/10 shadow-glass-lg lg:hidden">
        <div class="flex items-center justify-between px-5 h-14 border-b border-white/[0.06]">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Perpustakaan" class="w-8 h-8 rounded-[8px] object-cover">
                <span class="font-display font-semibold tracking-tight text-[15px] text-white">Perpustakaan</span>
            </div>
            <button @click="sidebarOpen = false" class="p-2 rounded-[8px] text-white/60 hover:text-white hover:bg-white/[0.06] transition-colors" aria-label="Tutup menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            <p class="px-3 pb-1.5 pt-1 text-[11px] font-medium tracking-wider uppercase text-white/30">Menu Utama</p>

            @php
                $links = [
                    ['route' => 'dashboard', 'active' => request()->routeIs('dashboard'), 'label' => 'Dashboard',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                    ['route' => 'books.index', 'active' => request()->routeIs('books.*'), 'label' => 'Katalog Buku',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
                    ['route' => 'loans.index', 'active' => request()->routeIs('loans.index'), 'label' => 'Riwayat Peminjaman',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'],
                    ['route' => 'loans.borrow.create', 'active' => request()->routeIs('loans.borrow.*'), 'label' => 'Pinjam Buku',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/>'],
                ];
            @endphp
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" class="relative flex items-center gap-3 px-3 py-2.5 rounded-[8px] font-body text-sm {{ $link['active'] ? 'text-white bg-white/[0.06] [&>svg]:text-white/85' : 'text-white/55 hover:text-white hover:bg-white/[0.04] [&>svg]:text-white/40 hover:[&>svg]:text-white/75 transition-colors duration-150' }}">
                    @if ($link['active'])
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-4 rounded-r-full bg-primary"></span>
                    @endif
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $link['icon'] !!}</svg>
                    <span class="flex-1">{{ $link['label'] }}</span>
                </a>
            @endforeach

            @if (Auth::user()->isStaff())
                <a href="{{ route('loans.return.create') }}" class="relative flex items-center gap-3 px-3 py-2.5 rounded-[8px] font-body text-sm {{ request()->routeIs('loans.return.*') ? 'text-white bg-white/[0.06] [&>svg]:text-white/85' : 'text-white/55 hover:text-white hover:bg-white/[0.04] [&>svg]:text-white/40 hover:[&>svg]:text-white/75 transition-colors duration-150' }}">
                    @if (request()->routeIs('loans.return.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-4 rounded-r-full bg-primary"></span>
                    @endif
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span class="flex-1">Kembalikan Buku</span>
                </a>
            @endif

            @if (Auth::user()->isAdmin())
                <p class="px-3 pt-4 pb-1.5 text-[11px] font-medium tracking-wider uppercase text-white/30">Administrasi</p>
                <a href="{{ route('users.index') }}" class="relative flex items-center gap-3 px-3 py-2.5 rounded-[8px] font-body text-sm {{ request()->routeIs('users.*') ? 'text-white bg-white/[0.06] [&>svg]:text-white/85' : 'text-white/55 hover:text-white hover:bg-white/[0.04] [&>svg]:text-white/40 hover:[&>svg]:text-white/75 transition-colors duration-150' }}">
                    @if (request()->routeIs('users.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-4 rounded-r-full bg-primary"></span>
                    @endif
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="flex-1">Manajemen Anggota</span>
                </a>
            @endif

            <a href="{{ route('profile.edit') }}" class="relative flex items-center gap-3 px-3 py-2.5 rounded-[8px] font-body text-sm {{ request()->routeIs('profile.*') ? 'text-white bg-white/[0.06] [&>svg]:text-white/85' : 'text-white/55 hover:text-white hover:bg-white/[0.04] [&>svg]:text-white/40 hover:[&>svg]:text-white/75 transition-colors duration-150' }}">
                @if (request()->routeIs('profile.*'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-4 rounded-r-full bg-primary"></span>
                @endif
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="flex-1">Pengaturan Akun</span>
            </a>
        </div>

        <div class="px-4 py-4 border-t border-white/[0.06]">
            <div class="flex items-center gap-3 mb-3 px-1">
                @if (Auth::user()->profile_image)
                    <img src="{{ Auth::user()->profile_image_url }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                @else
                    <span class="w-9 h-9 rounded-full bg-primary flex items-center justify-center font-display font-semibold text-sm text-white flex-shrink-0">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                @endif
                <div class="min-w-0">
                    <p class="font-body text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="font-body text-xs text-white/45 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-[8px] font-body text-sm text-white/50 hover:text-white hover:bg-white/[0.04] transition-colors duration-150">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>
</nav>
