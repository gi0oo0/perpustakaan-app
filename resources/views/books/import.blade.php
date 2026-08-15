<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-[25px] font-semibold tracking-tight text-white leading-tight">Import Buku via CSV</h2>
                <p class="font-body text-[13px] text-[#747C82] mt-1">Tambahkan banyak buku sekaligus dari file CSV</p>
            </div>
            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 h-[38px] px-4 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#A5ADB3] text-sm font-medium hover:bg-[#252A2E] hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <style>
        html.dark .import-table thead th {
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
            color: #747C82;
            letter-spacing: 0.05em;
        }
        html.dark .import-table tbody td {
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }
        html.dark .import-table .glass-badge-red,
        html.dark .import-table .glass-badge-yellow {
            font-size: 10px;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    </style>

    <div class="max-w-[1100px] mx-auto space-y-4" x-data="reveal">

        @if (session('error'))
            <div class="flex items-start gap-3 rounded-[10px] border border-rose-400/20 bg-rose-500/10 px-4 py-3 font-body text-sm text-[#E76B73]">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ===== SEBELUM IMPORT ===== --}}
        <div class="glass rounded-[12px] p-5">
            <div class="flex items-start gap-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-[9px] bg-[#173B36] border border-[#35B8A5]/25 flex-shrink-0">
                    <svg class="w-4 h-4 text-[#35B8A5]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <h3 class="font-display text-[15px] font-semibold text-white">Sebelum Import</h3>
                    <p class="font-body text-[13px] text-[#A5ADB3] mt-0.5">Pastikan file CSV memenuhi format berikut.</p>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-white/[0.05] grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex gap-3">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-[#173B36] text-[#35B8A5] text-[10px] font-bold flex-shrink-0 mt-0.5">01</span>
                    <div class="min-w-0">
                        <p class="font-display text-[13px] font-semibold text-white">Siapkan CSV</p>
                        <p class="font-body text-xs text-[#747C82] mt-1 leading-relaxed">Isi data buku di Excel/Google Sheets, lalu simpan sebagai CSV.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-[#173B36] text-[#35B8A5] text-[10px] font-bold flex-shrink-0 mt-0.5">02</span>
                    <div class="min-w-0">
                        <p class="font-display text-[13px] font-semibold text-white">Pastikan kolom sesuai</p>
                        <p class="font-body text-xs text-[#747C82] mt-1 leading-relaxed">Baris pertama judul kolom. Kolom <span class="text-white/70 font-medium">isbn</span>, <span class="text-white/70 font-medium">judul</span>, <span class="text-white/70 font-medium">penulis</span> wajib.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-[#173B36] text-[#35B8A5] text-[10px] font-bold flex-shrink-0 mt-0.5">03</span>
                    <div class="min-w-0">
                        <p class="font-display text-[13px] font-semibold text-white">Upload &amp; Import</p>
                        <p class="font-body text-xs text-[#747C82] mt-1 leading-relaxed">Pilih file di bawah, klik <span class="text-white/70 font-medium">Import Buku</span>. Hasil langsung tampil.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== STRUKTUR KOLOM ===== --}}
        <div class="glass rounded-[12px] p-5">
            <h3 class="font-display text-[15px] font-semibold text-white">Struktur Kolom CSV</h3>
            <p class="font-body text-[13px] text-[#A5ADB3] mt-0.5 mb-4">Kolom yang wajib dan opsional pada file CSV.</p>

            <div class="overflow-x-auto rounded-[10px] border border-white/[0.06]">
                <table class="glass-table import-table w-full text-sm">
                    <thead>
                        <tr>
                            <th>Kolom</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-mono text-white">isbn</td>
                            <td><span class="glass-badge-red">Wajib</span></td>
                            <td class="text-white/55">Harus unik di seluruh data buku</td>
                        </tr>
                        <tr>
                            <td class="font-mono text-white">judul</td>
                            <td><span class="glass-badge-red">Wajib</span></td>
                            <td class="text-white/55">Judul buku</td>
                        </tr>
                        <tr>
                            <td class="font-mono text-white">penulis</td>
                            <td><span class="glass-badge-red">Wajib</span></td>
                            <td class="text-white/55">Nama penulis</td>
                        </tr>
                        <tr>
                            <td class="font-mono text-white">penerbit</td>
                            <td><span class="glass-badge-yellow">Opsional</span></td>
                            <td class="text-white/55">Nama penerbit</td>
                        </tr>
                        <tr>
                            <td class="font-mono text-white">tahun_terbit</td>
                            <td><span class="glass-badge-yellow">Opsional</span></td>
                            <td class="text-white/55">4 digit angka, contoh: 2005</td>
                        </tr>
                        <tr>
                            <td class="font-mono text-white">stok</td>
                            <td><span class="glass-badge-yellow">Opsional</span></td>
                            <td class="text-white/55">Angka bulat. Kosongkan = 0</td>
                        </tr>
                        <tr>
                            <td class="font-mono text-white">kategori</td>
                            <td><span class="glass-badge-yellow">Opsional</span></td>
                            <td class="text-white/55">Fiksi, Non-Fiksi, Sains &amp; Teknologi, Sejarah, Pendidikan, Agama, Komik, Novel, Biografi, Pengembangan Diri, atau Lainnya</td>
                        </tr>
                        <tr>
                            <td class="font-mono text-white">cover_image</td>
                            <td><span class="glass-badge-yellow">Opsional</span></td>
                            <td class="text-white/55">URL gambar cover (http/https). Diunduh server saat import. Jika gagal, buku tetap masuk tanpa cover</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== CONTOH FORMAT ===== --}}
        <div class="glass rounded-[12px] p-5">
            <div class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <h3 class="font-display text-[15px] font-semibold text-white">Contoh Format CSV</h3>
                    <p class="font-body text-[13px] text-[#A5ADB3] mt-0.5">Baris pertama adalah judul kolom (header).</p>
                </div>
                <a href="{{ route('books.import.template') }}" class="inline-flex items-center gap-2 h-[36px] px-3.5 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#A5ADB3] text-xs font-medium hover:bg-[#252A2E] hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>

            <div class="mt-3 rounded-[10px] bg-[#151719] border border-white/[0.05] overflow-x-auto">
                <pre class="p-3.5 text-xs leading-relaxed text-white/80 font-mono"><code>isbn,judul,penulis,penerbit,tahun_terbit,stok,kategori,cover_image
