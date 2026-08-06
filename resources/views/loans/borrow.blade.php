<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-heading-lg text-text leading-tight">
            Pinjam Buku
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
                    {{-- Borrower Identity --}}
                    <div class="bg-white rounded-apple-lg p-6 mb-6 border border-apple-blue/20">
                        <h3 class="font-display font-semibold text-sm text-text mb-3">Identitas Peminjam</h3>
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-apple-blue rounded-full flex items-center justify-center text-2xl font-display font-semibold text-white flex-shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-display font-semibold text-lg text-text">{{ Auth::user()->name }}</p>
                                @if (Auth::user()->nisn)
                                    <p class="font-mono text-sm text-text-tertiary">NISN: {{ Auth::user()->nisn }}</p>
                                @endif
                                <p class="text-xs text-text-tertiary">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-apple-lg p-6">
                        <h3 class="font-display font-semibold text-lg text-text mb-2">Scan QR Code</h3>
                        <p class="text-sm text-text-tertiary mb-6">Aktifkan kamera lalu arahkan ke QR Code buku untuk meminjam.</p>

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

                        {{-- Scan Result --}}
                        <div id="scan-result" class="mt-4 hidden">
                            <div class="bg-apple-blue text-white px-4 py-3 font-display text-sm rounded-apple-lg">
                                Tergagal! ISBN: <span id="scan-result-text" class="font-mono"></span>
                            </div>
                        </div>

                        {{-- Manual Input --}}
                        <div class="mt-6 border-t border-surface-lighter pt-6">
                            <h4 class="font-display text-sm text-text mb-3">Atau Input Manual</h4>
                            <form id="borrow-form" method="POST" action="{{ route('loans.borrow.store') }}" class="flex gap-2">
                                @csrf
                                <input type="text" id="isbn" name="isbn"
                                       class="apple-input flex-1"
                                       placeholder="Masukkan ISBN..."
                                       autocomplete="off">
                                <button type="submit" class="apple-btn-primary whitespace-nowrap">Pinjam</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Available Books --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-apple-lg p-6 h-full">
                        <h3 class="font-display font-semibold text-lg text-text mb-4">Buku Tersedia</h3>
                        @if ($books->isEmpty())
                            <div class="text-center py-8">
                                <div class="text-4xl mb-3">📭</div>
                                <p class="text-sm text-text-tertiary">Tidak ada buku tersedia</p>
                            </div>
                        @else
                            <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                                @foreach ($books as $book)
                                    <div class="bg-surface-light rounded-apple-md p-3 flex items-center gap-3 hover:bg-surface-lighter transition-colors duration-100 cursor-pointer book-card">
                                        @if ($book->cover_image)
                                            <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="h-12 w-9 object-cover rounded-apple-sm flex-shrink-0">
                                        @else
                                            <div class="h-12 w-9 bg-surface-lighter rounded-apple-sm flex items-center justify-center text-lg flex-shrink-0">📖</div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="font-display font-semibold text-sm text-text truncate">{{ $book->title }}</p>
                                            <p class="text-xs text-text-tertiary">{{ $book->author }} · Stok: {{ $book->stock }}</p>
                                        </div>
                                        <button type="button" data-isbn="{{ $book->isbn }}" class="apple-btn-secondary text-xs py-1 px-2 flex-shrink-0 book-select-btn">Pilih</button>
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
                        scanArea.classList.remove('bg-surface-light');
                        scanArea.classList.add('bg-apple-blue');
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
                scanArea.classList.remove('bg-apple-blue');
                scanArea.classList.add('bg-surface-light');
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
                        confirmButtonColor: '#E5484D'
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

            document.querySelectorAll('.book-select-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    isbnInput.value = btn.dataset.isbn;
                });
            });
        });
    </script>
</x-app-layout>
