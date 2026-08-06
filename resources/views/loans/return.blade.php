<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-2xl text-text leading-tight">
            Kembalikan Buku
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-apple-lg">

            @if (session('success'))
                <div class="mb-6 bg-apple-blue text-white px-6 py-4 font-display text-sm rounded-apple-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-danger text-white px-6 py-4 font-display text-sm rounded-apple-lg">
                    Error:
                    <ul class="list-disc list-inside mt-1 font-normal">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Scan Station --}}
                <div class="lg:col-span-2">
                <div class="bg-white rounded-apple-lg p-6 mb-6 border border-apple-blue/20">
                        <h3 class="font-display font-semibold text-sm text-text mb-3">Pengembalian Buku</h3>
                        <p class="text-sm text-text-tertiary">Scan QR Code buku yang akan dikembalikan. Identitas peminjam akan otomatis terdeteksi.</p>
                    </div>

                    <div class="bg-white rounded-apple-lg p-6">
                        <h3 class="font-display font-semibold text-lg text-text mb-2">Scan QR Code</h3>
                        <p class="text-sm text-text-tertiary mb-6">Aktifkan kamera lalu arahkan ke QR Code buku yang akan dikembalikan.</p>

                        {{-- Scan Area --}}
                        <div id="scan-area" class="bg-surface-light border border-surface-lighter p-8 text-center relative overflow-hidden scan-pulse rounded-apple-lg">
                            <div id="reader" class="w-full hidden" style="height: 70vh;"></div>
                            <div id="scan-placeholder">
                                <div class="text-5xl mb-4">📷</div>
                                <p class="font-display font-semibold text-text text-lg">Menunggu Scanner...</p>
                                <p class="text-sm text-text-tertiary mt-1">Klik tombol di bawah untuk mulai</p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-6 flex gap-3">
                            <button type="button" id="btn-start-camera" class="apple-btn-primary flex-1 flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Siap Scan
                            </button>
                            <button type="button" id="btn-stop-camera" class="apple-btn-danger hidden flex-1">
                                Matikan Kamera
                            </button>
                        </div>

                        {{-- Return Info Box (hidden until scan) --}}
                        <div id="return-info" class="mt-6 hidden">
                            <div id="return-info-box" class="bg-surface-light rounded-apple-lg p-6">
                                <h4 class="font-display font-semibold text-sm text-text mb-4">Info Pengembalian</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-xs text-text-tertiary">Judul Buku</span>
                                        <p id="info-title" class="font-display font-semibold text-text">-</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-text-tertiary">Peminjam</span>
                                        <p id="info-borrower" class="font-display font-semibold text-text">-</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-text-tertiary">Tgl Pinjam</span>
                                        <p id="info-loan-date" class="font-display font-semibold text-text">-</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-text-tertiary">Jatuh Tempo</span>
                                        <p id="info-due-date" class="font-display font-semibold text-text">-</p>
                                    </div>
                                </div>

                                {{-- Denda Warning --}}
                                <div id="denda-warning" class="mt-4 hidden">
                                    <div class="bg-danger text-white px-4 py-3 font-display text-sm rounded-apple-lg">
                                        TERLAMBAT! <span id="days-late-text">-</span>
                                    </div>
                                    <div class="bg-red-50 border border-danger/20 border-t-0 px-4 py-3 rounded-b-apple-lg">
                                        <p class="font-display font-semibold text-xl text-danger">Denda: Rp <span id="denda-amount">0</span></p>
                                        <p class="text-xs text-text-tertiary mt-1">Tarif: Rp500/hari (maks Rp5.000)</p>
                                    </div>
                                </div>

                                <div id="denda-ok" class="mt-4 hidden">
                                    <div class="bg-apple-blue text-white px-4 py-3 font-display text-sm rounded-apple-lg">
                                        Tidak ada denda — dikembalikan tepat waktu!
                                    </div>
                                </div>

                                <form id="return-form" method="POST" action="{{ route('loans.return.store') }}" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="loan_id" id="return-loan-id">

                                    <div class="mb-4">
                                        <label class="flex items-start gap-3 cursor-pointer bg-yellow-50 border border-yellow-200 p-3 rounded-apple-md">
                                            <input type="checkbox" name="confirm_received" value="1" id="confirm-received"
                                                   class="mt-1 h-5 w-5 text-apple-blue border-surface-lighter focus:ring-apple-blue">
                                            <div>
                                                <p class="font-display font-semibold text-sm text-text">Buku diterima secara fisik</p>
                                                <p class="text-xs text-text-tertiary mt-1">Saya mengonfirmasi bahwa buku ini telah diterima dan dalam kondisi yang sesuai.</p>
                                            </div>
                                        </label>
                                        @error('confirm_received')
                                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" id="btn-confirm-return" class="apple-btn-primary w-full" disabled>
                                        Konfirmasi Kembalikan
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Manual Input --}}
                        <div class="mt-6 border-t border-surface-lighter pt-6">
                            <h4 class="font-display text-sm text-text mb-3">Atau Input Manual</h4>
                            <form id="manual-return-form" class="flex gap-2">
                                @csrf
                                <input type="text" id="isbn-manual" name="isbn"
                                       class="apple-input flex-1"
                                       placeholder="Masukkan ISBN..."
                                       autocomplete="off">
                                <button type="submit" class="apple-btn-primary whitespace-nowrap">Cek</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Active Loans --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-apple-lg p-6 h-full">
                        <h3 class="font-display font-semibold text-lg text-text mb-4">Sedang Dipinjam</h3>
                        @if ($activeLoans->isEmpty())
                            <div class="text-center py-8">
                                <div class="text-4xl mb-3">✅</div>
                                <p class="text-sm text-text-tertiary">Tidak ada buku dipinjam</p>
                            </div>
                        @else
                            <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                                @foreach ($activeLoans as $loan)
                                    <div class="bg-surface-light rounded-apple-md p-3 transition-colors duration-100 {{ $loan->isOverdue() ? 'bg-red-50 border border-danger/20' : '' }}">
                                        <div class="flex items-center gap-3">
                                            @if ($loan->book->cover_image)
                                                <img src="{{ asset($loan->book->cover_image) }}" alt="{{ $loan->book->title }}" class="h-12 w-9 object-cover rounded-apple-sm flex-shrink-0">
                                            @else
                                                <div class="h-12 w-9 bg-surface-lighter rounded-apple-sm flex items-center justify-center text-lg flex-shrink-0">📖</div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                            <p class="font-display font-semibold text-sm text-text truncate">{{ $loan->book->title }}</p>
                                            <p class="text-xs text-text-tertiary">{{ $loan->user->name }}{{ $loan->user->nisn ? ' (' . $loan->user->nisn . ')' : '' }}</p>
                                                <p class="text-xs {{ $loan->isOverdue() ? 'text-danger font-semibold' : 'text-text-tertiary' }}">
                                                    Tempo: {{ $loan->due_date->format('d/m/Y') }}
                                                    @if ($loan->isOverdue())
                                                        {{ $loan->getDaysLate() }}h telat
                                                    @endif
                                                </p>
                                                @if ($loan->isOverdue())
                                                    <p class="text-xs text-danger font-semibold">
                                                        Potensi denda: Rp{{ number_format($loan->getPotentialDenda(), 0, ',', '.') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <button type="button" onclick="document.getElementById('isbn-manual').value='{{ $loan->book->isbn }}'; document.getElementById('manual-return-form').dispatchEvent(new Event('submit'));"
                                            class="apple-btn-secondary w-full text-xs py-1 mt-2">Pilih untuk dikembalikan</button>
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
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: data.error, confirmButtonColor: '#E5484D' });
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
                        returnInfoBox.className = 'bg-red-50 rounded-apple-lg p-6 border border-danger/20';
                        dendaWarning.classList.remove('hidden');
                        document.getElementById('days-late-text').textContent = data.days_late + ' hari terlambat!';
                        document.getElementById('denda-amount').textContent = data.potential_denda.toLocaleString('id-ID');
                    } else {
                        returnInfoBox.className = 'bg-apple-blue/5 rounded-apple-lg p-6 border border-apple-blue/20';
                        dendaOk.classList.remove('hidden');
                    }

                    returnInfo.classList.remove('hidden');
                    returnInfo.scrollIntoView({ behavior: 'smooth', block: 'center' });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Gagal memeriksa data peminjaman.', confirmButtonColor: '#E5484D' });
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
                    Swal.fire({ icon: 'error', title: 'Kamera Gagal', text: 'Tidak bisa mengakses kamera.', confirmButtonColor: '#E5484D' });
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
        });
    </script>
</x-app-layout>
