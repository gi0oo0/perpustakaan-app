<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            Detail Buku
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('books.index') }}" class="neo-btn-secondary inline-flex items-center gap-2 text-xs">
                    ← Kembali
                </a>
            </div>

            {{-- Book Detail Split --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-0 bg-white border-3 border-border shadow-neo overflow-hidden">

                {{-- Left: Cover --}}
                <div class="lg:col-span-2 bg-gray-50 border-b-3 lg:border-b-0 lg:border-r-3 border-border p-8 flex items-center justify-center">
                    @if ($book->cover_image)
                        <div class="relative group">
                            <div class="absolute inset-0 bg-primary opacity-10 blur-xl group-hover:opacity-20 transition-all duration-300"></div>
                            <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}"
                                 class="relative w-full max-w-xs border-4 border-white border-3 border-border shadow-neo-hover group-hover:rotate-2 transition-transform duration-300">
                        </div>
                    @else
                        <div class="w-full max-w-xs h-80 bg-gray-200 border-3 border-border shadow-neo flex items-center justify-center text-6xl">
                            📖
                        </div>
                    @endif
                </div>

                {{-- Right: Details --}}
                <div class="lg:col-span-3 p-8">
                    <h1 class="font-heading font-bold text-3xl lg:text-4xl text-border leading-none tracking-tight">
                        {{ $book->title }}
                    </h1>
                    <p class="font-body text-lg text-primary font-semibold mt-2">{{ $book->author }}</p>

                    {{-- Metadata Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-8">
                        <div class="border-t-3 border-border pt-3">
                            <div class="font-heading text-xs uppercase tracking-wide text-muted">ISBN</div>
                            <div class="font-body font-semibold text-border mt-1">{{ $book->isbn }}</div>
                        </div>
                        <div class="border-t-3 border-border pt-3">
                            <div class="font-heading text-xs uppercase tracking-wide text-muted">Penerbit</div>
                            <div class="font-body font-semibold text-border mt-1">{{ $book->publisher ?? '-' }}</div>
                        </div>
                        <div class="border-t-3 border-border pt-3">
                            <div class="font-heading text-xs uppercase tracking-wide text-muted">Tahun</div>
                            <div class="font-body font-semibold text-border mt-1">{{ $book->publication_year ?? '-' }}</div>
                        </div>
                        <div class="border-t-3 border-border pt-3">
                            <div class="font-heading text-xs uppercase tracking-wide text-muted">Stok</div>
                            <div class="font-body font-bold text-lg mt-1 {{ $book->stock > 0 ? 'text-primary' : 'text-coral' }}">{{ $book->stock }}</div>
                        </div>
                        <div class="border-t-3 border-border pt-3">
                            <div class="font-heading text-xs uppercase tracking-wide text-muted">Dipinjam</div>
                            <div class="font-body font-bold text-lg text-border mt-1">{{ $activeLoans }}</div>
                        </div>
                    </div>

                    {{-- Synopsis --}}
                    @if ($book->description)
                        <div class="mt-8">
                            <h4 class="font-heading font-semibold text-sm text-border uppercase tracking-wide mb-2">Deskripsi</h4>
                            <p class="font-body text-base text-muted leading-relaxed max-w-2xl">{{ $book->description }}</p>
                        </div>
                    @endif

                    {{-- QR Code --}}
                    <div class="mt-8 bg-lemon border-3 border-border shadow-neo-sm p-4 inline-block">
                        <div class="font-heading text-xs uppercase tracking-wide text-border mb-2">QR Code</div>
                        <img src="data:image/png;base64,{{ \Milon\Barcode\Facades\DNS2DFacade::getBarcodePNG($book->isbn, 'QRCODE', 5, 5) }}" alt="QR Code {{ $book->isbn }}" class="w-32 h-32">
                        <p class="font-body text-xs text-muted mt-1 text-center">{{ $book->isbn }}</p>
                    </div>

                    {{-- Actions - Admin Only --}}
                    @if (Auth::user()->isAdmin())
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('books.edit', $book) }}">
                                <button type="button" class="neo-btn-primary">Edit Buku</button>
                            </a>
                            <a href="{{ route('books.print-label', $book) }}" target="_blank">
                                <button type="button" class="neo-btn-secondary">🖨 Cetak Label</button>
                            </a>
                            <form action="{{ route('books.destroy', $book) }}" method="POST" class="delete-form-detail">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="neo-btn-danger">Hapus</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-form-detail').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Data buku yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FF6B6B',
                    cancelButtonColor: '#111827',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
