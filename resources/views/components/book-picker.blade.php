<div {{ $attributes->merge(['class' => 'glass p-4 sm:p-5']) }}>
    <div class="flex items-start justify-between gap-2 mb-3">
        <div>
            <h3 class="font-display font-semibold text-[15px] text-white">Buku Tersedia</h3>
            <p class="text-xs text-white/45 mt-0.5">Pilih buku yang ingin dipinjam.</p>
        </div>
        <span class="glass-badge-violet flex-shrink-0" x-text="filteredBooks.length + ' buku'"></span>
    </div>

    <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/35 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" x-model="bookQuery" placeholder="Cari judul, penulis, ISBN..." class="search-input pl-10" autocomplete="off">
    </div>

    <select x-model="bookKategori" class="glass-select mt-2 h-10 text-sm">
        <option value="">Semua Kategori</option>
        <template x-for="k in categories" :key="k">
            <option :value="k" x-text="k"></option>
        </template>
    </select>

    <div class="mt-3 space-y-2 max-h-[520px] overflow-y-auto pr-1">
        <template x-for="b in filteredBooks" :key="b.isbn">
            <button type="button" @click="setBook(b)" :disabled="submitting"
                    class="w-full flex items-center gap-3 p-2.5 rounded-[12px] border text-left transition-all duration-150 group disabled:opacity-60 disabled:pointer-events-none"
                    :class="book && book.isbn === b.isbn
                        ? 'border-primary bg-primary/[0.08]'
                        : 'border-white/[0.07] bg-white/[0.03] hover:bg-white/[0.06] hover:border-white/15'">
                <img :src="b.cover" :alt="b.title" loading="lazy" class="w-10 h-[52px] rounded-md object-cover border border-white/10 flex-shrink-0">
                <span class="flex-1 min-w-0">
                    <span class="block font-display font-medium text-[13px] text-white truncate" x-text="b.title"></span>
                    <span class="block text-xs text-white/40 truncate mt-0.5" x-text="b.author"></span>
                    <span class="block text-[11px] text-white/35 mt-0.5">Stok: <span x-text="b.stock"></span></span>
                </span>
                <span class="flex-shrink-0 text-[11px] font-medium px-2.5 py-1 rounded-full border transition-colors"
                      :class="book && book.isbn === b.isbn
                          ? 'border-primary bg-primary text-white'
                          : 'border-white/15 text-white/55 group-hover:text-white group-hover:border-white/25'">
                    <span x-show="book && book.isbn === b.isbn" class="inline-flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        Dipilih
                    </span>
                    <span x-show="!(book && book.isbn === b.isbn)">Pilih</span>
                </span>
            </button>
        </template>

        <div x-show="!filteredBooks.length" x-cloak class="text-center py-8 px-4">
            <p class="font-display text-sm text-white/60 mb-1">Buku tidak ditemukan</p>
            <p class="text-xs text-white/40">Coba kata kunci atau kategori lain.</p>
        </div>
    </div>
</div>
