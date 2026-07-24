<nav x-data="{ open: false }" class="bg-white border-b-3 border-border relative z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-heading font-bold text-xl text-border flex items-center gap-2">
                        <span class="text-2xl">📚</span>
                        <span class="hidden sm:inline">Perpustakaan</span>
                    </a>
                </div>

                <div class="hidden space-x-2 sm:flex sm:items-center sm:ms-8">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide border-3 border-transparent {{ request()->routeIs('dashboard') ? 'bg-lemon border-border shadow-neo-sm' : 'text-muted hover:text-border hover:bg-gray-50' }} transition-all duration-150">
                        Dashboard
                    </a>
                    <a href="{{ route('books.index') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide border-3 border-transparent {{ request()->routeIs('books.*') ? 'bg-primary text-white border-border shadow-neo-sm' : 'text-muted hover:text-border hover:bg-gray-50' }} transition-all duration-150">
                        Buku
                    </a>
                    <a href="{{ route('loans.index') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide border-3 border-transparent {{ request()->routeIs('loans.index') ? 'bg-coral text-white border-border shadow-neo-sm' : 'text-muted hover:text-border hover:bg-gray-50' }} transition-all duration-150">
                        Peminjaman
                    </a>
                    <a href="{{ route('loans.borrow.create') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide border-3 border-transparent {{ request()->routeIs('loans.borrow.*') ? 'bg-lemon border-border shadow-neo-sm' : 'text-muted hover:text-border hover:bg-gray-50' }} transition-all duration-150">
                        Pinjam
                    </a>
                    <a href="{{ route('loans.return.create') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide border-3 border-transparent {{ request()->routeIs('loans.return.*') ? 'bg-lemon border-border shadow-neo-sm' : 'text-muted hover:text-border hover:bg-gray-50' }} transition-all duration-150">
                        Kembalikan
                    </a>
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('users.index') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide border-3 border-transparent {{ request()->routeIs('users.*') ? 'bg-yellow-400 text-border border-border shadow-neo-sm' : 'text-muted hover:text-border hover:bg-gray-50' }} transition-all duration-150">
                            Anggota
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @if (Auth::user()->isAdmin())
                    <span class="neo-badge bg-coral text-white mr-3 text-xs">Admin</span>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border-3 border-border bg-white shadow-neo-sm text-sm font-heading font-semibold uppercase tracking-wide text-border hover:bg-gray-50 transition-all duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <div class="font-heading font-semibold text-sm text-border">{{ Auth::user()->name }}</div>
                            <div class="font-body text-xs text-muted">{{ Auth::user()->email }}</div>
                            @if (Auth::user()->nisn)
                                <div class="font-mono text-xs text-muted mt-1">NISN: {{ Auth::user()->nisn }}</div>
                            @endif
                            @if (Auth::user()->isAdmin())
                                <span class="neo-badge bg-coral text-white text-xs mt-1">Admin</span>
                            @else
                                <span class="neo-badge bg-gray-200 text-muted text-xs mt-1">User</span>
                            @endif
                        </div>
                        <x-dropdown-link :href="route('profile.edit')" class="font-body">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="font-body">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 border-3 border-border bg-white shadow-neo-sm text-border hover:bg-gray-50 transition-all duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t-3 border-border">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide {{ request()->routeIs('dashboard') ? 'bg-lemon text-border' : 'text-muted hover:bg-gray-50' }}">
                Dashboard
            </a>
            <a href="{{ route('books.index') }}" class="block px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide {{ request()->routeIs('books.*') ? 'bg-primary text-white' : 'text-muted hover:bg-gray-50' }}">
                Buku
            </a>
            <a href="{{ route('loans.index') }}" class="block px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide {{ request()->routeIs('loans.index') ? 'bg-coral text-white' : 'text-muted hover:bg-gray-50' }}">
                Peminjaman
            </a>
            <a href="{{ route('loans.borrow.create') }}" class="block px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide {{ request()->routeIs('loans.borrow.*') ? 'bg-lemon text-border' : 'text-muted hover:bg-gray-50' }}">
                Pinjam
            </a>
            <a href="{{ route('loans.return.create') }}" class="block px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide {{ request()->routeIs('loans.return.*') ? 'bg-lemon text-border' : 'text-muted hover:bg-gray-50' }}">
                Kembalikan
            </a>
            @if (Auth::user()->isAdmin())
                <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide {{ request()->routeIs('users.*') ? 'bg-yellow-400 text-border' : 'text-muted hover:bg-gray-50' }}">
                    Anggota
                </a>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t-3 border-border">
            <div class="px-4">
                <div class="font-heading font-semibold text-base text-border">{{ Auth::user()->name }}</div>
                <div class="font-body text-sm text-muted">{{ Auth::user()->email }}</div>
                @if (Auth::user()->nisn)
                    <div class="font-mono text-xs text-muted mt-1">NISN: {{ Auth::user()->nisn }}</div>
                @endif
                @if (Auth::user()->isAdmin())
                    <span class="neo-badge bg-coral text-white text-xs mt-1">Admin</span>
                @else
                    <span class="neo-badge bg-gray-200 text-muted text-xs mt-1">User</span>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide text-muted hover:bg-gray-50">
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm font-heading font-semibold uppercase tracking-wide text-coral hover:bg-gray-50">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
