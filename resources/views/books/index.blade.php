<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            Daftar Buku
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-primary text-white border-3 border-border shadow-neo px-6 py-4 font-heading font-semibold uppercase tracking-wide text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="neo-card mb-6">
                <form method="GET" action="{{ route('books.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari judul, penulis, ISBN..."
                        class="neo-input flex-1">
                    <select name="kategori" class="neo-input w-full sm:w-48">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriList as $key => $label)
                            <option value="{{ $key }}" {{ request('kategori') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="neo-input w-full sm:w-48">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Habis</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="submit" class="neo-btn-primary text-xs">Cari</button>
                        <a href="{{ route('books.index') }}">
                            <button type="button" class="neo-btn-secondary text-xs">Reset</button>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 mb-6">
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('books.create') }}">
                        <button type="button" class="neo-btn-primary">+ Tambah Buku</button>
                    </a>
                @endif
                <a href="{{ route('books.print-label-batch') }}" target="_blank">
                    <button type="button" class="neo-btn-secondary">🖨 Cetak Label</button>
                </a>
            </div>

            {{-- Book Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse ($books as $book)
                    <div class="book-card bg-white border-3 border-border shadow-neo overflow-hidden">
                        {{-- Cover --}}
                        <div class="relative h-48 bg-gray-100 border-b-3 border-border overflow-hidden">
                            @if ($book->cover_image)
                                <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-5xl bg-lem50">📖</div>
                            @endif
                            {{-- Badges --}}
                            <div class="absolute top-2 right-2">
                                @if ($book->stock > 0)
                                    <span class="neo-badge bg-primary text-white">Tersedia</span>
                                @else
                                    <span class="neo-badge bg-coral text-white">Habis</span>
                                @endif
                            </div>
                            @if ($book->kategori)
                                <div class="absolute top-2 left-2">
                                    <span class="neo-badge bg-lemon text-border text-xs">{{ $book->kategori }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-4">
                            <h3 class="font-heading font-bold text-base text-border leading-tight truncate" title="{{ $book->title }}">
                                {{ $book->title }}
                            </h3>
                            <p class="font-body text-sm text-muted mt-1 truncate">{{ $book->author }}</p>

                            <div class="mt-2 flex items-center justify-between">
                                <span class="neo-badge {{ $book->stock > 0 ? 'bg-primary-50 text-primary border-primary' : 'bg-red-50 text-coral border-coral' }} text-xs">
                                    Stok: {{ $book->stock }}
                                </span>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('books.show', $book) }}" class="flex-1">
                                    <button type="button" class="neo-btn-primary w-full text-xs py-2">Detail</button>
                                </a>
                                @if (Auth::user()->isAdmin())
                                    <a href="{{ route('books.edit', $book) }}" class="flex-1">
                                        <button type="button" class="neo-btn-secondary w-full text-xs py-2">Edit</button>
                                    </a>
                                @endif
                            </div>

                            @if (Auth::user()->isAdmin())
                                <div class="mt-2 flex gap-2">
                                    <a href="{{ route('books.print-label', $book) }}" target="_blank" class="flex-1">
                                        <button type="button" class="neo-btn-secondary w-full text-xs py-2">🖨 Cetak</button>
                                    </a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" class="flex-1 delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="neo-btn-danger w-full text-xs py-2">Hapus</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div class="col-span-full flex flex-col items-center justify-center py-16">
                        <div class="w-24 h-24 bg-lemon border-3 border-border shadow-neo rounded-full flex items-center justify-center text-4xl mb-4">📚</div>
                        <p class="font-heading font-semibold text-lg text-border">Belum ada buku</p>
                        <p class="font-body text-sm text-muted mt-1">Tambahkan buku baru untuk memulai.</p>
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
