<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-heading-lg text-text leading-tight">
            Edit Buku
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-apple-lg">

            <div class="mb-6">
                <a href="{{ route('books.show', $book) }}" class="apple-btn-secondary inline-flex items-center gap-2 text-xs">
                    ← Kembali
                </a>
            </div>

            <div class="bg-white rounded-apple-lg p-6">
                <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="isbn" class="block font-display text-xs text-text mb-1">ISBN</label>
                            <input id="isbn" name="isbn" type="text" :value="old('isbn', $book->isbn)" required class="apple-input">
                            @error('isbn') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="title" class="block font-display text-xs text-text mb-1">Judul</label>
                            <input id="title" name="title" type="text" :value="old('title', $book->title)" required class="apple-input">
                            @error('title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="author" class="block font-display text-xs text-text mb-1">Penulis</label>
                            <input id="author" name="author" type="text" :value="old('author', $book->author)" required class="apple-input">
                            @error('author') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="publisher" class="block font-display text-xs text-text mb-1">Penerbit</label>
                            <input id="publisher" name="publisher" type="text" :value="old('publisher', $book->publisher)" class="apple-input">
                            @error('publisher') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="publication_year" class="block font-display text-xs text-text mb-1">Tahun Terbit</label>
                            <input id="publication_year" name="publication_year" type="number" :value="old('publication_year', $book->publication_year)" min="1000" max="9999" class="apple-input">
                            @error('publication_year') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="stock" class="block font-display text-xs text-text mb-1">Stok</label>
                            <input id="stock" name="stock" type="number" :value="old('stock', $book->stock)" min="0" required class="apple-input">
                            @error('stock') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="kategori" class="block font-display text-xs text-text mb-1">Kategori</label>
                            <select id="kategori" name="kategori" class="apple-input">
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoriList as $key => $label)
                                    <option value="{{ $key }}" {{ old('kategori', $book->kategori) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('kategori') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="description" class="block font-display text-xs text-text mb-1">Deskripsi</label>
                            <textarea id="description" name="description" rows="4" class="apple-input">{{ old('description', $book->description) }}</textarea>
                            @error('description') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="cover_image" class="block font-display text-xs text-text mb-1">Sampul Buku</label>
                            @if ($book->cover_image)
                                <div class="mb-3">
                                    <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="h-40 w-28 object-cover rounded-apple-lg shadow-apple">
                                </div>
                            @endif
                            <input type="file" id="cover_image" name="cover_image" accept="image/*" class="apple-input file:mr-4 file:py-2 file:px-4 file:rounded-apple-md file:border-0 file:font-display file:text-xs file:bg-surface-light file:hover:bg-surface-lighter file:cursor-pointer">
                            @error('cover_image') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8 border-t border-surface-lighter pt-6">
                        <a href="{{ route('books.show', $book) }}">
                            <button type="button" class="apple-btn-secondary">Batal</button>
                        </a>
                        <button type="submit" class="apple-btn-primary">Perbarui</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
