<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-gradient">Pinjam Buku</h2>
            <p class="font-body text-sm text-white/45 mt-1">Scan QR atau masukkan ISBN buku yang akan dipinjam</p>
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
            {{-- Borrower Identity --}}
            <div class="glass p-6 flex items-center gap-4">
                @if (Auth::user()->profile_image)
                    <img src="{{ Auth::user()->profile_image_url }}" alt="{{ Auth::user()->name }}" class="w-14 h-14 rounded-glass-sm object-cover flex-shrink-0">
                @else
                    <span class="w-14 h-14 rounded-glass-sm bg-gradient-soft flex items-center justify-center text-xl font-display font-semibold text-white shadow-glow flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                @endif
                <div>
                    <p class="font-display font-semibold text-lg text-white">{{ Auth::user()->name }}</p>
                    <p class="font-body text-xs text-white/40">{{ Auth::user()->email }}</p>
                    @if (Auth::user()->nisn)
                        <p class="font-mono text-sm text-white/50 mt-0.5">NISN: {{ Auth::user()->nisn }}</p>
                    @endif
                </div>
            </div>

            <div class="glass p-6">
                <h3 class="font-display font-semibold text-lg text-white mb-1">Scan QR Code</h3>
                <p class="font-body text-sm text-white/40 mb-5">Aktifkan kamera lalu arahkan ke QR Code buku untuk meminjam.</p>

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

                {{-- Scan Result --}}
                <div id="scan-result" class="mt-4 hidden">
                    <div class="glass rounded-glass-sm border-emerald-400/30 px-4 py-3 font-body text-sm text-emerald-300">
                        ISBN terdeteksi: <span id="scan-result-text" class="font-mono"></span>
                    </div>
                </div>

                {{-- Manual Input + Options --}}
                <div class="mt-6 pt-6 border-t border-white/10">
                    <h4 class="font-display text-sm text-white mb-3">Atau Input Manual</h4>
                    <form id="borrow-form" method="POST" action="{{ route('loans.borrow.store') }}">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" id="isbn" name="isbn"
                                   class="glass-input flex-1 font-mono"
                                   placeholder="Masukkan ISBN..."
                                   autocomplete="off">
                            <button type="submit" class="glass-btn-primary whitespace-nowrap">Pinjam</button>
                        </div>

                        {{-- Opsi Peminjaman --}}
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-5"
                             x-data="loanOptions({{ old('duration_days', 7) }}, {{ old('denda_per_day', 500) }})">
                            <div>
                                <label class="block font-body text-xs font-medium text-white/60 mb-2">Durasi Peminjaman</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="opt in durations" :key="opt">
                                        <button type="button" @click="duration = opt"
                                                class="rounded-glass-sm px-3 py-2.5 text-sm font-medium border transition-all duration-150"
                                                :class="duration === opt
                                                    ? 'border-primary/60 bg-primary/20 text-white shadow-glow font-semibold'
                                                    : 'border-white/10 bg-white/[0.04] text-white/50 hover:bg-white/[0.08] hover:text-white'">
                                            <span x-text="opt"></span> Hari
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label class="block font-body text-xs font-medium text-white/60 mb-2">Denda Keterlambatan <span class="text-white/35">/hari</span></label>
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="rate in rates" :key="rate">
                                        <button type="button" @click="denda = rate"
                                                class="rounded-glass-sm px-3 py-2.5 text-sm font-medium border transition-all duration-150"
                                                :class="denda === rate
                                                    ? 'border-rose-400/60 bg-rose-500/15 text-white shadow-glow font-semibold'
                                                    : 'border-white/10 bg-white/[0.04] text-white/50 hover:bg-white/[0.08] hover:text-white'">
                                            <span x-text="'Rp ' + rate.toLocaleString('id-ID')"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div class="sm:col-span-2 glass-inset rounded-glass-sm px-4 py-3 flex items-center gap-3">
                                <span class="text-xl">ℹ️</span>
                                <p class="font-body text-xs text-white/55 leading-relaxed">
                                    Peminjaman <span class="text-white font-medium" x-text="duration"></span> hari —
                                    harus dikembalikan sebelum <span class="text-white font-medium" x-text="new Date(Date.now() + duration * 864e5).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></span>.
                                    Denda <span class="text-rose-300 font-medium" x-text="'Rp ' + denda.toLocaleString('id-ID')"></span>/hari bila telat.
                                </p>
                            </div>

                            <input type="hidden" name="duration_days" :value="duration">
                            <input type="hidden" name="denda_per_day" :value="denda">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Available Books --}}
        <div>
            <div class="glass p-6 h-full">
                <h3 class="font-display font-semibold text-lg text-white mb-1">Buku Tersedia</h3>
                <p class="font-body text-sm text-white/40 mb-4">Pilih buku untuk diisi otomatis</p>
                @if ($books->isEmpty())
                    <div class="text-center py-10">
                        <div class="text-4xl mb-3">📭</div>
                        <p class="font-body text-sm text-white/40">Tidak ada buku tersedia</p>
                    </div>
                @else
                    <div class="space-y-2 max-h-[640px] overflow-y-auto pr-1">
                        @foreach ($books as $book)
                            <div class="glass-inset rounded-glass-sm p-3 flex items-center gap-3 hover:bg-white/[0.07] transition-all duration-150 cursor-pointer book-card">
                                @if ($book->cover_image)
                                    <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="h-12 w-9 object-cover rounded-lg flex-shrink-0 border border-white/10">
                                @else
                                    <div class="h-12 w-9 rounded-lg bg-white/[0.06] border border-white/10 flex items-center justify-center text-lg flex-shrink-0">📖</div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-display font-medium text-sm text-white truncate">{{ $book->title }}</p>
                                    <p class="font-body text-xs text-white/40">{{ $book->author }} · Stok: {{ $book->stock }}</p>
                                </div>
                                <button type="button" data-isbn="{{ $book->isbn }}" class="glass-btn-secondary text-xs py-1.5 px-3 flex-shrink-0 book-select-btn">Pilih</button>
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
                        scanArea.classList.remove('glass-inset');
                        scanArea.classList.add('border-emerald-400/40');
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
                scanArea.classList.add('glass-inset');
                scanArea.classList.remove('border-emerald-400/40');
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

            document.querySelectorAll('.book-select-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    isbnInput.value = btn.dataset.isbn;
                    window.toast('ISBN diisi: ' + btn.dataset.isbn, 'info');
                });
            });
        });
    </script>
</x-app-layout>
