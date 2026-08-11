<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBooks();

        $this->seedMemberLoans();

        $this->command->info('Demo data siap: buku & pinjaman contoh telah dibuat.');
    }

    private function seedBooks(): void
    {
        if (Book::count() > 0) {
            return;
        }

        $books = [
            ['isbn' => '978-602-424-369-0', 'title' => 'Menatap Senja di Ujung Kota', 'author' => 'Tere Liye', 'publisher' => 'Republika', 'publication_year' => 2020, 'kategori' => 'Novel', 'stock' => 6],
            ['isbn' => '978-979-3062-79-2', 'title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'publisher' => 'Bentang Pustaka', 'publication_year' => 2005, 'kategori' => 'Novel', 'stock' => 8],
            ['isbn' => '978-979-9932-11-5', 'title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'publisher' => 'Hasta Mitra', 'publication_year' => 1980, 'kategori' => 'Sejarah', 'stock' => 5],
            ['isbn' => '978-602-413-068-4', 'title' => 'Pendidikan Pancasila', 'author' => 'Kemendikbud', 'publisher' => 'Kemendikbud', 'publication_year' => 2017, 'kategori' => 'Pendidikan', 'stock' => 12],
            ['isbn' => '978-602-427-105-3', 'title' => 'Matematika Kelas XI', 'author' => 'Sutanto', 'publisher' => 'Erlangga', 'publication_year' => 2019, 'kategori' => 'Pendidikan', 'stock' => 10],
            ['isbn' => '978-602-1246-30-6', 'title' => 'Fisika Dasar', 'author' => 'Siswanto', 'publisher' => 'Grasindo', 'publication_year' => 2018, 'kategori' => 'Sains & Teknologi', 'stock' => 7],
            ['isbn' => '978-623-00-1151-6', 'title' => 'Bahasa Inggris untuk SMK', 'author' => 'Dewi Rahayu', 'publisher' => 'Bumi Aksara', 'publication_year' => 2021, 'kategori' => 'Pendidikan', 'stock' => 9],
            ['isbn' => '978-979-22-6602-4', 'title' => 'Kimia Organik', 'author' => 'Hartono', 'publisher' => 'Penerbit ITB', 'publication_year' => 2016, 'kategori' => 'Sains & Teknologi', 'stock' => 6],
            ['isbn' => '978-602-03-2734-1', 'title' => 'Sejarah Indonesia', 'author' => 'Ricklefs', 'publisher' => 'Gramedia', 'publication_year' => 2008, 'kategori' => 'Sejarah', 'stock' => 4],
        ];

        foreach ($books as $b) {
            Book::firstOrCreate(['isbn' => $b['isbn']], $b);
        }
    }

    private function seedMemberLoans(): void
    {
        $member = User::where('email', 'user@perpustakaan.com')->first();
        $admin = User::where('email', 'admin@perpustakaan.com')->first();

        if (!$member || !$admin) {
            return;
        }

        if ($member->loans()->count() > 0) {
            return;
        }

        $books = Book::orderBy('id')->get();
        if ($books->count() < 3) {
            return;
        }

        $now = Carbon::today();

        DB::transaction(function () use ($member, $admin, $books, $now) {
            Loan::create([
                'user_id' => $member->id,
                'book_id' => $books[0]->id,
                'loan_date' => $now->copy()->subDays(4),
                'due_date' => $now->copy()->addDays(3),
                'duration_days' => 7,
                'denda_per_day' => 500,
                'processed_by' => $admin->id,
            ]);

            Loan::create([
                'user_id' => $member->id,
                'book_id' => $books[1]->id,
                'loan_date' => $now->copy()->subDays(20),
                'due_date' => $now->copy()->subDays(13),
                'duration_days' => 7,
                'denda_per_day' => 500,
                'processed_by' => $admin->id,
            ]);

            Loan::create([
                'user_id' => $member->id,
                'book_id' => $books[2]->id,
                'loan_date' => $now->copy()->subDays(30),
                'due_date' => $now->copy()->subDays(23),
                'returned_at' => $now->copy()->subDays(10),
                'duration_days' => 7,
                'denda_per_day' => 500,
                'denda' => 5000,
                'status_denda' => 'belum_bayar',
                'processed_by' => $admin->id,
            ]);
        });
    }
}
