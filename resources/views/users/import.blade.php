<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-display text-text">Import Anggota via CSV</h1>
                <p class="mt-1 text-text-tertiary">Tambah banyak anggota sekaligus dari file CSV</p>
            </div>
            <a href="{{ route('users.index') }}" class="apple-btn-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-apple-lg space-y-6">

            @if (session('error'))
                <div class="bg-danger text-white px-6 py-4 font-display text-sm rounded-apple-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ===== PANDUAN ===== --}}
            <div class="bg-white rounded-apple-lg p-6">
                <h2 class="font-display font-semibold text-lg text-text mb-4">Panduan Sebelum Import</h2>

                <ol class="space-y-3">
                    <li class="flex gap-3">
                        <span class="w-7 h-7 bg-apple-blue text-white rounded-full flex items-center justify-center text-sm font-display font-semibold flex-shrink-0">1</span>
                        <div>
                            <p class="font-display font-semibold text-sm text-text">Siapkan file CSV</p>
                            <p class="text-sm text-text-tertiary mt-0.5">Buka Excel/Google Sheets, isi data anggota, lalu simpan sebagai CSV.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-7 h-7 bg-apple-blue text-white rounded-full flex items-center justify-center text-sm font-display font-semibold flex-shrink-0">2</span>
                        <div>
                            <p class="font-display font-semibold text-sm text-text">Pastikan kolom sesuai</p>
                            <p class="text-sm text-text-tertiary mt-0.5">Baris pertama harus berisi judul kolom. Kolom <span class="font-medium text-text">nama</span> dan <span class="font-medium text-text">email</span> wajib diisi.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-7 h-7 bg-apple-blue text-white rounded-full flex items-center justify-center text-sm font-display font-semibold flex-shrink-0">3</span>
                        <div>
                            <p class="font-display font-semibold text-sm text-text">Unggah dan impor</p>
                            <p class="text-sm text-text-tertiary mt-0.5">Pilih file di bawah lalu klik <span class="font-medium text-text">Import Anggota</span>. Hasilnya akan langsung tampil.</p>
                        </div>
                    </li>
                </ol>

                {{-- Kolom --}}
                <div class="mt-6 border-t border-surface-lighter pt-5">
                    <h3 class="font-display font-semibold text-sm text-text mb-3">Struktur Kolom</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-surface-lighter">
                                    <th class="text-left py-2 pr-4 font-display font-semibold text-text-tertiary">Kolom</th>
                                    <th class="text-left py-2 pr-4 font-display font-semibold text-text-tertiary">Wajib?</th>
                                    <th class="text-left py-2 font-display font-semibold text-text-tertiary">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-surface-lighter">
                                    <td class="py-2 pr-4 font-mono text-text">nama</td>
                                    <td class="py-2 pr-4"><span class="apple-badge-red">Wajib</span></td>
                                    <td class="py-2 text-text-tertiary">Nama lengkap anggota</td>
                                </tr>
                                <tr class="border-b border-surface-lighter">
                                    <td class="py-2 pr-4 font-mono text-text">email</td>
                                    <td class="py-2 pr-4"><span class="apple-badge-red">Wajib</span></td>
                                    <td class="py-2 text-text-tertiary">Harus unik dan format email valid</td>
                                </tr>
                                <tr class="border-b border-surface-lighter">
                                    <td class="py-2 pr-4 font-mono text-text">nisn</td>
                                    <td class="py-2 pr-4"><span class="apple-badge-yellow">Opsional</span></td>
                                    <td class="py-2 text-text-tertiary">Nomor Induk Siswa Nasional (unik)</td>
                                </tr>
                                <tr class="border-b border-surface-lighter">
                                    <td class="py-2 pr-4 font-mono text-text">role</td>
                                    <td class="py-2 pr-4"><span class="apple-badge-yellow">Opsional</span></td>
                                    <td class="py-2 text-text-tertiary">admin, staff, atau user (kosongkan = user)</td>
                                </tr>
                                <tr class="border-b border-surface-lighter">
                                    <td class="py-2 pr-4 font-mono text-text">password</td>
                                    <td class="py-2 pr-4"><span class="apple-badge-yellow">Opsional</span></td>
                                    <td class="py-2 text-text-tertiary">Min. 8 karakter huruf + angka. Kosongkan = dibuat otomatis</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Contoh --}}
                <div class="mt-5">
                    <h3 class="font-display font-semibold text-sm text-text mb-2">Contoh Isi File</h3>
                    <pre class="bg-surface-light rounded-apple-md p-4 text-xs leading-relaxed text-text overflow-x-auto"><code>nama,email,nisn,role,password
