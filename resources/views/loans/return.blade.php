<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-xl sm:text-2xl font-bold tracking-tight text-gradient">Kembalikan Buku</h2>
            <p class="font-body text-sm text-white/45 mt-1">Scan QR untuk memproses pengembalian</p>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="mb-6 glass rounded-glass-sm border-rose-400/30 px-5 py-4">
            <p class="font-display text-sm font-semibold text-rose-300 mb-1">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li class="font-body text-sm text-rose-200/80">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="reveal">
        {{-- Scan Station --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="glass p-6 border-sky-400/20">
                <h3 class="font-display font-semibold text-sm text-white mb-1">Pengembalian Buku</h3>
                <p class="font-body text-sm text-white/40">Scan QR Code buku yang akan dikembalikan. Identitas peminjam akan otomatis terdeteksi.</p>
            </div>

            <div class="glass p-6">
                <h3 class="font-display font-semibold text-lg text-white mb-1">Scan QR Code</h3>
                <p class="font-body text-sm text-white/40 mb-5">Aktifkan kamera lalu arahkan ke QR Code buku yang akan dikembalikan.</p>

                {{-- Scan Area --}}
                <div id="scan-area" class="glass-inset p-8 text-center relative overflow-hidden scan-pulse rounded-glass">
                    <div id="reader" class="w-full hidden" style="height: 70vh;"></div>
                    <div id="scan-placeholder">
                        <div class="text-5xl mb-4">📷</div>
                        <p class="font-display font-semibold text-white text-lg">Menunggu Scanner...</p>
                        <p class="font-body text-sm text-white/40 mt-1">Klik tombol di bawah untuk mulai</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 flex gap-3">
                    <button type="button" id="btn-start-camera" class="glass-btn-primary flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Siap Scan
                    </button>
                    <button type="button" id="btn-stop-camera" class="glass-btn-danger hidden flex-1">Matikan Kamera</button>
                </div>

                {{-- Return Info Box (hidden until scan) --}}
                <div id="return-info" class="mt-6 hidden">
                    <div id="return-info-box" class="glass p-6 rounded-glass">
                        <h4 class="font-display font-semibold text-sm text-white mb-4">Info Pengembalian</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="font-body text-xs text-white/40">Judul Buku</span>
                                <p id="info-title" class="font-display font-semibold text-white mt-0.5">-</p>
                            </div>
                            <div>
                                <span class="font-body text-xs text-white/40">Peminjam</span>
                                <p id="info-borrower" class="font-display font-semibold text-white mt-0.5">-</p>
                            </div>
                            <div>
                                <span class="font-body text-xs text-white/40">Tgl Pinjam</span>
                                <p id="info-loan-date" class="font-display font-semibold text-white mt-0.5">-</p>
                            </div>
                            <div>
                                <span class="font-body text-xs text-white/40">Jatuh Tempo</span>
                                <p id="info-due-date" class="font-display font-semibold text-white mt-0.5">-</p>
                            </div>
                        </div>

                        {{-- Denda Warning --}}
                        <div id="denda-warning" class="mt-4 hidden">
                            <div class="rounded-glass-sm border border-rose-400/30 bg-rose-500/10 px-4 py-3">
                                <p class="font-display text-sm font-semibold text-rose-300">TERLAMBAT! <span id="days-late-text">-</span></p>
                                <p class="font-display text-lg sm:text-xl font-bold text-rose-300 mt-1">Denda: Rp <span id="denda-amount">0</span></p>
                                <p class="font-body text-xs text-white/40 mt-1" id="denda-rate-text">Tarif: Rp500/hari</p>
                            </div>
                        </div>

                        <div id="denda-ok" class="mt-4 hidden">
                            <div class="rounded-glass-sm border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 font-body text-sm text-emerald-300">
                                ✓ Tidak ada denda — dikembalikan tepat waktu!
                            </div>
                        </div>

                        <form id="return-form" method="POST" action="{{ route('loans.return.store') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="loan_id" id="return-loan-id">

                            <label class="flex items-start gap-3 cursor-pointer rounded-glass-sm border border-amber-400/20 bg-amber-400/[0.06] p-3.5 transition-colors hover:bg-amber-400/10 mb-4">
                                <input type="checkbox" name="confirm_received" value="1" id="confirm-received"
                                       class="mt-0.5 h-5 w-5 rounded bg-white/[0.06] border-white/15 text-primary focus:ring-primary/40">
                                <div>
                                    <p class="font-display font-semibold text-sm text-white">Buku diterima secara fisik</p>
                                    <p class="font-body text-xs text-white/40 mt-0.5">Saya mengonfirmasi bahwa buku ini telah diterima dan dalam kondisi yang sesuai.</p>
                                </div>
                            </label>
                            @error('confirm_received')
                                <p class="font-body text-xs text-rose-300 mb-2">{{ $message }}</p>
                            @enderror

                            <button type="submit" id="btn-confirm-return" class="glass-btn-primary w-full" disabled>
                                Konfirmasi Kembalikan
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Manual Input --}}
                <div class="mt-6 pt-6 border-t border-white/10">
                    <h4 class="font-display text-sm text-white mb-3">Atau Input Manual</h4>
                    <form id="manual-return-form" class="flex gap-2">
                        @csrf
                        <input type="text" id="isbn-manual" name="isbn"
                               class="glass-input flex-1 font-mono"
                               placeholder="Masukkan ISBN..."
                               autocomplete="off">
                        <button type="submit" class="glass-btn-primary whitespace-nowrap">Cek</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Active Loans --}}
        <div>
            <div class="glass p-6 h-full">
                <h3 class="font-display font-semibold text-lg text-white mb-1">Sedang Dipinjam</h3>
                <p class="font-body text-sm text-white/40 mb-4">Buku yang aktif dipinjam</p>
                @if ($activeLoans->isEmpty())
                    <div class="text-center py-10">
                        <div class="text-4xl mb-3">✅</div>
                        <p class="font-body text-sm text-white/40">Tidak ada buku dipinjam</p>
                    </div>
                @else
                    <div class="space-y-2 max-h-[640px] overflow-y-auto pr-1">
                        @foreach ($activeLoans as $loan)
                            <div class="glass-inset rounded-glass-sm p-3 transition-colors duration-150 {{ $loan->isOverdue() ? 'border-rose-400/25 bg-rose-500/[0.06]' : '' }}">
                                <div class="flex items-center gap-3">
                                    @if ($loan->book->cover_url)
                                        <img src="{{ $loan->book->cover_url }}" alt="{{ $loan->book->title }}" class="h-12 w-9 object-cover rounded-lg flex-shrink-0 border border-white/10">
                                    @else
                                        <div class="h-12 w-9 rounded-lg bg-white/[0.06] border border-white/10 flex items-center justify-center text-lg flex-shrink-0">📖</div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="font-display font-medium text-sm text-white truncate">{{ $loan->book->title }}</p>
                                        <p class="font-body text-xs text-white/40">{{ $loan->user->name }}{{ $loan->user->nisn ? ' (' . $loan->user->nisn . ')' : '' }}</p>
                                        <p class="font-body text-xs {{ $loan->isOverdue() ? 'text-rose-300 font-medium' : 'text-white/40' }}">
                                            Tempo: {{ $loan->due_date->format('d/m/Y') }}
                                            @if ($loan->isOverdue())
                                                · {{ $loan->getDaysLate() }}h telat
                                            @endif
                                        </p>
                                        @if ($loan->isOverdue())
                                            <p class="font-body text-xs text-rose-300 font-semibold mt-0.5">
                                                Potensi denda: Rp{{ number_format($loan->getPotentialDenda(), 0, ',', '.') }} (Rp{{ number_format($loan->getDendaPerDay(), 0, ',', '.') }}/hari)
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" data-isbn="{{ $loan->book->isbn }}" class="glass-btn-secondary w-full text-xs py-1.5 mt-2.5 return-select-btn">Pilih untuk dikembalikan</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnStart = document.getElementById('btn-start-camera');
            const btnStop = document.getElementById('btn-stop-camera');
            const readerDiv = document.getElementById('reader');
            const scanPlaceholder = document.getElementById('scan-placeholder');
            const scanArea = document.getElementById('scan-area');
            const returnInfo = document.getElementById('return-info');
            const returnInfoBox = document.getElementById('return-info-box');
            const dendaWarning = document.getElementById('denda-warning');
            const dendaOk = document.getElementById('denda-ok');
            const manualForm = document.getElementById('manual-return-form');

            let html5QrCode = null;

            function checkReturn(isbn) {
                fetch('{{ route("loans.return.check") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ isbn: isbn }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.error,
                            confirmButtonColor: '#fb5e63',
                            background: '#0b1220',
                            color: '#ffffff',
                            customClass: { popup: 'rounded-2xl border border-white/10' }
                        });
                        return;
                    }

                    document.getElementById('info-title').textContent = data.book_title;
                    document.getElementById('info-borrower').textContent = data.borrower_name + (data.borrower_nisn ? ' (NISN: ' + data.borrower_nisn + ')' : '');
                    document.getElementById('info-loan-date').textContent = data.loan_date;
                    document.getElementById('info-due-date').textContent = data.due_date;
                    document.getElementById('return-loan-id').value = data.loan_id;

                    dendaWarning.classList.add('hidden');
                    dendaOk.classList.add('hidden');

                    if (data.is_overdue) {
                        returnInfoBox.className = 'glass p-6 rounded-glass border-rose-400/30 bg-rose-500/[0.05]';
                        dendaWarning.classList.remove('hidden');
                        document.getElementById('days-late-text').textContent = data.days_late + ' hari terlambat!';
                        document.getElementById('denda-amount').textContent = data.potential_denda.toLocaleString('id-ID');
                        document.getElementById('denda-rate-text').textContent = 'Tarif: Rp' + data.denda_per_day.toLocaleString('id-ID') + '/hari';
                    } else {
                        returnInfoBox.className = 'glass p-6 rounded-glass border-emerald-400/25';
                        dendaOk.classList.remove('hidden');
                    }

                    returnInfo.classList.remove('hidden');
                    returnInfo.scrollIntoView({ behavior: 'smooth', block: 'center' });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal memeriksa data peminjaman.',
                        confirmButtonColor: '#fb5e63',
                        background: '#0b1220',
                        color: '#ffffff',
                        customClass: { popup: 'rounded-2xl border border-white/10' }
                    });
                });
            }

            function startCamera(config) {
                html5QrCode = new Html5Qrcode("reader");
                return html5QrCode.start(
                    config,
                    {
                        fps: 15,
                        qrbox: function(viewfinderWidth, viewfinderHeight) {
                            let minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                            let size = Math.floor(minEdge * 0.7);
                            return { width: size, height: size };
                        },
                        aspectRatio: 1.0,
                        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                    },
                    function onScanSuccess(decodedText) {
                        if (html5QrCode) {
                            html5QrCode.stop().then(function () {
                                readerDiv.classList.add('hidden');
                                scanPlaceholder.classList.remove('hidden');
                                btnStart.classList.remove('hidden');
                                btnStop.classList.add('hidden');
                            }).catch(function () {});
                        }
                        checkReturn(decodedText);
                    },
                    function () {}
                );
            }

            btnStart.addEventListener('click', function () {
                readerDiv.classList.remove('hidden');
                readerDiv.style.height = '70vh';
                scanPlaceholder.classList.add('hidden');
                scanArea.classList.add('scan-pulse');
                btnStart.classList.add('hidden');
                btnStop.classList.remove('hidden');
                returnInfo.classList.add('hidden');

                startCamera({ facingMode: "environment" }).catch(function () {
                    return startCamera({ facingMode: "user" });
                }).catch(function () {
                    return startCamera(true);
                }).catch(function (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kamera Gagal',
                        text: 'Tidak bisa mengakses kamera.',
                        confirmButtonColor: '#fb5e63',
                        background: '#0b1220',
                        color: '#ffffff',
                        customClass: { popup: 'rounded-2xl border border-white/10' }
                    });
                    readerDiv.classList.add('hidden');
                    scanPlaceholder.classList.remove('hidden');
                    btnStart.classList.remove('hidden');
                    btnStop.classList.add('hidden');
                });
            });

            btnStop.addEventListener('click', function () {
                if (html5QrCode) {
                    html5QrCode.stop().then(function () {
                        readerDiv.classList.add('hidden');
                        scanPlaceholder.classList.remove('hidden');
                        btnStart.classList.remove('hidden');
                        btnStop.classList.add('hidden');
                    }).catch(function () {});
                }
            });

            manualForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var isbn = document.getElementById('isbn-manual').value.trim();
                if (isbn) {
                    checkReturn(isbn);
                }
            });

            var confirmCheckbox = document.getElementById('confirm-received');
            var confirmBtn = document.getElementById('btn-confirm-return');
            if (confirmCheckbox && confirmBtn) {
                confirmCheckbox.addEventListener('change', function() {
                    confirmBtn.disabled = !this.checked;
                    if (this.checked) {
                        confirmBtn.classList.remove('opacity-50');
                    } else {
                        confirmBtn.classList.add('opacity-50');
                    }
                });
                confirmBtn.classList.add('opacity-50');
            }

            document.querySelectorAll('.return-select-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.getElementById('isbn-manual').value = btn.dataset.isbn;
                    manualForm.dispatchEvent(new Event('submit'));
                });
            });
        });
    </script>
</x-app-layout>
