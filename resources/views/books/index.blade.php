<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-gradient">
                    Katalog Buku
                </h2>
                <p class="font-body text-sm text-white/45 mt-1">Jelajahi dan kelola koleksi perpustakaan</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('books.import') }}" class="glass-btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Import CSV
                    </a>
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
        @php
            $kategoriOptions = array_merge(
                [['value' => '', 'label' => 'Semua Kategori']],
                collect($kategoriList)->map(fn ($label, $key) => ['value' => $key, 'label' => $label])->values()->all()
            );
        @endphp
        <div class="glass p-5 relative z-20">
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

                <div @selectbox:change="kategori = $event.detail">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Kategori</label>
                    <x-select-box :options="$kategoriOptions" placeholder="Pilih Kategori" />
                </div>

                <div @selectbox:change="status = $event.detail">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Status</label>
                    <x-select-box :options="[
                        ['value' => '', 'label' => 'Semua Status'],
                        ['value' => 'available', 'label' => 'Tersedia'],
                        ['value' => 'borrowed', 'label' => 'Habis'],
                    ]" placeholder="Pilih Status" />
                </div>

                <div @selectbox:change="sort = $event.detail">
                    <label class="block font-body text-xs font-medium text-white/50 mb-2">Urutkan</label>
                    <x-select-box :options="[
                        ['value' => 'recent', 'label' => 'Terbaru'],
                        ['value' => 'title', 'label' => 'Judul A-Z'],
                        ['value' => 'author', 'label' => 'Penulis A-Z'],
                        ['value' => 'year', 'label' => 'Tahun Terbit'],
                        ['value' => 'stock', 'label' => 'Stok'],
                    ]" placeholder="Urutkan" />
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
        <div class="grid grid-cols-[repeat(auto-fill,minmax(min(210px,100%),1fr))] gap-5">
            <template x-for="(book, index) in filtered" :key="book.id">
                <div class="glass glass-hover group overflow-hidden flex flex-col animate-card" :style="'animation-delay: ' + (index % 8 * 50) + 'ms'">
                    {{-- Cover --}}
                    <div class="relative aspect-[3/4] overflow-hidden cursor-pointer" @click="previewBook(book)" :style="!book.cover_image ? 'background-color: ' + coverColor(book) : ''">
                        <template x-if="book.cover_image">
                            <img :src="book.cover_image" :alt="book.title" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!book.cover_image">
                            <div class="cover absolute inset-0 flex flex-col">
                                <div class="px-4 pt-4 flex items-start justify-between gap-2">
                                    <template x-if="book.kategori">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white/15 border border-white/25" x-text="book.kategori"></span>
                                    </template>
                                    <span class="ml-auto px-2 py-0.5 rounded-md text-[10px] font-semibold" :class="book.available ? 'bg-white/20' : 'bg-black/30'">
                                        <span x-text="book.available ? 'Tersedia' : 'Habis'"></span>
                                    </span>
                                </div>
                                <div class="mt-auto px-5 pb-2 text-center">
                                    <p class="text-[9px] uppercase tracking-[0.22em] opacity-50 mb-2">Perpustakaan Sekolah</p>
                                    <h4 class="font-semibold leading-snug text-center"
                                        style="font-size:clamp(16px,1.4vw,19px);display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;overflow-wrap:break-word;word-break:normal;text-align:center"
                                        x-text="book.title"></h4>
                                    <p class="text-[11px] mt-2 opacity-80 truncate" x-text="book.author"></p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-white/25 px-4 pb-4 flex items-center justify-between">
                                    <span class="text-[10px] font-medium opacity-90">Stok: <span x-text="book.stock"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Info --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-display font-semibold text-base text-white leading-snug truncate cursor-pointer hover:text-primary transition-colors" :title="book.title" @click="previewBook(book)">
                            <span x-text="book.title"></span>
                        </h3>
                        <p class="font-body text-sm text-white/50 mt-1 truncate" x-text="book.author"></p>

                        <div class="mt-3">
                            <span class="glass-badge" :class="book.available ? 'glass-badge-green' : 'glass-badge-red'">
                                <span x-text="'Stok: ' + book.stock"></span>
                            </span>
                        </div>

                        <div class="mt-auto pt-4 flex flex-wrap gap-2">
                            <a :href="book.url" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-[#2DB7A8] bg-[#2DB7A8]/10 border border-[#2DB7A8]/20 hover:bg-[#2DB7A8]/15 transition-colors">Detail</a>
                            <template x-if="isAdmin">
                                <a :href="book.edit_url" class="flex-1 glass-btn-secondary text-xs py-2">Edit</a>
                            </template>
                            <template x-if="isAdmin">
                                <form :action="book.url" method="POST" @submit="confirmDelete($event, $el)">
                                    @csrf
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="glass-btn-danger-soft rounded-lg p-2.5" title="Hapus">
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
                <div class="col-span-full glass p-12 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 rounded-full bg-primary/10 border border-primary/15 flex items-center justify-center mb-5">
                        <svg class="w-9 h-9 text-primary-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <p class="font-display font-semibold text-lg text-white">Tidak ada buku yang cocok</p>
                    <p class="font-body text-sm text-white/40 mt-1">Coba ubah kata kunci atau filter yang digunakan.</p>
                    <button @click="resetFilters" class="glass-btn-primary mt-5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset Filter
                    </button>
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
                            <span x-text="$store.bookPreview.data.available ? 'Tersedia' : 'Habis'"></span>
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
                            <div class="aspect-[3/4] w-full rounded-glass-lg border border-white/10 p-4 flex flex-col justify-end overflow-hidden" :style="'background-color: ' + $store.bookPreview.coverColor($store.bookPreview.data)">
                                <div class="cover text-center">
                                    <p class="text-[9px] uppercase tracking-[0.22em] opacity-50 mb-2">Perpustakaan Sekolah</p>
                                    <p class="font-semibold leading-snug text-center" style="font-size:18px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;overflow-wrap:break-word;word-break:normal" x-text="$store.bookPreview.data.title"></p>
                                    <p class="text-[11px] mt-2 opacity-80 truncate" x-text="$store.bookPreview.data.author"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="min-w-0">
                        <h3 class="font-display text-xl sm:text-2xl font-bold text-white leading-snug" x-text="$store.bookPreview.data.title"></h3>
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