Budi Santoso,budi@example.com,0012345678,user,santos12345
Siti Aminah,siti@example.com,0098765432,,aminah2026</code></pre>
                    <p class="text-xs text-text-tertiary mt-2">Baris pertama adalah judul kolom (header). Pemisah boleh koma, titik koma, atau tab — otomatis terdeteksi.</p>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <a href="{{ route('users.import.template') }}" class="apple-btn-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Unduh Template CSV
                    </a>
                </div>
            </div>

            {{-- ===== FORM UPLOAD ===== --}}
            <div class="bg-white rounded-apple-lg p-6">
                <h2 class="font-display font-semibold text-lg text-text mb-4">Upload File CSV</h2>

                <form action="{{ route('users.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label for="csv_file" class="block text-sm font-display font-semibold text-text mb-2">Pilih File CSV</label>
                            <input type="file" id="csv_file" name="csv_file" accept=".csv,text/csv"
                                class="apple-input w-full @error('csv_file') border-danger @enderror"
                                required>
                            @error('csv_file')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-surface-lighter">
                            <a href="{{ route('users.index') }}" class="apple-btn-secondary">Batal</a>
                            <button type="submit" class="apple-btn-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Import Anggota
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ===== HASIL IMPORT ===== --}}
            @if (isset($imported) || isset($failed))
                <div class="bg-white rounded-apple-lg p-6">
                    <h2 class="font-display font-semibold text-lg text-text mb-4">Hasil Import</h2>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-apple-blue/5 border border-apple-blue/20 rounded-apple-md p-4">
                            <p class="text-2xl font-display font-semibold text-apple-blue">{{ count($imported ?? []) }}</p>
                            <p class="text-xs text-text-tertiary mt-1">Berhasil diimport</p>
                        </div>
                        <div class="bg-red-50 border border-danger/20 rounded-apple-md p-4">
                            <p class="text-2xl font-display font-semibold text-danger">{{ count($failed ?? []) }}</p>
                            <p class="text-xs text-text-tertiary mt-1">Gagal</p>
                        </div>
                    </div>

                    @if (!empty($imported))
                        <h3 class="font-display font-semibold text-sm text-text mb-3">Akun Baru</h3>
                        <div class="overflow-x-auto mb-6">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-surface-lighter">
                                        <th class="text-left py-2 pr-4 font-display font-semibold text-text-tertiary">Nama</th>
                                        <th class="text-left py-2 pr-4 font-display font-semibold text-text-tertiary">Email</th>
                                        <th class="text-left py-2 pr-4 font-display font-semibold text-text-tertiary">Role</th>
                                        <th class="text-left py-2 font-display font-semibold text-text-tertiary">Password</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($imported as $user)
                                        <tr class="border-b border-surface-lighter">
                                            <td class="py-2 pr-4 text-text">{{ $user['name'] }}</td>
                                            <td class="py-2 pr-4 text-text-tertiary">{{ $user['email'] }}</td>
                                            <td class="py-2 pr-4">
                                                @if ($user['role'] == 'admin')
                                                    <span class="apple-badge-red">Admin</span>
                                                @elseif ($user['role'] == 'staff')
                                                    <span class="apple-badge-yellow">Staff</span>
                                                @else
                                                    <span class="apple-badge-blue">Anggota</span>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                <span class="font-mono text-text">{{ $user['password'] }}</span>
                                                <span class="text-xs text-text-tertiary">(dapat diganti)</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if (!empty($failed))
                        <h3 class="font-display font-semibold text-sm text-text mb-3">Baris Gagal</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-surface-lighter">
                                        <th class="text-left py-2 pr-4 font-display font-semibold text-text-tertiary">Nama</th>
                                        <th class="text-left py-2 pr-4 font-display font-semibold text-text-tertiary">Email</th>
                                        <th class="text-left py-2 font-display font-semibold text-text-tertiary">Alasan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($failed as $item)
                                        <tr class="border-b border-surface-lighter">
                                            <td class="py-2 pr-4 text-text">{{ $item['name'] ?: '-' }}</td>
                                            <td class="py-2 pr-4 text-text-tertiary">{{ $item['email'] ?: '-' }}</td>
                                            <td class="py-2">
                                                @foreach ($item['errors'] as $error)
                                                    <span class="inline-block bg-red-50 border border-danger/20 text-danger text-xs rounded-apple-sm px-2 py-0.5 mr-1 mb-1">{{ $error }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="mt-6 pt-4 border-t border-surface-lighter">
                        <a href="{{ route('users.index') }}" class="apple-btn-primary">Selesai — Kembali ke Daftar Pengguna</a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
