<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <form method="GET" action="{{ route('books.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari buku..."
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <x-primary-button>{{ __('Cari') }}</x-primary-button>
                </form>
                <div class="flex gap-2">
                    <a href="{{ route('books.create') }}">
                        <x-primary-button>{{ __('Tambah Buku') }}</x-primary-button>
                    </a>
                    <a href="{{ route('books.print-label-batch') }}" target="_blank">
                        <x-secondary-button>{{ __('Cetak Label Semua') }}</x-secondary-button>
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sampul</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ISBN</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($books as $book)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($book->cover_image)
                                            <img src="{{ asset($book->cover_image) }}" alt="{{ $book->title }}" class="h-14 w-10 object-cover rounded">
                                        @else
                                            <div class="h-14 w-10 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs">No</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <img src="data:image/png;base64,{{ \Milon\Barcode\Facades\DNS2DFacade::getBarcodePNG($book->isbn, 'QRCODE', 4, 4) }}" alt="QR" class="h-16 w-16">
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $book->isbn }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $book->title }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $book->author }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $book->stock }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('books.show', $book) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        <a href="{{ route('books.edit', $book) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                        <a href="{{ route('books.print-label', $book) }}" target="_blank" class="text-green-600 hover:text-green-900">Cetak</a>
                                        <form action="{{ route('books.destroy', $book) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada buku.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $books->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
