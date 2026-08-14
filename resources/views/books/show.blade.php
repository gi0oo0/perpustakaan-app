<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-gradient">Detail Buku</h2>
                <p class="font-body text-sm text-white/45 mt-1">Informasi lengkap koleksi</p>
            </div>
            <a href="{{ route('books.index') }}" class="glass-btn-secondary text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Katalog
            </a>
        </div>
    </x-slot>

    <div class="glass overflow-hidden" x-data="reveal">
        <div class="grid grid-cols-1 lg:grid-cols-5">
            {{-- Cover --}}
            <div class="lg:col-span-2 p-5 sm:p-8 lg:border-r border-white/[0.07] flex items-center justify-center bg-white/[0.02]">
                @if ($book->cover_url)
                    <div class="relative group">
                        <div class="absolute inset-0 bg-primary/20 blur-3xl opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}"
                             class="relative w-full max-w-xs rounded-glass-lg shadow-glass-lg group-hover:scale-[1.02] transition-transform duration-500">
                    </div>
                @else
                    @php($coverColor = ['#334155', '#6B8F71', '#B8A58A', '#647F9E', '#A86F5E', '#7C8465', '#64748B', '#A4777E'][$book->id % 8])
                    <div class="cover w-full max-w-xs h-80 rounded-glass-lg border border-white/10 p-6 flex flex-col justify-end" style="background-color: {{ $coverColor }};">
                        <p class="font-semibold leading-snug text-xl">{{ $book->title }}</p>
                        <p class="text-xs mt-2 opacity-80">{{ $book->author }}</p>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="lg:col-span-3 p-5 sm:p-8">
                <div class="flex items-center gap-2 mb-3">
                    @if ($book->kategori)
                        <span class="glass-badge-violet">{{ $book->kategori }}</span>
                    @endif
                    @if ($book->stock > 0)
                        <span class="glass-badge-green">Tersedia</span>
                    @else
                        <span class="glass-badge-red">Habis</span>
                    @endif
                </div>

                <h1 class="font-display text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight text-white leading-tight">
                    {{ $book->title }}
                </h1>
                <p class="font-body text-base sm:text-lg text-violet-300 font-medium mt-2">{{ $book->author }}</p>

                {{-- Metadata Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-8">
                    <div class="border-t border-white/10 pt-3">
                        <div class="font-body text-xs text-white/40">ISBN</div>
                        <div class="font-display font-semibold text-white mt-1 font-mono text-sm">{{ $book->isbn }}</div>
                    </div>
                    <div class="border-t border-white/10 pt-3">
                        <div class="font-body text-xs text-white/40">Penerbit</div>
                        <div class="font-display font-semibold text-white mt-1">{{ $book->publisher ?? '-' }}</div>
                    </div>
                    <div class="border-t border-white/10 pt-3">
                        <div class="font-body text-xs text-white/40">Tahun</div>
                        <div class="font-display font-semibold text-white mt-1">{{ $book->publication_year ?? '-' }}</div>
                    </div>
                    <div class="border-t border-white/10 pt-3">
                        <div class="font-body text-xs text-white/40">Stok</div>
                        <div class="font-display font-semibold text-lg mt-1 {{ $book->stock > 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                            <span x-data="countUp" data-count="{{ $book->stock }}" x-text="displayed"></span>
                        </div>
                    </div>
                    <div class="border-t border-white/10 pt-3">
                        <div class="font-body text-xs text-white/40">Dipinjam</div>
                        <div class="font-display font-semibold text-lg text-sky-300 mt-1">
                            <span x-data="countUp" data-count="{{ $activeLoans }}" x-text="displayed"></span>
                        </div>
                    </div>
                </div>

                {{-- Synopsis --}}
                @if ($book->description)
                    <div class="mt-8">
                        <h4 class="font-display font-semibold text-sm text-white mb-2">Deskripsi</h4>
                        <p class="font-body text-sm text-white/55 leading-relaxed max-w-2xl">{{ $book->description }}</p>
                    </div>
                @endif

                {{-- QR Code --}}
                <div class="mt-8 glass-inset p-5 inline-block">
                    <div class="font-body text-xs text-white/40 mb-3">QR Code Buku</div>
                    <div class="bg-white p-3 rounded-glass-sm w-fit">
                        <img src="data:image/png;base64,{{ \Milon\Barcode\Facades\DNS2DFacade::getBarcodePNG($book->isbn, 'QRCODE', 5, 5) }}" alt="QR Code {{ $book->isbn }}" class="w-28 h-28">
                    </div>
                    <p class="font-body text-xs text-white/40 mt-2 text-center font-mono">{{ $book->isbn }}</p>
                </div>

                {{-- Actions --}}
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('loans.borrow.create') }}" class="glass-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Pinjam Buku
                    </a>
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('books.edit', $book) }}" class="glass-btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Buku
                        </a>
                        <a href="{{ route('books.print-label', $book) }}" target="_blank" class="glass-btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak Label
                        </a>
                        <form action="{{ route('books.destroy', $book) }}" method="POST" @submit="confirmDelete($event, $el)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="glass-btn-danger">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
