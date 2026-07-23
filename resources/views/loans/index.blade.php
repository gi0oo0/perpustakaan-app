<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Peminjaman') }}
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
                <div class="flex gap-2">
                    <a href="{{ route('loans.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ !request('status') ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 border' }}">Semua</a>
                    <a href="{{ route('loans.index', ['status' => 'active']) }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request('status') === 'active' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700 border' }}">Dipinjam</a>
                    <a href="{{ route('loans.index', ['status' => 'overdue']) }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request('status') === 'overdue' ? 'bg-red-500 text-white' : 'bg-white text-gray-700 border' }}">Terlambat</a>
                    <a href="{{ route('loans.index', ['status' => 'returned']) }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request('status') === 'returned' ? 'bg-green-500 text-white' : 'bg-white text-gray-700 border' }}">Dikembalikan</a>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('loans.borrow.create') }}">
                        <x-primary-button>{{ __('Pinjam Buku') }}</x-primary-button>
                    </a>
                    <a href="{{ route('loans.return.create') }}">
                        <x-secondary-button>{{ __('Kembalikan Buku') }}</x-secondary-button>
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buku</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Pinjam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Jatuh Tempo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Kembali</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($loans as $loan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $loan->book->title ?? 'Buku dihapus' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $loan->loan_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $loan->due_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $loan->returned_at ? $loan->returned_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($loan->returned_at)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Dikembalikan</span>
                                        @elseif ($loan->due_date < \Carbon\Carbon::today())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Terlambat</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Dipinjam</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat peminjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $loans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
