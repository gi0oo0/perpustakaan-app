<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-2xl text-text leading-tight">
            Detail Buku
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-apple-lg">

            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('books.index') }}" class="apple-btn-secondary inline-flex items-center gap-2 text-xs">
                    ← Kembali
                </a>
            </div>

            {{-- Book Detail Split --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-0 bg-white rounded-apple-lg overflow-hidden shadow-apple">

                {{-- Left: Cover --}}
                <div class="lg:col-span-2 bg-surface-light border-b lg:border-b-0 lg:border-r border-surface-lighter p-8 flex items-center justify-center">
                    @if ($book->cover_image)
                        <div class="relative group">
                            <div class="absolute inset-0 bg-apple-blue opacity-5 blur-xl group-hover:opacity-10 transition-all duration-300"></div>
                            <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}"
                                 class="relative w-full max-w-xs rounded-apple-lg shadow-apple group-hover:scale-[1.02] transition-transform duration-300">
                        </div>
                    @else
                        <div class="w-full max-w-xs h-80 bg-surface-lighter rounded-apple-lg flex items-center justify-center text-6xl">
                            📖
                        </div>
                    @endif
                </div>

                {{-- Right: Details --}}
                <div class="lg:col-span-3 p-8">
                    <h1 class="font-display font-semibold text-3xl lg:text-4xl text-text leading-none tracking-tight">
                        {{ $book->title }}
                    </h1>
                    <p class="text-lg text-apple-blue font-semibold mt-2">{{ $book->author }}</p>

                    {{-- Metadata Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-8">
                        <div class="border-t border-surface-lighter pt-3">
                            <div class="text-xs text-text-tertiary">ISBN</div>
                            <div class="font-display font-semibold text-text mt-1">{{ $book->isbn }}</div>
                        </div>
                        <div class="border-t border-surface-lighter pt-3">
                            <div class="text-xs text-text-tertiary">Penerbit</div>
                            <div class="font-display font-semibold text-text mt-1">{{ $book->publisher ?? '-' }}</div>
                        </div>
                        <div class="border-t border-surface-lighter pt-3">
                            <div class="text-xs text-text-tertiary">Tahun</div>
                            <div class="font-display font-semibold text-text mt-1">{{ $book->publication_year ?? '-' }}</div>
                        </div>
                        <div class="border-t border-surface-lighter pt-3">
                            <div class="text-xs text-text-tertiary">Stok</div>
                            <div class="font-display font-semibold text-lg mt-1 {{ $book->stock > 0 ? 'text-apple-blue' : 'text-danger' }}">{{ $book->stock }}</div>
                        </div>
                        <div class="border-t border-surface-lighter pt-3">
                            <div class="text-xs text-text-tertiary">Dipinjam</div>
                            <div class="font-display font-semibold text-lg text-text mt-1">{{ $activeLoans }}</div>
                        </div>
                    </div>

                    {{-- Synopsis --}}
                    @if ($book->description)
                        <div class="mt-8">
                            <h4 class="font-display text-sm text-text mb-2">Deskripsi</h4>
                            <p class="text-base text-text-tertiary leading-relaxed max-w-2xl">{{ $book->description }}</p>
                        </div>
                    @endif

                    {{-- QR Code --}}
                    <div class="mt-8 bg-surface-light p-4 inline-block rounded-apple-lg">
                        <div class="text-xs text-text-tertiary mb-2">QR Code</div>
                        <img src="data:image/png;base64,{{ \Milon\Barcode\Facades\DNS2DFacade::getBarcodePNG($book->isbn, 'QRCODE', 5, 5) }}" alt="QR Code {{ $book->isbn }}" class="w-32 h-32">
                        <p class="text-xs text-text-tertiary mt-1 text-center">{{ $book->isbn }}</p>
                    </div>

                    {{-- Actions - Admin Only --}}
                    @if (Auth::user()->isAdmin())
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('books.edit', $book) }}">
                                <button type="button" class="apple-btn-primary">Edit Buku</button>
                            </a>
                            <a href="{{ route('books.print-label', $book) }}" target="_blank">
                                <button type="button" class="apple-btn-secondary">Cetak Label</button>
                            </a>
                            <form action="{{ route('books.destroy', $book) }}" method="POST" class="delete-form-detail">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="apple-btn-danger">Hapus</button>
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
                    confirmButtonColor: '#E5484D',
                    cancelButtonColor: '#6E6E73',
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
