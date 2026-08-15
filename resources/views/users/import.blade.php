<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-[24px] font-semibold tracking-tight text-white">Import Anggota via CSV</h2>
                <p class="font-body text-[13px] text-[#8B949E] mt-1">Tambahkan banyak anggota sekaligus dari file CSV.</p>
            </div>
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 h-[38px] px-4 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#A5ADB3] text-sm font-medium hover:bg-[#252A2E] hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-[1140px] mx-auto space-y-5" x-data="reveal">

        @if (session('error'))
            <div class="flex items-start gap-3 rounded-[10px] border border-[#E76B73]/[0.18] bg-[#E76B73]/[0.10] px-4 py-3 font-body text-sm text-[#E76B73]">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ===== QUICK STEPS ===== --}}
        <div class="glass rounded-[12px] px-5 py-3.5">
            <ol class="flex items-center gap-3 flex-wrap">
                <li class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[#2DB7A8] text-[#071311] text-[11px] font-bold flex items-center justify-center flex-shrink-0">1</span>
                    <span class="font-body text-[13px] font-medium text-white">Siapkan CSV</span>
                </li>
                <li class="flex-1 h-px bg-white/[0.08] min-w-[20px]"></li>
                <li class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[#202428] border border-white/[0.06] text-[#747C82] text-[11px] font-bold flex items-center justify-center flex-shrink-0">2</span>
                    <span class="font-body text-[13px] font-medium text-[#A5ADB3]">Periksa kolom</span>
                </li>
                <li class="flex-1 h-px bg-white/[0.08] min-w-[20px]"></li>
                <li class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-[#202428] border border-white/[0.06] text-[#747C82] text-[11px] font-bold flex items-center justify-center flex-shrink-0">3</span>
                    <span class="font-body text-[13px] font-medium text-[#A5ADB3]">Upload &amp; Import</span>
                </li>
            </ol>
        </div>

        {{-- ===== WORKSPACE ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-[3fr_2fr] gap-5 items-start">

            {{-- LEFT: UPLOAD --}}
            <div class="glass rounded-[12px] p-6">
                <h3 class="font-display text-[16px] font-semibold text-white">Upload File CSV</h3>
                <p class="font-body text-[13px] text-[#8B949E] mt-0.5 mb-5">Pilih file CSV yang sudah disiapkan.</p>

                <form action="{{ route('users.import.store') }}" method="POST" enctype="multipart/form-data"
                      x-data="filePicker" @submit="submitting = true">
                    @csrf

                    <input type="file" id="csv_file" name="csv_file" accept=".csv,text/csv" required
                           x-ref="fileInput"
                           @change="onPick($event)"
                           class="sr-only">

                    {{-- Dropzone (belum ada file) --}}
                    <template x-if="!fileName">
                        <label for="csv_file"
                               @dragover.prevent
                               @drop.prevent="onDrop($event)"
                               class="flex flex-col items-center justify-center gap-3 rounded-[10px] border border-dashed border-white/[0.12] bg-[#151719] min-h-[220px] px-6 py-10 text-center cursor-pointer transition-colors hover:border-[#2DB7A8]/50 hover:bg-white/[0.02]">
                            <span class="flex items-center justify-center w-12 h-12 rounded-full bg-[#2DB7A8]/10">
                                <svg class="w-6 h-6 text-[#35B8A5]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            </span>
                            <div>
                                <p class="font-display text-sm font-semibold text-[#F1F3F4]">Tarik &amp; lepas file CSV di sini</p>
                                <p class="font-body text-xs text-[#747C82] mt-1">atau pilih file dari komputer</p>
                            </div>
                            <span class="inline-flex items-center justify-center h-[34px] px-4 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#F1F3F4] text-xs font-medium hover:bg-[#252A2E] transition-colors">Pilih File</span>
                        </label>
                    </template>

                    {{-- Selected file (file sudah dipilih) --}}
                    <template x-if="fileName">
                        <div class="rounded-[10px] border border-dashed border-white/[0.12] bg-[#151719] min-h-[220px] p-5 flex flex-col">
                            <div class="flex items-center gap-3 rounded-[10px] bg-[#202428] border border-white/[0.06] px-4 py-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-[8px] bg-[#2DB7A8]/10 flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#2DB7A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="font-body text-sm font-medium text-white truncate" x-text="fileName"></p>
                                    <p class="font-body text-xs text-[#747C82] mt-0.5" x-text="fileSizeLabel"></p>
                                </div>
                                <label for="csv_file" class="inline-flex items-center justify-center h-[30px] px-3 rounded-[7px] bg-[#252A2E] border border-white/[0.07] text-xs font-medium text-[#A5ADB3] hover:bg-[#2A3034] hover:text-white transition-colors cursor-pointer flex-shrink-0">Ganti File</label>
                            </div>
                            <div class="mt-auto pt-4">
                                <p class="font-body text-[13px] font-medium flex items-center gap-1.5" :class="isCsv ? 'text-[#4CAF7D]' : 'text-[#E76B73]'">
                                    <svg x-show="isCsv" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <svg x-show="!isCsv" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span x-text="isCsv ? 'File siap diimpor' : 'Bukan file CSV — pilih file lain'"></span>
                                </p>
                            </div>
                        </div>
                    </template>

                    @error('csv_file')
                        <div class="mt-3 flex items-start gap-2.5 rounded-[10px] border border-[#E76B73]/[0.18] bg-[#E76B73]/[0.10] px-4 py-3 font-body text-[13px] text-[#E76B73]">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="mt-5">
                        <button type="submit" :disabled="!fileName || submitting"
                                class="w-full inline-flex items-center justify-center gap-2 h-[42px] rounded-[8px] bg-[#2DB7A8] text-[#071311] text-sm font-semibold hover:bg-[#27A99A] transition-colors disabled:opacity-50 disabled:pointer-events-none">
                            <template x-if="!submitting">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            </template>
                            <template x-if="!submitting"><span>Import Anggota</span></template>
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

            {{-- RIGHT: FORMAT CSV --}}
            <div class="glass rounded-[12px] p-6">
                <h3 class="font-display text-[16px] font-semibold text-white">Format CSV</h3>
                <p class="font-body text-[13px] text-[#8B949E] mt-0.5 mb-4">Kolom yang tersedia dalam file import.</p>

                <div class="rounded-[10px] border border-white/[0.06] overflow-hidden divide-y divide-white/[0.045]">
                    <div class="flex items-center justify-between px-4 h-[42px]">
                        <span class="font-mono text-[13px] text-white">nama</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-rose-500/10 text-[#E76B73] border border-rose-400/15">Wajib</span>
                    </div>
                    <div class="flex items-center justify-between px-4 h-[42px]">
                        <span class="font-mono text-[13px] text-white">email</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-rose-500/10 text-[#E76B73] border border-rose-400/15">Wajib</span>
                    </div>
                    <div class="flex items-center justify-between px-4 h-[42px]">
                        <span class="font-mono text-[13px] text-white">nisn</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-amber-400/10 text-[#D9A441] border border-amber-400/15">Opsional</span>
                    </div>
                    <div class="flex items-center justify-between px-4 h-[42px]">
                        <span class="font-mono text-[13px] text-white">role</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-amber-400/10 text-[#D9A441] border border-amber-400/15">Opsional</span>
                    </div>
                    <div class="flex items-center justify-between px-4 h-[42px]">
                        <span class="font-mono text-[13px] text-white">password</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-amber-400/10 text-[#D9A441] border border-amber-400/15">Opsional</span>
                    </div>
                </div>

                <p class="font-body text-xs text-[#747C82] mt-3">Baris pertama digunakan sebagai header. Pemisah koma, titik semikolon, atau tab dideteksi otomatis.</p>

                <a href="{{ route('users.import.template') }}" class="mt-5 w-full inline-flex items-center justify-center gap-2 h-[38px] px-4 rounded-[8px] bg-[#202428] border border-white/[0.08] text-[#A5ADB3] text-sm font-medium hover:bg-[#252A2E] hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
        </div>

        {{-- ===== CONTOH CSV ===== --}}
        <div class="glass rounded-[12px] p-6">
            <h3 class="font-display text-[16px] font-semibold text-white">Contoh Format CSV</h3>
            <p class="font-body text-[13px] text-[#8B949E] mt-0.5">Baris pertama adalah judul kolom (header).</p>
            <div class="mt-3 rounded-[10px] bg-[#151719] border border-white/[0.05] overflow-x-auto max-h-[120px] overflow-y-auto">
                <pre class="p-3.5 text-xs leading-relaxed text-white/80 font-mono"><code>nama,email,nisn,role,password
Budi Santoso,budi@example.com,0012345678,user,santos12345
Siti Aminah,siti@example.com,0098765432,,aminah2026</code></pre>
            </div>
        </div>

        {{-- ===== HASIL IMPORT ===== --}}
        @if (isset($imported) || isset($failed))
            <div class="glass rounded-[12px] p-6">
                <div class="flex items-center gap-2.5 mb-4">
                    <span class="flex items-center justify-center w-8 h-8 rounded-[9px] bg-emerald-400/10 border border-emerald-400/20">
                        <svg class="w-4 h-4 text-[#4CAF7D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div>
                        <h3 class="font-display text-[16px] font-semibold text-white">Import berhasil</h3>
                        <p class="font-body text-[13px] text-[#8B949E] mt-0.5">Hasil pemrosesan file CSV.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div class="rounded-[10px] border border-emerald-400/20 bg-emerald-400/10 p-4">
                        <p class="font-display text-[26px] font-bold text-[#4CAF7D] leading-tight">{{ count($imported ?? []) }}</p>
                        <p class="font-body text-xs text-[#747C82] mt-0.5">Berhasil diimport</p>
                    </div>
                    <div class="rounded-[10px] border border-[#E76B73]/[0.18] bg-[#E76B73]/[0.10] p-4">
                        <p class="font-display text-[26px] font-bold text-[#E76B73] leading-tight">{{ count($failed ?? []) }}</p>
                        <p class="font-body text-xs text-[#747C82] mt-0.5">Gagal</p>
                    </div>
                </div>

                @if (!empty($imported))
                    <h4 class="font-display font-semibold text-sm text-white mb-3">Akun Baru</h4>
                    <div class="overflow-x-auto mb-5 rounded-[10px] border border-white/[0.06]">
                        <table class="glass-table w-full text-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($imported as $user)
                                    <tr>
                                        <td class="text-white">{{ $user['name'] }}</td>
                                        <td class="text-white/55">{{ $user['email'] }}</td>
                                        <td>
                                            @if ($user['role'] == 'admin')
                                                <span class="glass-badge-red">Admin</span>
                                            @elseif ($user['role'] == 'staff')
                                                <span class="glass-badge-yellow">Staff</span>
                                            @else
                                                <span class="glass-badge-blue">Anggota</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="font-mono text-white/80">{{ $user['password'] }}</span>
                                            <span class="font-body text-xs text-white/40">(dapat diganti)</span>
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
                        <table class="glass-table w-full text-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($failed as $item)
                                    <tr>
                                        <td class="text-white">{{ $item['name'] ?: '-' }}</td>
                                        <td class="text-white/55">{{ $item['email'] ?: '-' }}</td>
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
                    <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center gap-2 h-[40px] px-6 rounded-[8px] bg-[#2DB7A8] text-[#071311] text-sm font-semibold hover:bg-[#27A99A] transition-colors">
                        Selesai — Kembali ke Daftar Pengguna
                    </a>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>