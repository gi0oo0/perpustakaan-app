<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            ✏ Edit Buku
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('books.show', $book) }}" class="neo-btn-secondary inline-flex items-center gap-2 text-xs">
                    ← Kembali
                </a>
            </div>

            <div class="neo-card">
                <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="isbn" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">ISBN</label>
                            <input id="isbn" name="isbn" type="text" :value="old('isbn', $book->isbn)" required class="neo-input">
                            @error('isbn') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="title" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Judul</label>
                            <input id="title" name="title" type="text" :value="old('title', $book->title)" required class="neo-input">
                            @error('title') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="author" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Penulis</label>
                            <input id="author" name="author" type="text" :value="old('author', $book->author)" required class="neo-input">
                            @error('author') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="publisher" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Penerbit</label>
                            <input id="publisher" name="publisher" type="text" :value="old('publisher', $book->publisher)" class="neo-input">
                            @error('publisher') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="publication_year" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Tahun Terbit</label>
                            <input id="publication_year" name="publication_year" type="number" :value="old('publication_year', $book->publication_year)" min="1000" max="9999" class="neo-input">
                            @error('publication_year') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="stock" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Stok</label>
                            <input id="stock" name="stock" type="number" :value="old('stock', $book->stock)" min="0" required class="neo-input">
                            @error('stock') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="kategori" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Kategori</label>
                            <select id="kategori" name="kategori" class="neo-input">
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoriList as $key => $label)
                                    <option value="{{ $key }}" {{ old('kategori', $book->kategori) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('kategori') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Deskripsi</label>
                            <textarea id="description" name="description" rows="4" class="neo-input">{{ old('description', $book->description) }}</textarea>
                            @error('description') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="cover_image" class="block font-heading font-semibold text-xs text-border uppercase tracking-wide mb-1">Sampul Buku</label>
                            @if ($book->cover_image)
                                <div class="mb-3">
                                    <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="h-40 w-28 object-cover border-3 border-border shadow-neo-sm">
                                </div>
                            @endif
                            <input type="file" id="cover_image" name="cover_image" accept="image/*" class="neo-input file:mr-4 file:py-2 file:px-4 file:border-3 file:border-border file:font-heading file:font-semibold file:text-xs file:uppercase file:bg-lemon file:hover:bg-lemon-100 file:cursor-pointer">
                            @error('cover_image') <p class="font-body text-xs text-coral mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8 border-t-3 border-border pt-6">
                        <a href="{{ route('books.show', $book) }}">
                            <button type="button" class="neo-btn-secondary">Batal</button>
                        </a>
                        <button type="submit" class="neo-btn-primary">✓ Perbarui</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
