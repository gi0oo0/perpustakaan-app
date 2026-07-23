<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Total Buku</div>
                        <div class="mt-1 text-3xl font-bold text-gray-900">{{ \App\Models\Book::count() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Sedang Dipinjam</div>
                        <div class="mt-1 text-3xl font-bold text-yellow-600">{{ \App\Models\Loan::whereNull('returned_at')->count() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500">Terlambat Dikembalikan</div>
                        <div class="mt-1 text-3xl font-bold text-red-600">{{ \App\Models\Loan::whereNull('returned_at')->where('due_date', '<', \Carbon\Carbon::today())->count() }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Akses Cepat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('books.index') }}" class="block p-4 border rounded-lg hover:bg-gray-50 text-center">
                            <div class="text-2xl mb-2">&#128214;</div>
                            <div class="font-medium text-gray-900">Lihat Daftar Buku</div>
                        </a>
                        <a href="{{ route('loans.borrow.create') }}" class="block p-4 border rounded-lg hover:bg-gray-50 text-center">
                            <div class="text-2xl mb-2">&#128179;</div>
                            <div class="font-medium text-gray-900">Pinjam Buku</div>
                        </a>
                        <a href="{{ route('loans.return.create') }}" class="block p-4 border rounded-lg hover:bg-gray-50 text-center">
                            <div class="text-2xl mb-2">&#128260;</div>
                            <div class="font-medium text-gray-900">Kembalikan Buku</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
