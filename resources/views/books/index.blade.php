<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-gradient">
                    Katalog Buku
                </h2>
                <p class="font-body text-sm text-white/45 mt-1">Jelajahi dan kelola koleksi perpustakaan</p>
            </div>
            <div class="flex items-center gap-3">
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('books.create') }}" class="glass-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Buku
                    </a>
                @endif
                <a href="{{ route('books.print-label-batch') }}" target="_blank" class="glass-btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Label
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="bookCatalog(@js($booksJson), {{ Auth::user()->isAdmin() ? 'true' : 'false' }})">
        {{-- Live Filters --}}
        <div class="glass p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div class="lg:col-span-1">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Cari</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3.5 flex items-center text-white/30 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="text" x-model="query" placeholder="Cari judul, penulis, ISBN..." class="glass-input pl-10">
                    </div>
                </div>

                <div>
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Kategori</label>
                    <select x-model="kategori" class="glass-select w-full">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriList as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Status</label>
                    <select x-model="status" class="glass-select w-full">
                        <option value="">Semua Status</option>
                        <option value="available">Tersedia</option>
                        <option value="borrowed">Habis</option>
                    </select>
                </div>

                <div>
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Urutkan</label>
                    <select x-model="sort" class="glass-select w-full">
                        <option value="recent">Terbaru</option>
                        <option value="title">Judul A-Z</option>
                        <option value="author">Penulis A-Z</option>
                        <option value="year">Tahun Terbit</option>
                        <option value="stock">Stok</option>
                    </select>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <p class="font-body text-xs text-white/40">
                    <span x-text="filtered.length" class="text-white/70 font-semibold"></span> buku ditemukan
                </p>
                <p class="font-body text-xs text-white/30 hidden sm:block">Klik kartu buku untuk pratinjau cepat</p>
            </div>
        </div>

        {{-- Book Grid --}}
        <div class="grid grid-cols-[repeat(auto-fit,minmax(min(220px,100%),1fr))] gap-5">
            <template x-for="(book, index) in filtered" :key="book.id">
                <div class="glass glass-hover group overflow-hidden flex flex-col animate-card" :style="'animation-delay: ' + (index % 8 * 50) + 'ms'">
                    {{-- Cover --}}
                    <div class="relative aspect-[3/4] overflow-hidden cursor-pointer" @click="previewBook(book)">
                        <template x-if="book.cover_image">
                            <img :src="book.cover_image" :alt="book.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </template>
                        <template x-if="!book.cover_image">
                            <div class="w-full h-full flex items-center justify-center text-6xl bg-gradient-to-br from-white/[0.06] to-white/[0.02]">📖</div>
                        </template>
                        <div class="absolute inset-0 bg-gradient-to-t from-night/90 via-transparent to-transparent opacity-80"></div>

                        {{-- Badges --}}
                        <div class="absolute top-3 right-3 flex flex-col items-end gap-1.5">
                            <template x-if="book.available">
                                <span class="glass-badge-green">● Tersedia</span>
                            </template>
                            <template x-if="!book.available">
                                <span class="glass-badge-red">● Habis</span>
                            </template>
                        </div>
                        <template x-if="book.kategori">
                            <div class="absolute top-3 left-3">
                                <span class="glass-badge-violet" x-text="book.kategori"></span>
                            </div>
                        </template>

                        <div class="absolute bottom-3 left-4 right-4">
                            <span class="glass-badge" :class="book.available ? 'glass-badge-blue' : 'glass-badge-red'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span x-text="'Stok: ' + book.stock"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-display font-semibold text-base text-white leading-snug truncate cursor-pointer hover:text-sky-300 transition-colors" :title="book.title" @click="previewBook(book)">
                            <span x-text="book.title"></span>
                        </h3>
                        <p class="font-body text-sm text-white/45 mt-1 truncate" x-text="book.author"></p>

                        <div class="mt-auto pt-4 flex flex-wrap gap-2">
                            <a :href="book.url" class="flex-1 glass-btn-primary text-xs py-2">Detail</a>
                            <template x-if="isAdmin">
                                <a :href="book.edit_url" class="flex-1 glass-btn-secondary text-xs py-2">Edit</a>
                            </template>
                            <template x-if="isAdmin">
                                <form :action="book.url" method="POST" @submit="confirmDelete($event, $el)">
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="glass-btn-danger text-xs py-2" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="filtered.length === 0">
                <div class="col-span-full glass p-16 flex flex-col items-center justify-center text-center">
                    <div class="w-24 h-24 rounded-2xl bg-gradient-soft flex items-center justify-center text-4xl shadow-glow mb-5">📚</div>
                    <p class="font-display font-semibold text-lg text-white">Tidak ada buku yang cocok</p>
                    <p class="font-body text-sm text-white/40 mt-1">Coba ubah kata kunci atau filter pencarian.</p>
                </div>
            </template>
        </div>
    </div>

    {{-- Quick Preview Modal --}}
    <x-modal name="book-preview" maxWidth="2xl">
        <template x-if="$store.bookPreview.data">
            <div class="p-6 sm:p-8">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-2">
                        <template x-if="$store.bookPreview.data.kategori">
                            <span class="glass-badge-violet" x-text="$store.bookPreview.data.kategori"></span>
                        </template>
                        <span class="glass-badge" :class="$store.bookPreview.data.available ? 'glass-badge-green' : 'glass-badge-red'">
                            <span x-text="$store.bookPreview.data.available ? '● Tersedia' : '● Habis'"></span>
                        </span>
                    </div>
                    <button @click="$dispatch('close-modal', 'book-preview')" class="p-2 rounded-glass-sm text-white/40 hover:text-white hover:bg-white/[0.06] transition-colors" aria-label="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-[190px,1fr] gap-6">
                    <div>
                        <template x-if="$store.bookPreview.data.cover_image">
                            <img :src="$store.bookPreview.data.cover_image" :alt="$store.bookPreview.data.title" class="w-full rounded-glass-lg shadow-glass-lg border border-white/10">
                        </template>
                        <template x-if="!$store.bookPreview.data.cover_image">
                            <div class="aspect-[3/4] w-full rounded-glass-lg bg-white/[0.04] border border-white/10 flex items-center justify-center text-6xl">📖</div>
                        </template>
                    </div>

                    <div class="min-w-0">
                        <h3 class="font-display text-2xl font-bold text-white leading-snug" x-text="$store.bookPreview.data.title"></h3>
                        <p class="font-body text-violet-300 font-medium mt-1" x-text="$store.bookPreview.data.author"></p>

                        <div class="grid grid-cols-2 gap-4 mt-6">
                            <div class="border-t border-white/10 pt-3">
                                <div class="font-body text-xs text-white/40">ISBN</div>
                                <div class="font-display font-semibold text-white mt-1 font-mono text-sm" x-text="$store.bookPreview.data.isbn || '-'"></div>
                            </div>
                            <div class="border-t border-white/10 pt-3">
                                <div class="font-body text-xs text-white/40">Penerbit</div>
                                <div class="font-display font-semibold text-white mt-1 truncate" x-text="$store.bookPreview.data.publisher || '-'"></div>
                            </div>
                            <div class="border-t border-white/10 pt-3">
                                <div class="font-body text-xs text-white/40">Tahun</div>
                                <div class="font-display font-semibold text-white mt-1" x-text="$store.bookPreview.data.publication_year || '-'"></div>
                            </div>
                            <div class="border-t border-white/10 pt-3">
                                <div class="font-body text-xs text-white/40">Stok</div>
                                <div class="font-display font-semibold text-lg mt-1" :class="$store.bookPreview.data.available ? 'text-emerald-300' : 'text-rose-300'">
                                    <span x-text="$store.bookPreview.data.stock"></span>
                                </div>
                            </div>
                        </div>

                        <template x-if="$store.bookPreview.data.description">
                            <div class="mt-6">
                                <h4 class="font-display font-semibold text-sm text-white mb-2">Deskripsi</h4>
                                <p class="font-body text-sm text-white/55 leading-relaxed" x-text="$store.bookPreview.data.description"></p>
                            </div>
                        </template>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a :href="$store.bookPreview.data.url" class="glass-btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Detail Lengkap
                            </a>
                            <a :href="$store.bookPreview.data.borrow_url" class="glass-btn-secondary" :class="!$store.bookPreview.data.available ? 'opacity-50 pointer-events-none' : ''">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Pinjam Buku
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </x-modal>
</x-app-layout>
