<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-gradient">Tambah Buku</h2>
                <p class="font-body text-sm text-white/45 mt-1">Masukkan data buku baru ke koleksi</p>
            </div>
            <a href="{{ route('books.index') }}" class="glass-btn-secondary text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto" x-data="reveal">
        <div class="glass p-6 sm:p-8">
            <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="isbn" class="block font-body text-xs font-medium text-white/70 mb-2">ISBN</label>
                        <input id="isbn" name="isbn" type="text" :value="old('isbn')" required autofocus class="glass-input font-mono" placeholder="978-xxx-xxx-xxx">
                        @error('isbn') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="title" class="block font-body text-xs font-medium text-white/70 mb-2">Judul</label>
                        <input id="title" name="title" type="text" :value="old('title')" required class="glass-input" placeholder="Judul buku">
                        @error('title') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="author" class="block font-body text-xs font-medium text-white/70 mb-2">Penulis</label>
                        <input id="author" name="author" type="text" :value="old('author')" required class="glass-input" placeholder="Nama penulis">
                        @error('author') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="publisher" class="block font-body text-xs font-medium text-white/70 mb-2">Penerbit</label>
                        <input id="publisher" name="publisher" type="text" :value="old('publisher')" class="glass-input" placeholder="Nama penerbit">
                        @error('publisher') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="publication_year" class="block font-body text-xs font-medium text-white/70 mb-2">Tahun Terbit</label>
                        <input id="publication_year" name="publication_year" type="number" :value="old('publication_year')" min="1000" max="9999" class="glass-input" placeholder="2024">
                        @error('publication_year') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="stock" class="block font-body text-xs font-medium text-white/70 mb-2">Stok</label>
                        <input id="stock" name="stock" type="number" :value="old('stock', 0)" min="0" required class="glass-input" placeholder="0">
                        @error('stock') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="kategori" class="block font-body text-xs font-medium text-white/70 mb-2">Kategori</label>
                        <select id="kategori" name="kategori" class="glass-select w-full">
                            <option value="">Pilih Kategori</option>
                            @foreach ($kategoriList as $key => $label)
                                <option value="{{ $key }}" {{ old('kategori') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('kategori') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block font-body text-xs font-medium text-white/70 mb-2">Deskripsi</label>
                        <textarea id="description" name="description" rows="4" class="glass-input resize-y" placeholder="Deskripsi singkat buku...">{{ old('description') }}</textarea>
                        @error('description') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="cover_image" class="block font-body text-xs font-medium text-white/70 mb-2">Sampul Buku</label>
                        <div x-data="filePicker" class="relative">
                            <input type="file" id="cover_image" name="cover_image" accept="image/*"
                                   @change="onPick($event)"
                                   class="glass-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-white/10 file:text-white file:hover:bg-white/15 file:cursor-pointer file:transition-colors cursor-pointer">
                            <template x-if="fileName">
                                <p class="mt-2 font-body text-xs text-emerald-300" x-text="'✓ ' + fileName"></p>
                            </template>
                        </div>
                        @error('cover_image') <p class="font-body text-xs text-rose-300 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-white/10">
                    <a href="{{ route('books.index') }}" class="glass-btn-secondary">Batal</a>
                    <button type="submit" class="glass-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Buku
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
