<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            🔄 Kembalikan Buku
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-primary text-white border-3 border-border shadow-neo px-6 py-4 font-heading font-semibold uppercase tracking-wide text-sm">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-coral text-white border-3 border-border shadow-neo px-6 py-4 font-heading font-semibold uppercase tracking-wide text-sm">
                    ✗ Error:
                    <ul class="list-disc list-inside mt-1 font-body font-normal normal-case">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Scan Station --}}
                <div class="lg:col-span-2">
                <div class="neo-card mb-6 border-3 border-primary">
                        <h3 class="font-heading font-bold text-sm text-border mb-3 uppercase tracking-wide">🔄 Pengembalian Buku</h3>
                        <p class="font-body text-sm text-muted">Scan QR Code buku yang akan dikembalikan. Identitas peminjam akan otomatis terdeteksi.</p>
                    </div>

                    <div class="neo-card">
                        <h3 class="font-heading font-bold text-lg text-border mb-2 uppercase tracking-wide">Scan QR Code</h3>
                        <p class="font-body text-sm text-muted mb-6">Aktifkan kamera lalu arahkan ke QR Code buku yang akan dikembalikan.</p>

                        {{-- Scan Area --}}
                        <div id="scan-area" class="bg-lemon border-3 border-dashed border-border p-8 text-center relative overflow-hidden scan-pulse">
                            <div id="reader" class="w-full hidden" style="height: 70vh;"></div>
                            <div id="scan-placeholder">
                                <div class="text-5xl mb-4">📷</div>
                                <p class="font-heading font-semibold text-border text-lg">Menunggu Scanner...</p>
                                <p class="font-body text-sm text-muted mt-1">Klik tombol di bawah untuk mulai</p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-6 flex gap-3">
                            <button type="button" id="btn-start-camera" class="neo-btn-primary flex-1 flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Siap Scan
                            </button>
                            <button type="button" id="btn-stop-camera" class="neo-btn-danger hidden flex-1">
                                ✕ Matikan Kamera
                            </button>
                        </div>

                        {{-- Return Info Box (hidden until scan) --}}
                        <div id="return-info" class="mt-6 hidden">
                            <div id="return-info-box" class="border-3 border-border shadow-neo p-6">
                                <h4 class="font-heading font-bold text-sm text-border uppercase tracking-wide mb-4">📦 Info Pengembalian</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="font-heading text-xs uppercase text-muted">Judul Buku</span>
                                        <p id="info-title" class="font-body font-semibold text-border">-</p>
                                    </div>
                                    <div>
                                        <span class="font-heading text-xs uppercase text-muted">Peminjam</span>
                                        <p id="info-borrower" class="font-body font-semibold text-border">-</p>
                                    </div>
                                    <div>
                                        <span class="font-heading text-xs uppercase text-muted">Tgl Pinjam</span>
                                        <p id="info-loan-date" class="font-body font-semibold text-border">-</p>
                                    </div>
                                    <div>
                                        <span class="font-heading text-xs uppercase text-muted">Jatuh Tempo</span>
                                        <p id="info-due-date" class="font-body font-semibold text-border">-</p>
                                    </div>
                                </div>

                                {{-- Denda Warning --}}
                                <div id="denda-warning" class="mt-4 hidden">
                                    <div class="bg-coral text-white border-3 border-border px-4 py-3 font-heading font-semibold text-sm">
                                        ⚠ TERLAMBAT! <span id="days-late-text">-</span>
                                    </div>
                                    <div class="bg-red-50 border-3 border-coral border-t-0 px-4 py-3">
                                        <p class="font-heading font-bold text-xl text-coral">Denda: Rp <span id="denda-amount">0</span></p>
                                        <p class="font-body text-xs text-muted mt-1">Tarif: Rp500/hari (maks Rp5.000)</p>
                                    </div>
                                </div>

                                <div id="denda-ok" class="mt-4 hidden">
                                    <div class="bg-primary text-white border-3 border-border px-4 py-3 font-heading font-semibold text-sm">
                                        ✓ Tidak ada denda — dikembalikan tepat waktu!
                                    </div>
                                </div>

                                <form id="return-form" method="POST" action="{{ route('loans.return.store') }}" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="loan_id" id="return-loan-id">
                                    <button type="submit" id="btn-confirm-return" class="neo-btn-primary w-full">
                                        ✓ Konfirmasi Kembalikan
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Manual Input --}}
                        <div class="mt-6 border-t-3 border-border pt-6">
                            <h4 class="font-heading font-semibold text-sm text-border uppercase tracking-wide mb-3">Atau Input Manual</h4>
                            <form id="manual-return-form" class="flex gap-2">
                                @csrf
                                <input type="text" id="isbn-manual" name="isbn"
                                       class="neo-input flex-1"
                                       placeholder="Masukkan ISBN..."
                                       autocomplete="off">
                                <button type="submit" class="neo-btn-primary whitespace-nowrap">→ Cek</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Active Loans --}}
                <div class="lg:col-span-1">
                    <div class="neo-card h-full">
                        <h3 class="font-heading font-bold text-lg text-border mb-4 uppercase tracking-wide">📦 Sedang Dipinjam</h3>
                        @if ($activeLoans->isEmpty())
                            <div class="text-center py-8">
                                <div class="text-4xl mb-3">✅</div>
                                <p class="font-body text-sm text-muted">Tidak ada buku dipinjam</p>
                            </div>
                        @else
                            <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                                @foreach ($activeLoans as $loan)
                                    <div class="border-2 border-border p-3 {{ $loan->isOverdue() ? 'bg-red-50 border-coral' : '' }} transition-colors duration-100" style="box-shadow: 3px 3px 0px #111827;">
                                        <div class="flex items-center gap-3">
                                            @if ($loan->book->cover_image)
                                                <img src="{{ asset($loan->book->cover_image) }}" alt="{{ $loan->book->title }}" class="h-12 w-9 object-cover border-2 border-border flex-shrink-0">
                                            @else
                                                <div class="h-12 w-9 bg-gray-100 border-2 border-border flex items-center justify-center text-lg flex-shrink-0">📖</div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                            <p class="font-heading font-semibold text-sm text-border truncate">{{ $loan->book->title }}</p>
                                            <p class="font-body text-xs text-muted">{{ $loan->user->name }}{{ $loan->user->nisn ? ' (' . $loan->user->nisn . ')' : '' }}</p>
                                                <p class="font-body text-xs {{ $loan->isOverdue() ? 'text-coral font-semibold' : 'text-muted' }}">
                                                    Tempo: {{ $loan->due_date->format('d/m/Y') }}
                                                    @if ($loan->isOverdue())
                                                        ⚠ {{ $loan->getDaysLate() }}h telat
                                                    @endif
                                                </p>
                                                @if ($loan->isOverdue())
                                                    <p class="font-body text-xs text-coral font-bold">
                                                        Potensi denda: Rp{{ number_format($loan->getPotentialDenda(), 0, ',', '.') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <button type="button" onclick="document.getElementById('isbn-manual').value='{{ $loan->book->isbn }}'; document.getElementById('manual-return-form').dispatchEvent(new Event('submit'));"
                                            class="neo-btn-secondary w-full text-xs py-1 mt-2">Pilih untuk dikembalikan</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

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
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: data.error, confirmButtonColor: '#FF6B6B' });
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
                        returnInfoBox.className = 'border-3 border-coral shadow-neo p-6 bg-red-50';
                        dendaWarning.classList.remove('hidden');
                        document.getElementById('days-late-text').textContent = data.days_late + ' hari terlambat!';
                        document.getElementById('denda-amount').textContent = data.potential_denda.toLocaleString('id-ID');
                    } else {
                        returnInfoBox.className = 'border-3 border-primary shadow-neo p-6 bg-primary-50';
                        dendaOk.classList.remove('hidden');
                    }

                    returnInfo.classList.remove('hidden');
                    returnInfo.scrollIntoView({ behavior: 'smooth', block: 'center' });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Gagal memeriksa data peminjaman.', confirmButtonColor: '#FF6B6B' });
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
                    Swal.fire({ icon: 'error', title: 'Kamera Gagal', text: 'Tidak bisa mengakses kamera.', confirmButtonColor: '#FF6B6B' });
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
        });
    </script>
</x-app-layout>
