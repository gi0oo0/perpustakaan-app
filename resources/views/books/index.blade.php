<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-2xl text-text leading-tight">
            Daftar Buku
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-apple-lg">

            @if (session('success'))
                <div class="mb-6 bg-apple-blue text-white px-6 py-4 font-display text-sm rounded-apple-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white rounded-apple-lg p-6 mb-6">
                <form method="GET" action="{{ route('books.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, ISBN..."
                        class="apple-input flex-1">
                    <select name="kategori" class="apple-input w-full sm:w-48">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriList as $key => $label)
                            <option value="{{ $key }}" {{ request('kategori') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="apple-input w-full sm:w-48">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Habis</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="submit" class="apple-btn-primary text-xs">Cari</button>
                        <a href="{{ route('books.index') }}">
                            <button type="button" class="apple-btn-secondary text-xs">Reset</button>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 mb-6">
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('books.create') }}">
                        <button type="button" class="apple-btn-primary">+ Tambah Buku</button>
                    </a>
                @endif
                <a href="{{ route('books.print-label-batch') }}" target="_blank">
                    <button type="button" class="apple-btn-secondary">Cetak Label</button>
                </a>
            </div>

            {{-- Book Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse ($books as $book)
                    <div class="bg-white rounded-apple-lg overflow-hidden shadow-apple hover:shadow-apple-lg transition-shadow duration-200">
                        {{-- Cover --}}
                        <div class="relative h-48 bg-surface-light overflow-hidden">
                            @if ($book->cover_image)
                                <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-5xl bg-surface-lighter">📖</div>
                            @endif
                            {{-- Badges --}}
                            <div class="absolute top-2 right-2">
                                @if ($book->stock > 0)
                                    <span class="apple-badge apple-badge-green text-xs">Tersedia</span>
                                @else
                                    <span class="apple-badge apple-badge-red text-xs">Habis</span>
                                @endif
                            </div>
                            @if ($book->kategori)
                                <div class="absolute top-2 left-2">
                                    <span class="apple-badge text-xs">{{ $book->kategori }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <h3 class="font-display font-semibold text-base text-text leading-tight truncate" title="{{ $book->title }}">
                                {{ $book->title }}
                            </h3>
                            <p class="text-sm text-text-tertiary mt-1 truncate">{{ $book->author }}</p>

                            <div class="mt-2 flex items-center justify-between">
                                <span class="apple-badge {{ $book->stock > 0 ? 'apple-badge-blue' : 'apple-badge-red' }} text-xs">
                                    Stok: {{ $book->stock }}
                                </span>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('books.show', $book) }}" class="flex-1">
                                    <button type="button" class="apple-btn-primary w-full text-xs py-2">Detail</button>
                                </a>
                                @if (Auth::user()->isAdmin())
                                    <a href="{{ route('books.edit', $book) }}" class="flex-1">
                                        <button type="button" class="apple-btn-secondary w-full text-xs py-2">Edit</button>
                                    </a>
                                @endif
                            </div>

                            @if (Auth::user()->isAdmin())
                                <div class="mt-2 flex gap-2">
                                    <a href="{{ route('books.print-label', $book) }}" target="_blank" class="flex-1">
                                        <button type="button" class="apple-btn-secondary w-full text-xs py-2">Cetak</button>
                                    </a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" class="flex-1 delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="apple-btn-danger w-full text-xs py-2">Hapus</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div class="col-span-full flex flex-col items-center justify-center py-16">
                        <div class="w-24 h-24 bg-surface-light rounded-full flex items-center justify-center text-4xl mb-4">📚</div>
                        <p class="font-display font-semibold text-lg text-text">Belum ada buku</p>
                        <p class="text-sm text-text-tertiary mt-1">Tambahkan buku baru untuk memulai.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $books->links() }}
            </div>

            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Data buku yang dihapus tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FF3B30',
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
