<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-gradient">Import Anggota via CSV</h2>
                <p class="font-body text-sm text-white/45 mt-1">Tambah banyak anggota sekaligus dari file CSV</p>
            </div>
            <a href="{{ route('users.index') }}" class="glass-btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6" x-data="reveal">

        @if (session('error'))
            <div class="glass rounded-glass-sm border-rose-400/30 px-5 py-4 font-body text-sm text-rose-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- ===== PANDUAN ===== --}}
        <div class="glass p-6">
            <h2 class="font-display font-semibold text-lg text-white mb-4">Panduan Sebelum Import</h2>

            <ol class="space-y-3">
                <li class="flex gap-3">
                    <span class="w-7 h-7 bg-gradient-soft text-white rounded-full flex items-center justify-center text-sm font-display font-semibold flex-shrink-0 shadow-glow">1</span>
                    <div>
                        <p class="font-display font-semibold text-sm text-white">Siapkan file CSV</p>
                        <p class="font-body text-sm text-white/40 mt-0.5">Buka Excel/Google Sheets, isi data anggota, lalu simpan sebagai CSV.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="w-7 h-7 bg-gradient-soft text-white rounded-full flex items-center justify-center text-sm font-display font-semibold flex-shrink-0 shadow-glow">2</span>
                    <div>
                        <p class="font-display font-semibold text-sm text-white">Pastikan kolom sesuai</p>
                        <p class="font-body text-sm text-white/40 mt-0.5">Baris pertama harus berisi judul kolom. Kolom <span class="font-medium text-white">nama</span> dan <span class="font-medium text-white">email</span> wajib diisi.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="w-7 h-7 bg-gradient-soft text-white rounded-full flex items-center justify-center text-sm font-display font-semibold flex-shrink-0 shadow-glow">3</span>
                    <div>
                        <p class="font-display font-semibold text-sm text-white">Unggah dan impor</p>
                        <p class="font-body text-sm text-white/40 mt-0.5">Pilih file di bawah lalu klik <span class="font-medium text-white">Import Anggota</span>. Hasilnya akan langsung tampil.</p>
                    </div>
                </li>
            </ol>

            {{-- Kolom --}}
            <div class="mt-6 pt-5 border-t border-white/10">
                <h3 class="font-display font-semibold text-sm text-white mb-3">Struktur Kolom</h3>
                <div class="overflow-x-auto glass-inset rounded-glass-sm">
                    <table class="glass-table w-full text-sm">
                        <thead>
                            <tr>
                                <th>Kolom</th>
                                <th>Wajib?</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-mono text-white">nama</td>
                                <td><span class="glass-badge-red">Wajib</span></td>
                                <td class="text-white/55">Nama lengkap anggota</td>
                            </tr>
                            <tr>
                                <td class="font-mono text-white">email</td>
                                <td><span class="glass-badge-red">Wajib</span></td>
                                <td class="text-white/55">Harus unik dan format email valid</td>
                            </tr>
                            <tr>
                                <td class="font-mono text-white">nisn</td>
                                <td><span class="glass-badge-yellow">Opsional</span></td>
                                <td class="text-white/55">Nomor Induk Siswa Nasional (unik)</td>
                            </tr>
                            <tr>
                                <td class="font-mono text-white">role</td>
                                <td><span class="glass-badge-yellow">Opsional</span></td>
                                <td class="text-white/55">admin, staff, atau user (kosongkan = user)</td>
                            </tr>
                            <tr>
                                <td class="font-mono text-white">password</td>
                                <td><span class="glass-badge-yellow">Opsional</span></td>
                                <td class="text-white/55">Min. 8 karakter huruf + angka. Kosongkan = password default <code class="font-mono text-white/80">password</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Contoh --}}
            <div class="mt-5">
                <h3 class="font-display font-semibold text-sm text-white mb-2">Contoh Isi File</h3>
                <pre class="glass-inset rounded-glass-sm p-4 text-xs leading-relaxed text-white/80 overflow-x-auto"><code>nama,email,nisn,role,password
Budi Santoso,budi@example.com,0012345678,user,santos12345
Siti Aminah,siti@example.com,0098765432,,aminah2026</code></pre>
                <p class="font-body text-xs text-white/40 mt-2">Baris pertama adalah judul kolom (header). Pemisah boleh koma, titik koma, atau tab — otomatis terdeteksi.</p>
            </div>

            <div class="mt-5 flex items-center gap-3">
                <a href="{{ route('users.import.template') }}" class="glass-btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template CSV
                </a>
            </div>
        </div>

        {{-- ===== FORM UPLOAD ===== --}}
        <div class="glass p-6">
            <h2 class="font-display font-semibold text-lg text-white mb-1">Upload File CSV</h2>
            <p class="font-body text-sm text-white/40 mb-5">Pilih file CSV yang sudah disiapkan</p>

            <form action="{{ route('users.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div>
                    <label for="csv_file" class="block font-body text-xs font-medium text-white/70 mb-2">Pilih File CSV</label>
                    <div x-data="filePicker" class="relative">
                        <input type="file" id="csv_file" name="csv_file" accept=".csv,text/csv" required
                            @change="onPick($event)"
                            class="glass-input file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-white/10 file:text-white file:hover:bg-white/15 file:cursor-pointer file:transition-colors cursor-pointer">
                        <template x-if="fileName">
                            <p class="mt-2 font-body text-xs text-emerald-300" x-text="'✓ ' + fileName"></p>
                        </template>
                    </div>
                    @error('csv_file') <p class="mt-1 font-body text-xs text-rose-300">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-5 mt-5 border-t border-white/10">
                    <a href="{{ route('users.index') }}" class="glass-btn-secondary">Batal</a>
                    <button type="submit" class="glass-btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Import Anggota
                    </button>
                </div>
            </form>
        </div>

        {{-- ===== HASIL IMPORT ===== --}}
        @if (isset($imported) || isset($failed))
            <div class="glass p-6">
                <h2 class="font-display font-semibold text-lg text-white mb-4">Hasil Import</h2>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="glass-inset rounded-glass-sm border-emerald-400/20 p-4">
                        <p class="font-display text-lg sm:text-2xl font-bold text-emerald-300">{{ count($imported ?? []) }}</p>
                        <p class="font-body text-xs text-white/40 mt-1">Berhasil diimport</p>
                    </div>
                    <div class="glass-inset rounded-glass-sm border-rose-400/20 p-4">
                        <p class="font-display text-lg sm:text-2xl font-bold text-rose-300">{{ count($failed ?? []) }}</p>
                        <p class="font-body text-xs text-white/40 mt-1">Gagal</p>
                    </div>
                </div>

                @if (!empty($imported))
                    <h3 class="font-display font-semibold text-sm text-white mb-3">Akun Baru</h3>
                    <div class="overflow-x-auto mb-6 glass-inset rounded-glass-sm">
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
                    <h3 class="font-display font-semibold text-sm text-white mb-3">Baris Gagal</h3>
                    <div class="overflow-x-auto glass-inset rounded-glass-sm">
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
                                                <span class="inline-block bg-rose-500/10 border border-rose-400/20 text-rose-300 text-xs rounded-glass-full px-2 py-0.5 mr-1 mb-1">{{ $error }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-6 pt-4 border-t border-white/10">
                    <a href="{{ route('users.index') }}" class="glass-btn-primary">Selesai — Kembali ke Daftar Pengguna</a>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
