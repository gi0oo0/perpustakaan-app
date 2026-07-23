<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            @if ($book->cover_image)
                                <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="w-full max-w-xs rounded-lg shadow">
                            @else
                                <div class="w-full max-w-xs h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">No Image</div>
                            @endif
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $book->title }}</h3>

                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ISBN</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $book->isbn }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Penulis</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $book->author }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Penerbit</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $book->publisher ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tahun Terbit</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $book->publication_year ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Stok Tersedia</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="font-semibold {{ $book->stock > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $book->stock }}</span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Sedang Dipinjam</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $activeLoans }}</dd>
                                </div>
                            </dl>

                            @if ($book->description)
                                <div class="mt-6">
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">Deskripsi</h4>
                                    <p class="text-sm text-gray-900">{{ $book->description }}</p>
                                </div>
                            @endif

                            <div class="mt-6 flex space-x-3">
                                <a href="{{ route('books.edit', $book) }}">
                                    <x-primary-button>{{ __('Edit') }}</x-primary-button>
                                </a>
                                <a href="{{ route('books.print-label', $book) }}" target="_blank">
                                    <x-secondary-button>{{ __('Cetak Label') }}</x-secondary-button>
                                </a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button>{{ __('Hapus') }}</x-danger-button>
                                </form>
                            </div>

                            <div class="mt-6 p-4 border rounded-lg bg-gray-50">
                                <h4 class="text-sm font-medium text-gray-500 mb-2">QR Code</h4>
                                <img src="data:image/png;base64,{{ \Milon\Barcode\Facades\DNS2DFacade::getBarcodePNG($book->isbn, 'QRCODE', 5, 5) }}" alt="QR Code {{ $book->isbn }}">
                                <p class="text-xs text-gray-400 mt-1">{{ $book->isbn }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