9786020631231,Laskar Pelangi,Andrea Hirata,Bentang Pustaka,2005,3,Fiksi,https://example.com/cover/laskar-pelangi.jpg
9789799731234,Atomic Habits,James Clear,Gramedia,2018,2,Pengembangan Diri,</code></pre>
            </div>
            <p class="font-body text-xs text-[#747C82] mt-2">Pemisah boleh koma, titik koma, atau tab — otomatis terdeteksi.</p>
        </div>

        {{-- ===== UPLOAD FILE CSV ===== --}}
        <div class="glass rounded-[12px] p-5">
            <h3 class="font-display text-[15px] font-semibold text-white">Upload File CSV</h3>
            <p class="font-body text-[13px] text-[#A5ADB3] mt-0.5 mb-4">Pilih file CSV yang sudah disiapkan.</p>

            <form action="{{ route('books.import.store') }}" method="POST" enctype="multipart/form-data"
                  x-data="filePicker" @submit="submitting = true">
                @csrf

                <div>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv,text/csv" required
                           x-ref="fileInput"
                           @change="onPick($event)"
                           class="sr-only">
                    <label for="csv_file"
                           @dragover.prevent
                           @drop.prevent="onDrop($event)"
                           class="flex flex-col items-center justify-center gap-2.5 rounded-[10px] border border-dashed border-white/[0.12] bg-[#1B1F22] px-6 py-7 text-center cursor-pointer transition-colors hover:border-[#2DB7A8]/50 hover:bg-white/[0.02]">
                        <span class="flex items-center justify-center w-10 h-10 rounded-full bg-[#2DB7A8]/10">
                            <svg class="w-5 h-5 text-[#35B8A5]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </span>
                        <div>
                            <p class="font-display text-sm font-semibold text-[#F1F3F4]">Tarik &amp; lepas file CSV di sini</p>
                            <p class="font-body text-xs text-[#747C82] mt-0.5">atau pilih file dari komputer</p>
                        </div>
                        <span class="inline-flex items-center justify-center h-[34px] px-4 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#F1F3F4] text-xs font-medium hover:bg-[#252A2E] transition-colors">Pilih File</span>
                    </label>

                    <template x-if="fileName">
                        <div class="mt-3 flex items-center gap-3 rounded-[10px] bg-[#202428] border border-white/[0.06] px-3.5 py-2.5">
                            <span class="flex items-center justify-center w-8 h-8 rounded-[8px] bg-[#2DB7A8]/10 flex-shrink-0">
                                <svg class="w-4 h-4 text-[#2DB7A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="font-body text-sm font-medium text-white truncate" x-text="fileName"></p>
                                <p class="font-body text-xs" :class="isCsv ? 'text-[#747C82]' : 'text-[#E76B73]'">
                                    <span x-text="fileSizeLabel"></span>
                                    <template x-if="!isCsv"><span> &middot; Bukan file CSV</span></template>
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold flex-shrink-0"
                                  :class="isCsv ? 'bg-emerald-400/10 text-emerald-300' : 'bg-rose-500/10 text-rose-300'">
                                <span x-text="isCsv ? 'Valid' : 'Tidak Valid'"></span>
                            </span>
                            <label for="csv_file" class="inline-flex items-center justify-center h-[30px] px-3 rounded-[7px] bg-[#252A2E] border border-white/[0.07] text-xs font-medium text-[#A5ADB3] hover:bg-[#2A3034] hover:text-white transition-colors cursor-pointer flex-shrink-0">Ganti File</label>
                        </div>
                    </template>
                    @error('csv_file') <p class="mt-2 font-body text-xs text-[#E76B73]">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 mt-5 border-t border-white/[0.05]">
                    <a href="{{ route('books.index') }}" class="inline-flex items-center justify-center h-[40px] px-5 rounded-[9px] bg-[#202428] border border-white/[0.08] text-[#A5ADB3] text-sm font-medium hover:bg-[#252A2E] hover:text-white transition-colors">Batal</a>
                    <button type="submit" :disabled="!fileName || submitting"
                            class="inline-flex items-center justify-center gap-2 h-[40px] px-6 rounded-[9px] bg-[#2DB7A8] text-[#071311] text-sm font-semibold hover:bg-[#27A99A] transition-colors disabled:opacity-50 disabled:pointer-events-none">
                        <template x-if="!submitting">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </template>
                        <template x-if="!submitting"><span>Import Buku</span></template>
                        <template x-if="submitting">
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Mengimpor...
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>

        {{-- ===== HASIL IMPORT ===== --}}
        @if (isset($imported) || isset($failed))
            <div class="glass rounded-[12px] p-5">
                <h3 class="font-display text-[15px] font-semibold text-white mb-4">Hasil Import</h3>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div class="rounded-[10px] border border-emerald-400/20 bg-emerald-400/10 p-4">
                        <p class="font-display text-xl font-bold text-emerald-300">{{ count($imported ?? []) }}</p>
                        <p class="font-body text-xs text-[#747C82] mt-1">Berhasil diimport</p>
                    </div>
                    <div class="rounded-[10px] border border-rose-400/20 bg-rose-500/10 p-4">
                        <p class="font-display text-xl font-bold text-rose-300">{{ count($failed ?? []) }}</p>
                        <p class="font-body text-xs text-[#747C82] mt-1">Gagal</p>
                    </div>
                </div>

                @if (!empty($imported))
                    <h4 class="font-display font-semibold text-sm text-white mb-3">Buku Baru</h4>
                    <div class="overflow-x-auto mb-5 rounded-[10px] border border-white/[0.06]">
                        <table class="glass-table import-table w-full text-sm">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>ISBN</th>
                                    <th>Tahun</th>
                                    <th>Stok</th>
                                    <th>Kategori</th>
                                    <th>Cover</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($imported as $book)
                                    <tr>
                                        <td class="text-white">{{ $book['title'] }}</td>
                                        <td class="text-white/55">{{ $book['author'] }}</td>
                                        <td class="font-mono text-white/70">{{ $book['isbn'] }}</td>
                                        <td class="text-white/55">{{ $book['publication_year'] ?: '-' }}</td>
                                        <td class="text-white/55">{{ $book['stock'] !== '' ? $book['stock'] : '0' }}</td>
                                        <td>
                                            @if ($book['kategori'])
                                                <span class="glass-badge-violet">{{ $book['kategori'] }}</span>
                                            @else
                                                <span class="text-white/30">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($book['cover_image'])
                                                <img src="{{ $book['cover_image'] }}" alt="Cover {{ $book['title'] }}" class="h-12 w-9 object-cover rounded-glass-sm border border-white/10">
                                            @else
                                                <span class="text-white/30">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if (!empty($failed))
                    <h4 class="font-display font-semibold text-sm text-white mb-3">Baris Gagal</h4>
                    <div class="overflow-x-auto rounded-[10px] border border-white/[0.06]">
                        <table class="glass-table import-table w-full text-sm">
                            <thead>
                                <tr>
                                    <th>ISBN</th>
                                    <th>Judul</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($failed as $item)
                                    <tr>
                                        <td class="font-mono text-white/70">{{ $item['isbn'] ?: '-' }}</td>
                                        <td class="text-white">{{ $item['title'] ?: '-' }}</td>
                                        <td>
                                            @foreach ($item['errors'] as $error)
                                                <span class="inline-block bg-rose-500/10 border border-rose-400/20 text-rose-300 text-xs rounded-full px-2 py-0.5 mr-1 mb-1">{{ $error }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-5 pt-4 border-t border-white/[0.05]">
                    <a href="{{ route('books.index') }}" class="inline-flex items-center justify-center gap-2 h-[40px] px-6 rounded-[9px] bg-[#2DB7A8] text-[#071311] text-sm font-semibold hover:bg-[#27A99A] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Selesai — Kembali ke Katalog Buku
                    </a>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>