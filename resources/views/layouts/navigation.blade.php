<nav x-data="{ open: false }" class="apple-nav border-b border-white/40 sticky top-0 z-50">
    <div class="apple-container mx-auto px-apple-lg">
        <div class="flex justify-between h-[60px]">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-display text-heading-sm text-text flex items-center gap-2">
                        <span class="hidden sm:inline">Perpustakaan</span>
                    </a>
                </div>

                <div class="hidden space-x-1 sm:flex sm:items-center sm:ms-8">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-4 py-2 text-body-xs font-body rounded-full {{ request()->routeIs('dashboard') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }} transition-all duration-200">
                        Dashboard
                    </a>
                    <a href="{{ route('books.index') }}"
                       class="inline-flex items-center px-4 py-2 text-body-xs font-body rounded-full {{ request()->routeIs('books.*') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }} transition-all duration-200">
                        Buku
                    </a>
                    <a href="{{ route('loans.index') }}"
                       class="inline-flex items-center px-4 py-2 text-body-xs font-body rounded-full {{ request()->routeIs('loans.index') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }} transition-all duration-200">
                        Peminjaman
                    </a>
                    <a href="{{ route('loans.borrow.create') }}"
                       class="inline-flex items-center px-4 py-2 text-body-xs font-body rounded-full {{ request()->routeIs('loans.borrow.*') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }} transition-all duration-200">
                        Pinjam
                    </a>
                    @if (Auth::user()->isStaff())
                        <a href="{{ route('loans.return.create') }}"
                           class="inline-flex items-center px-4 py-2 text-body-xs font-body rounded-full {{ request()->routeIs('loans.return.*') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }} transition-all duration-200">
                            Kembalikan
                        </a>
                    @endif
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('users.index') }}"
                           class="inline-flex items-center px-4 py-2 text-body-xs font-body rounded-full {{ request()->routeIs('users.*') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }} transition-all duration-200">
                            Anggota
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @if (Auth::user()->isAdmin())
                    <span class="apple-badge-blue mr-3">Admin</span>
                @elseif (Auth::user()->role === 'staff')
                    <span class="apple-badge-yellow mr-3">Staff</span>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-body-xs font-body text-text-tertiary hover:text-text rounded-apple hover:bg-surface-light transition-all duration-200">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-surface-lighter">
                            <div class="font-body text-body-sm font-normal text-text">{{ Auth::user()->name }}</div>
                            <div class="font-body text-caption text-text-tertiary">{{ Auth::user()->email }}</div>
                            @if (Auth::user()->nisn)
                                <div class="font-mono text-caption text-text-tertiary mt-1">NISN: {{ Auth::user()->nisn }}</div>
                            @endif
                            @if (Auth::user()->isAdmin())
                                <span class="apple-badge-blue text-caption mt-1">Admin</span>
                            @elseif (Auth::user()->role === 'staff')
                                <span class="apple-badge-yellow text-caption mt-1">Staff</span>
                            @else
                                <span class="apple-badge-gray text-caption mt-1">User</span>
                            @endif
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-apple text-text-tertiary hover:text-text hover:bg-surface-light transition-all duration-200">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-white/40 bg-white/70 backdrop-blur-2xl">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-body-xs font-body {{ request()->routeIs('dashboard') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }}">
                Dashboard
            </a>
            <a href="{{ route('books.index') }}" class="block px-4 py-2 text-body-xs font-body {{ request()->routeIs('books.*') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }}">
                Buku
            </a>
            <a href="{{ route('loans.index') }}" class="block px-4 py-2 text-body-xs font-body {{ request()->routeIs('loans.index') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }}">
                Peminjaman
            </a>
            <a href="{{ route('loans.borrow.create') }}" class="block px-4 py-2 text-body-xs font-body {{ request()->routeIs('loans.borrow.*') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }}">
                Pinjam
            </a>
            @if (Auth::user()->isStaff())
                <a href="{{ route('loans.return.create') }}" class="block px-4 py-2 text-body-xs font-body {{ request()->routeIs('loans.return.*') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }}">
                    Kembalikan
                </a>
            @endif
            @if (Auth::user()->isAdmin())
                <a href="{{ route('users.index') }}" class="block px-4 py-2 text-body-xs font-body {{ request()->routeIs('users.*') ? 'bg-surface-lighter text-text' : 'text-text-tertiary hover:text-text hover:bg-surface-light' }}">
                    Anggota
                </a>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-surface-lighter">
            <div class="px-4">
                <div class="font-body text-body-sm text-text">{{ Auth::user()->name }}</div>
                <div class="font-body text-caption text-text-tertiary">{{ Auth::user()->email }}</div>
                @if (Auth::user()->nisn)
                    <div class="font-mono text-caption text-text-tertiary mt-1">NISN: {{ Auth::user()->nisn }}</div>
                @endif
                @if (Auth::user()->isAdmin())
                    <span class="apple-badge-blue text-caption mt-1">Admin</span>
                @elseif (Auth::user()->role === 'staff')
                    <span class="apple-badge-yellow text-caption mt-1">Staff</span>
                @else
                    <span class="apple-badge-gray text-caption mt-1">User</span>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-body-xs font-body text-text-tertiary hover:text-text hover:bg-surface-light">
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-body-xs font-body text-danger hover:bg-surface-light">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
