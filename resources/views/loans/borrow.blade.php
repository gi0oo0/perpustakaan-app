<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pinjam Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Scan Barcode Buku</h3>
                        <p class="text-sm text-gray-500 mb-4">Klik tombol di bawah untuk mengaktifkan kamera, lalu arahkan ke barcode buku.</p>

                        <div class="mb-4">
                            <button type="button" id="btn-start-camera"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Buka Kamera
                            </button>
                            <button type="button" id="btn-stop-camera" class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg hidden">
                                Matikan Kamera
                            </button>
                        </div>

                        <div id="reader" class="w-full rounded-lg overflow-hidden hidden" style="height: 300px;"></div>

                        <div id="scan-result" class="mt-3 hidden">
                            <p class="text-sm text-green-600 font-semibold">Barcode terdeteksi!</p>
                            <p class="text-xs text-gray-500" id="scan-result-text"></p>
                        </div>

                        <form id="borrow-form" method="POST" action="{{ route('loans.borrow.store') }}" class="mt-4">
                            @csrf
                            <div>
                                <x-input-label for="isbn" :value="__('ISBN Buku')" />
                                <x-text-input id="isbn" name="isbn" type="text" class="mt-1 block w-full text-lg"
                                    :value="old('isbn')" placeholder="ISBN terisi otomatis dari scan..." autocomplete="off" />
                                <x-input-error :messages="$errors->get('isbn')" class="mt-2" />
                            </div>
                            <div class="mt-4">
                                <x-primary-button type="submit">{{ __('Pinjam') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Buku Tersedia</h3>
                        @if ($books->isEmpty())
                            <p class="text-gray-500 text-sm">Tidak ada buku yang tersedia untuk dipinjam.</p>
                        @else
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach ($books as $book)
                                    <div class="border rounded-lg p-3 flex items-center space-x-3 hover:bg-gray-50">
                                        @if ($book->cover_image)
                                            <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="h-12 w-9 object-cover rounded">
                                        @else
                                            <div class="h-12 w-9 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs">N/A</div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $book->title }}</p>
                                            <p class="text-xs text-gray-500">{{ $book->author }} &middot; Stok: {{ $book->stock }}</p>
                                        </div>
                                        <button type="button" onclick="document.getElementById('isbn').value='{{ $book->isbn }}'"
                                            class="text-xs text-indigo-600 hover:text-indigo-900 shrink-0">Pilih</button>
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
                        scanResultText.textContent = 'ISBN: ' + decodedText;
                        html5QrCode.stop().then(function () {
                            readerDiv.classList.add('hidden');
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
                btnStart.classList.add('hidden');
                btnStop.classList.remove('hidden');
                scanResult.classList.add('hidden');

                startCamera({ facingMode: "environment" }).catch(function () {
                    return startCamera({ facingMode: "user" });
                }).catch(function () {
                    return startCamera(true);
                }).catch(function (err) {
                    alert('Tidak bisa mengakses kamera.\n\nPastikan:\n1. Browser diizinkan akses kamera\n2. Kamera tidak dipakai aplikasi lain\n3. Gunakan HTTPS\n\nError: ' + err);
                    readerDiv.classList.add('hidden');
                    btnStart.classList.remove('hidden');
                    btnStop.classList.add('hidden');
                });
            });

            btnStop.addEventListener('click', function () {
                if (html5QrCode) {
                    html5QrCode.stop().then(function () {
                        readerDiv.classList.add('hidden');
                        btnStart.classList.remove('hidden');
                        btnStop.classList.add('hidden');
                    }).catch(function () {});
                }
            });
        });
    </script>
</x-app-layout>
