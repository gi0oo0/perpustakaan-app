<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-bold text-2xl text-border leading-tight">
            📦 Pinjam Buku
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
                    <div class="neo-card">
                        <h3 class="font-heading font-bold text-lg text-border mb-2 uppercase tracking-wide">Scan QR Code</h3>
                        <p class="font-body text-sm text-muted mb-6">Aktifkan kamera lalu arahkan ke QR Code buku untuk meminjam.</p>

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

                        {{-- Scan Result --}}
                        <div id="scan-result" class="mt-4 hidden">
                            <div class="bg-primary text-white border-3 border-border shadow-neo px-4 py-3 font-heading font-semibold text-sm">
                                ✓ Tergagal! ISBN: <span id="scan-result-text" class="font-mono"></span>
                            </div>
                        </div>

                        {{-- Manual Input --}}
                        <div class="mt-6 border-t-3 border-border pt-6">
                            <h4 class="font-heading font-semibold text-sm text-border uppercase tracking-wide mb-3">Atau Input Manual</h4>
                            <form id="borrow-form" method="POST" action="{{ route('loans.borrow.store') }}" class="flex gap-2">
                                @csrf
                                <input type="text" id="isbn" name="isbn"
                                       class="neo-input flex-1"
                                       placeholder="Masukkan ISBN..."
                                       autocomplete="off">
                                <button type="submit" class="neo-btn-primary whitespace-nowrap">→ Pinjam</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Available Books --}}
                <div class="lg:col-span-1">
                    <div class="neo-card h-full">
                        <h3 class="font-heading font-bold text-lg text-border mb-4 uppercase tracking-wide">📖 Buku Tersedia</h3>
                        @if ($books->isEmpty())
                            <div class="text-center py-8">
                                <div class="text-4xl mb-3">📭</div>
                                <p class="font-body text-sm text-muted">Tidak ada buku tersedia</p>
                            </div>
                        @else
                            <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                                @foreach ($books as $book)
                                    <div class="border-2 border-border p-3 flex items-center gap-3 hover:bg-lemon-50 transition-colors duration-100 cursor-pointer book-card" style="box-shadow: 3px 3px 0px #111827;">
                                        @if ($book->cover_image)
                                            <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="h-12 w-9 object-cover border-2 border-border flex-shrink-0">
                                        @else
                                            <div class="h-12 w-9 bg-gray-100 border-2 border-border flex items-center justify-center text-lg flex-shrink-0">📖</div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="font-heading font-semibold text-sm text-border truncate">{{ $book->title }}</p>
                                            <p class="font-body text-xs text-muted">{{ $book->author }} · Stok: {{ $book->stock }}</p>
                                        </div>
                                        <button type="button" onclick="document.getElementById('isbn').value='{{ $book->isbn }}'"
                                            class="neo-btn-secondary text-xs py-1 px-2 flex-shrink-0">Pilih</button>
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
            const isbnInput = document.getElementById('isbn');
            const borrowForm = document.getElementById('borrow-form');
            const btnStart = document.getElementById('btn-start-camera');
            const btnStop = document.getElementById('btn-stop-camera');
            const readerDiv = document.getElementById('reader');
            const scanResult = document.getElementById('scan-result');
            const scanResultText = document.getElementById('scan-result-text');
            const scanPlaceholder = document.getElementById('scan-placeholder');
            const scanArea = document.getElementById('scan-area');

            let html5QrCode = null;

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
                        isbnInput.value = decodedText;
                        scanResult.classList.remove('hidden');
                        scanResultText.textContent = decodedText;
                        scanArea.classList.remove('scan-pulse');
                        scanArea.classList.remove('bg-lemon');
                        scanArea.classList.add('bg-primary');
                        html5QrCode.stop().then(function () {
                            readerDiv.classList.add('hidden');
                            scanPlaceholder.classList.remove('hidden');
                            btnStart.classList.remove('hidden');
                            btnStop.classList.add('hidden');
                        }).catch(function () {});
                        setTimeout(function () { borrowForm.submit(); }, 500);
                    },
                    function () {}
                );
            }

            btnStart.addEventListener('click', function () {
                readerDiv.classList.remove('hidden');
                readerDiv.style.height = '70vh';
                scanPlaceholder.classList.add('hidden');
                scanArea.classList.add('scan-pulse');
                scanArea.classList.remove('bg-primary');
                scanArea.classList.add('bg-lemon');
                btnStart.classList.add('hidden');
                btnStop.classList.remove('hidden');
                scanResult.classList.add('hidden');

                startCamera({ facingMode: "environment" }).catch(function () {
                    return startCamera({ facingMode: "user" });
                }).catch(function () {
                    return startCamera(true);
                }).catch(function (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kamera Gagal',
                        html: 'Tidak bisa mengakses kamera.<br><br><small>Pastikan:<br>1. Browser diizinkan akses kamera<br>2. Kamera tidak dipakai aplikasi lain<br>3. Gunakan HTTPS</small>',
                        confirmButtonColor: '#FF6B6B'
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
        });
    </script>
</x-app-layout>
