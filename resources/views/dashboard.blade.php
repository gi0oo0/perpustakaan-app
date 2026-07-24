<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Summary Banner --}}
            <div class="bg-lemon border-3 border-border shadow-neo p-8 relative overflow-hidden">
                <div class="absolute top-4 right-8 w-16 h-16 border-4 border-border rounded-full opacity-20"></div>
                <div class="absolute bottom-4 right-24 w-0 h-0 border-l-[20px] border-l-transparent border-r-[20px] border-r-transparent border-b-[30px] border-b-primary opacity-20"></div>
                <h3 class="font-heading font-bold text-xl text-border mb-2">Selamat Datang! 👋</h3>
                <p class="font-body text-muted">Kelola perpustakaan Anda dengan mudah dan menyenangkan.</p>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white border-3 border-border shadow-neo-sm p-4 text-center">
                        <div class="font-heading font-bold text-3xl text-primary">{{ \App\Models\Book::count() }}</div>
                        <div class="font-body text-sm text-muted mt-1">Total Buku</div>
                    </div>
                    <div class="bg-white border-3 border-border shadow-neo-sm p-4 text-center">
                        <div class="font-heading font-bold text-3xl text-border">{{ \App\Models\Loan::whereNull('returned_at')->count() }}</div>
                        <div class="font-body text-sm text-muted mt-1">Dipinjam</div>
                    </div>
                    <div class="bg-white border-3 border-border shadow-neo-sm p-4 text-center">
                        <div class="font-heading font-bold text-3xl text-coral">{{ \App\Models\Loan::whereNull('returned_at')->where('due_date', '<', \Carbon\Carbon::today())->count() }}</div>
                        <div class="font-body text-sm text-muted mt-1">Terlambat</div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div>
                <h3 class="font-heading font-semibold text-lg text-border mb-4 uppercase tracking-wide">Akses Cepat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="{{ route('books.index') }}" class="book-card neo-card text-center group">
                        <div class="w-14 h-14 bg-primary border-3 border-border shadow-neo-sm mx-auto flex items-center justify-center text-2xl mb-3 group-hover:shadow-neo-hover transition-all duration-150">📖</div>
                        <div class="font-heading font-semibold text-border uppercase tracking-wide text-sm">Daftar Buku</div>
                        <p class="font-body text-xs text-muted mt-1">Lihat & kelola koleksi buku</p>
                    </a>
                    <a href="{{ route('loans.borrow.create') }}" class="book-card neo-card text-center group">
                        <div class="w-14 h-14 bg-lemon border-3 border-border shadow-neo-sm mx-auto flex items-center justify-center text-2xl mb-3 group-hover:shadow-neo-hover transition-all duration-150">📦</div>
                        <div class="font-heading font-semibold text-border uppercase tracking-wide text-sm">Pinjam Buku</div>
                        <p class="font-body text-xs text-muted mt-1">Scan & pinjam buku baru</p>
                    </a>
                    <a href="{{ route('loans.return.create') }}" class="book-card neo-card text-center group">
                        <div class="w-14 h-14 bg-coral border-3 border-border shadow-neo-sm mx-auto flex items-center justify-center text-2xl mb-3 group-hover:shadow-neo-hover transition-all duration-150">🔄</div>
                        <div class="font-heading font-semibold text-border uppercase tracking-wide text-sm">Kembalikan Buku</div>
                        <p class="font-body text-xs text-muted mt-1">Scan & kembalikan buku</p>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
