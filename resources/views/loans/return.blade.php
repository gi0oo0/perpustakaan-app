<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-[25px] font-semibold tracking-tight text-white leading-tight">Kembalikan Buku</h2>
            <p class="font-body text-[13px] text-[#92989D] mt-1">Scan atau masukkan kode buku untuk memproses pengembalian.</p>
        </div>
    </x-slot>

    <style>
        .scan-corner {
            position: absolute;
            width: 24px;
            height: 24px;
            border: 2px solid rgba(45, 183, 168, 0.85);
            opacity: 0;
            transition: opacity 180ms ease;
            pointer-events: none;
        }
        .scan-corner-tl { top: 16px; left: 16px; border-right: none; border-bottom: none; border-top-left-radius: 8px; }
        .scan-corner-tr { top: 16px; right: 16px; border-left: none; border-bottom: none; border-top-right-radius: 8px; }
        .scan-corner-bl { bottom: 16px; left: 16px; border-right: none; border-top: none; border-bottom-left-radius: 8px; }
        .scan-corner-br { bottom: 16px; right: 16px; border-left: none; border-top: none; border-bottom-right-radius: 8px; }
        #scan-area.scanning .scan-corner { opacity: 1; }

        #scan-active-hint {
            opacity: 0;
            transition: opacity 200ms ease;
            pointer-events: none;
        }
        #scan-area.scanning #scan-active-hint { opacity: 1; }

        .scan-pulse::before,
        .scan-pulse::after { display: none !important; }

        #reader video { width: 100% !important; height: 100% !important; object-fit: cover !important; }
        #reader #qr-shaded-region { display: none !important; }
        #reader__dashboard,
        #reader__dashboard_section { display: none !important; }

        .return-item.selected {
            background-color: rgba(45, 183, 168, 0.07);
            border-color: rgba(45, 183, 168, 0.35);
        }
    </style>

    @if ($errors->any())
        <div class="mb-6 rounded-[10px] border border-rose-400/30 bg-rose-500/10 px-5 py-4">
            <p class="font-display text-sm font-semibold text-rose-300 mb-1">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li class="font-body text-sm text-rose-200/80">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-[1120px] mx-auto grid grid-cols-1 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] gap-5 lg:items-start" x-data="reveal">
        {{-- ============ LEFT: RETURN DESK ============ --}}
        <div class="glass rounded-[12px] p-6">
            <h3 class="font-display text-[15px] font-semibold text-white">Kembalikan buku</h3>
            <p class="font-body text-[13px] text-[#92989D] mt-1">Scan QR code buku untuk memproses pengembalian secara cepat.</p>

            {{-- Scanner Viewport --}}
            <div id="scan-area" class="relative mt-6 h-[300px] overflow-hidden rounded-[10px] border border-dashed border-white/[0.12] bg-[#151719]">
                <div id="reader" class="absolute inset-0 hidden"></div>

                <div id="scan-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-center px-6">
                    <span class="flex items-center justify-center w-12 h-12 rounded-full bg-white/[0.04] border border-white/[0.06] mb-4">
                        <svg class="w-5 h-5 text-white/45" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </span>
                    <p class="font-display text-[15px] font-semibold text-white">Scanner belum aktif</p>
                    <p class="font-body text-[13px] text-[#92989D] mt-1.5">Klik tombol di bawah untuk mulai memindai.</p>
                </div>

                <span class="scan-corner scan-corner-tl"></span>
                <span class="scan-corner scan-corner-tr"></span>
                <span class="scan-corner scan-corner-bl"></span>
                <span class="scan-corner scan-corner-br"></span>

                <div id="scan-active-hint" class="absolute bottom-3 left-0 right-0 text-center">
                    <span class="inline-flex items-center gap-2 rounded-full bg-black/45 px-3 py-1.5 text-[11px] text-white/70">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#2DB7A8]"></span>
                        Memindai... Arahkan QR code ke dalam kotak
                    </span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-5 flex justify-center gap-3">
                <button type="button" id="btn-start-camera"
                        class="inline-flex items-center justify-center gap-2 h-[42px] w-[172px] rounded-[9px] bg-[#2DB7A8] text-[#071311] font-semibold text-sm hover:bg-[#2FBFB0] focus:outline-none transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Mulai Scan
                </button>
                <button type="button" id="btn-stop-camera"
                        class="hidden inline-flex items-center justify-center gap-2 h-[42px] w-[172px] rounded-[9px] bg-[#202427] border border-white/[0.07] text-[#A5ADB3] font-medium text-sm hover:bg-[#252A2E] hover:text-white focus:outline-none transition-colors">
                    Berhenti Scan
                </button>
            </div>

            {{-- Manual Input --}}
            <div class="mt-6 pt-6 border-t border-white/[0.06]">
                <p class="font-body text-[13px] text-white/60 mb-3">Masukkan ISBN secara manual</p>
                <form id="manual-return-form" class="flex gap-2" data-no-auto-loading>
                    @csrf
                    <input type="text" id="isbn-manual" name="isbn"
                           class="glass-input flex-1 font-mono !rounded-[9px]"
                           placeholder="Masukkan ISBN..."
                           autocomplete="off">
                    <button type="submit"
                            class="inline-flex items-center justify-center h-[42px] px-5 rounded-[9px] bg-[#2DB7A8] text-[#071311] font-semibold text-sm hover:bg-[#2FBFB0] transition-colors whitespace-nowrap">
                        Cari
                    </button>
                </form>
                <p class="font-body text-xs text-[#92989D]/70 mt-2">Gunakan opsi ini jika QR code tidak dapat dipindai.</p>

                <div id="check-status" class="hidden mt-3 flex items-center gap-2">
                    <x-loading-spinner size="16" stroke="2.5" />
                    <span class="font-body text-xs text-[#A5ADB3]">Memeriksa data peminjaman...</span>
                </div>
            </div>

            {{-- Return Info (hidden until scanned) --}}
            <div id="return-info" class="mt-6 hidden">
                <div id="return-info-box" class="rounded-[10px] border border-white/[0.07] bg-white/[0.02] p-5">
                    <p class="font-display text-[11px] font-semibold text-[#2DB7A8] uppercase tracking-wider mb-4">Buku dipilih</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                        <div class="sm:col-span-2">
                            <span class="block font-body text-[11px] text-[#92989D] uppercase tracking-wider">Judul Buku</span>
                            <p id="info-title" class="font-display font-semibold text-[15px] text-white mt-0.5">-</p>
                        </div>
                        <div>
                            <span class="block font-body text-[11px] text-[#92989D] uppercase tracking-wider">Peminjam</span>
                            <p id="info-borrower" class="font-body text-sm text-white mt-0.5">-</p>
                        </div>
                        <div>
                            <span class="block font-body text-[11px] text-[#92989D] uppercase tracking-wider">Tanggal Jatuh Tempo</span>
                            <p id="info-due-date" class="font-body text-sm text-white mt-0.5">-</p>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="block font-body text-[11px] text-[#92989D] uppercase tracking-wider">Tgl Pinjam</span>
                            <p id="info-loan-date" class="font-body text-sm text-[#92989D] mt-0.5">-</p>
                        </div>
                    </div>

                    {{-- Denda Warning --}}
                    <div id="denda-warning" class="mt-4 hidden">
                        <div class="rounded-[9px] border border-rose-400/30 bg-rose-500/10 px-4 py-3">
                            <p class="font-display text-sm font-semibold text-rose-300">TERLAMBAT! <span id="days-late-text">-</span></p>
                            <p class="font-display text-lg font-bold text-rose-300 mt-1">Denda: Rp <span id="denda-amount">0</span></p>
                            <p class="font-body text-xs text-white/40 mt-1" id="denda-rate-text">Tarif: Rp500/hari</p>
                        </div>
                    </div>

                    <div id="denda-ok" class="mt-4 hidden">
                        <div class="rounded-[9px] border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 font-body text-sm text-emerald-300">
                            &#10003; Tidak ada denda — dikembalikan tepat waktu!
                        </div>
                    </div>

                    <form id="return-form" method="POST" action="{{ route('loans.return.store') }}" class="mt-5">
                        @csrf
                        <input type="hidden" name="loan_id" id="return-loan-id">

                        <label class="flex items-start gap-3 cursor-pointer rounded-[9px] border border-white/[0.07] bg-white/[0.02] p-3.5 transition-colors hover:bg-white/[0.03] mb-4">
                            <input type="checkbox" name="confirm_received" value="1" id="confirm-received"
                                   class="mt-0.5 h-5 w-5 rounded bg-white/[0.06] border-white/15 text-primary focus:ring-primary/40">
                            <div>
                                <p class="font-display font-semibold text-sm text-white">Buku diterima secara fisik</p>
                                <p class="font-body text-xs text-[#92989D] mt-0.5">Saya mengonfirmasi bahwa buku ini telah diterima dan dalam kondisi yang sesuai.</p>
                            </div>
                        </label>
                        @error('confirm_received')
                            <p class="font-body text-xs text-rose-300 mb-2">{{ $message }}</p>
                        @enderror

                        <button type="submit" id="btn-confirm-return"
                                class="w-full h-[42px] inline-flex items-center justify-center rounded-[9px] bg-[#2DB7A8] text-[#071311] font-semibold text-sm hover:bg-[#2FBFB0] transition-colors" disabled>
                            Konfirmasi Pengembalian
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ============ RIGHT: SEDANG DIPINJAM ============ --}}
        <div class="glass rounded-[12px] p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-display text-[15px] font-semibold text-white">Sedang Dipinjam</h3>
                    <p class="font-body text-[13px] text-[#92989D] mt-0.5">Buku yang saat ini sedang dipinjam</p>
                </div>
                <span class="rounded-full bg-white/[0.05] border border-white/[0.06] px-2.5 py-1 text-xs font-semibold text-white/70 flex-shrink-0">{{ $activeLoans->count() }} buku</span>
            </div>

            @if ($activeLoans->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-9 h-9 mx-auto mb-3 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
                    <p class="font-display text-sm font-medium text-white/70">Tidak ada peminjaman aktif</p>
                    <p class="font-body text-xs text-[#92989D] mt-1">Belum ada buku yang sedang dipinjam.</p>
                </div>
            @else
                <div class="mt-4 space-y-1.5 max-h-[640px] overflow-y-auto pr-1">
                    @foreach ($activeLoans as $loan)
                        <div class="return-item rounded-[9px] border border-transparent p-2.5 transition-colors duration-150 hover:bg-white/[0.02]">
                            <div class="flex items-center gap-3">
                                @if ($loan->book->cover_url)
                                    <img src="{{ $loan->book->cover_url }}" alt="{{ $loan->book->title }}" class="h-11 w-8 object-cover rounded-[6px] flex-shrink-0 border border-white/[0.05]">
                                @else
                                    @php($coverColor = ['#2E3B4E', '#3A5A53', '#4E3A44', '#52543A', '#39425C', '#5A4636', '#4A4359', '#3E4A48'][$loan->book->id % 8])
                                    <div class="h-11 w-8 rounded-[6px] border border-white/[0.05] flex-shrink-0" style="background-color: {{ $coverColor }};"></div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-display font-medium text-[13px] text-white truncate">{{ $loan->book->title }}</p>
                                    <p class="font-body text-xs text-[#92989D] truncate">{{ $loan->user->name }}{{ $loan->user->nisn ? ' (' . $loan->user->nisn . ')' : '' }}</p>
                                    <p class="font-body text-xs {{ $loan->isOverdue() ? 'text-[#E76B73] font-medium' : 'text-[#92989D]' }}">
                                        Tempo: {{ $loan->due_date->format('d/m/Y') }}
                                        @if ($loan->isOverdue())
                                            · {{ $loan->getDaysLate() }}h telat
                                        @endif
                                    </p>
                                    @if ($loan->isOverdue())
                                        <p class="font-body text-xs text-[#E76B73] font-semibold mt-0.5">
                                            Potensi denda: Rp{{ number_format($loan->getPotentialDenda(), 0, ',', '.') }} (Rp{{ number_format($loan->getDendaPerDay(), 0, ',', '.') }}/hari)
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2 flex justify-end">
                                <button type="button" data-isbn="{{ $loan->book->isbn }}"
                                        class="return-select-btn inline-flex items-center justify-center h-[30px] px-3.5 rounded-[7px] bg-[#202427] border border-white/[0.07] text-xs font-medium text-[#A5ADB3] hover:bg-[#252A2E] hover:text-white transition-colors">
                                    Pilih
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
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
            const checkStatus = document.getElementById('check-status');
            const manualSubmitBtn = document.querySelector('#manual-return-form button[type="submit"]');

            let html5QrCode = null;

            function checkReturn(isbn) {
                checkStatus.classList.remove('hidden');
                if (manualSubmitBtn) manualSubmitBtn.disabled = true;
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
                        returnInfoBox.className = 'rounded-[10px] border border-rose-400/30 bg-rose-500/10 p-5';
                        dendaWarning.classList.remove('hidden');
                        document.getElementById('days-late-text').textContent = data.days_late + ' hari terlambat!';
                        document.getElementById('denda-amount').textContent = data.potential_denda.toLocaleString('id-ID');
                        document.getElementById('denda-rate-text').textContent = 'Tarif: Rp' + data.denda_per_day.toLocaleString('id-ID') + '/hari';
                    } else {
                        returnInfoBox.className = 'rounded-[10px] border border-emerald-400/25 bg-emerald-400/10 p-5';
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
                })
                .finally(function () {
                    checkStatus.classList.add('hidden');
                    if (manualSubmitBtn) manualSubmitBtn.disabled = false;
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
                        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                    },
                    function onScanSuccess(decodedText) {
                        if (html5QrCode) {
                            html5QrCode.stop().then(function () {
                                readerDiv.classList.add('hidden');
                                scanPlaceholder.classList.remove('hidden');
                                scanArea.classList.remove('scanning');
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
                scanPlaceholder.classList.add('hidden');
                scanArea.classList.add('scanning');
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
                    scanArea.classList.remove('scanning');
                    btnStart.classList.remove('hidden');
                    btnStop.classList.add('hidden');
                });
            });

            btnStop.addEventListener('click', function () {
                if (html5QrCode) {
                    html5QrCode.stop().then(function () {
                        readerDiv.classList.add('hidden');
                        scanPlaceholder.classList.remove('hidden');
                        scanArea.classList.remove('scanning');
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
                    document.querySelectorAll('.return-item').forEach(function (el) {
                        el.classList.remove('selected');
                    });
                    var item = btn.closest('.return-item');
                    if (item) item.classList.add('selected');

                    document.getElementById('isbn-manual').value = btn.dataset.isbn;
                    manualForm.dispatchEvent(new Event('submit'));
                });
            });
        });
    </script>
</x-app-layout>