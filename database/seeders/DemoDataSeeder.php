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

        $this->seedCovers();

        $this->seedMemberLoans();

        $this->command->info('Demo data siap: buku & pinjaman contoh telah dibuat.');
    }

    private function seedBooks(): void
    {
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
            ['isbn' => '978-602-8811-70-5', 'title' => 'Negeri 5 Menara', 'author' => 'Ahmad Fuadi', 'publisher' => 'Gramedia', 'publication_year' => 2009, 'kategori' => 'Novel', 'stock' => 7],
            ['isbn' => '978-602-424-448-2', 'title' => 'Filosofi Teras', 'author' => 'Henry Manampiring', 'publisher' => 'Kompas', 'publication_year' => 2018, 'kategori' => 'Pengembangan Diri', 'stock' => 5],
            ['isbn' => '978-602-424-796-3', 'title' => 'Habis Gelap Terbitlah Terang', 'author' => 'R.A. Kartini', 'publisher' => 'Balai Pustaka', 'publication_year' => 1911, 'kategori' => 'Sejarah', 'stock' => 4],
            ['isbn' => '978-979-3062-91-4', 'title' => 'Sang Pemimpi', 'author' => 'Andrea Hirata', 'publisher' => 'Bentang Pustaka', 'publication_year' => 2006, 'kategori' => 'Novel', 'stock' => 9],
            ['isbn' => '978-602-03-1226-2', 'title' => 'Ronggeng Dukuh Paruk', 'author' => 'Ahmad Tohari', 'publisher' => 'Gramedia', 'publication_year' => 1982, 'kategori' => 'Novel', 'stock' => 6],
            ['isbn' => '978-602-424-124-5', 'title' => 'Atomic Habits', 'author' => 'James Clear', 'publisher' => 'Gramedia', 'publication_year' => 2019, 'kategori' => 'Pengembangan Diri', 'stock' => 8],
            ['isbn' => '978-602-298-905-1', 'title' => 'Biologi untuk SMA', 'author' => 'Neil A. Campbell', 'publisher' => 'Erlangga', 'publication_year' => 2017, 'kategori' => 'Sains & Teknologi', 'stock' => 10],
            ['isbn' => '978-602-241-115-4', 'title' => 'Ekonomi Kelas XII', 'author' => 'Alam S.', 'publisher' => 'Erlangga', 'publication_year' => 2019, 'kategori' => 'Pendidikan', 'stock' => 8],
            ['isbn' => '978-602-241-620-3', 'title' => 'Ensiklopedia Sains untuk Anak', 'author' => 'Dorling Kindersley', 'publisher' => 'Erlangga', 'publication_year' => 2016, 'kategori' => 'Sains & Teknologi', 'stock' => 5],
            ['isbn' => '978-602-424-559-5', 'title' => 'Si Juki: Komik', 'author' => 'Faza Meonk', 'publisher' => 'Gramedia', 'publication_year' => 2016, 'kategori' => 'Komik', 'stock' => 7],
            ['isbn' => '978-602-250-870-0', 'title' => 'Kumpulan Cerita Rakyat Nusantara', 'author' => 'Tim Pustaka', 'publisher' => 'Pustaka Ceria', 'publication_year' => 2015, 'kategori' => 'Fiksi', 'stock' => 6],
        ];

        foreach ($books as $b) {
            Book::firstOrCreate(['isbn' => $b['isbn']], $b);
        }
    }

    private function seedCovers(): void
    {
        $directory = public_path('images/covers');
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        Book::whereNull('cover_image')->orderBy('id')->get()
            ->each(function (Book $book) use ($directory) {
                $file = 'images/covers/' . $book->isbn . '.svg';
                $path = public_path($file);

                if (!file_exists($path)) {
                    file_put_contents($path, $this->renderCoverSvg($book));
                }

                $book->update(['cover_image' => $file]);
            });
    }

    private function renderCoverSvg(Book $book): string
    {
        [$from, $to] = $this->coverGradient($book->id);

        $lines = $this->wrapTitle($book->title);
        $lineCount = count($lines);
        $startY = 210 - (($lineCount - 1) * 22);

        $text = '';
        foreach ($lines as $i => $line) {
            $y = $startY + ($i * 44);
            $text .= "<text x=\"150\" y=\"{$y}\" font-family=\"Georgia, 'Times New Roman', serif\" font-size=\"28\" font-weight=\"bold\" fill=\"#ffffff\" text-anchor=\"middle\">" . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</text>';
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="420" viewBox="0 0 300 420">'
            . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . "<stop offset=\"0%\" stop-color=\"{$from}\"/>"
            . "<stop offset=\"100%\" stop-color=\"{$to}\"/>"
            . '</linearGradient></defs>'
            . '<rect width="300" height="420" fill="url(#g)"/>'
            . '<rect width="300" height="420" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="10"/>'
            . "<text x=\"150\" y=\"90\" font-family=\"Arial, sans-serif\" font-size=\"14\" letter-spacing=\"4\" fill=\"rgba(255,255,255,0.7)\" text-anchor=\"middle\" text-transform=\"uppercase\">PERPUSTAKAAN SEKOLAH</text>"
            . $text
            . '</svg>';
    }

    private function wrapTitle(string $title, int $maxChars = 22): array
    {
        $words = explode(' ', trim($title));
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;

            if (mb_strlen($candidate) <= $maxChars) {
                $current = $candidate;
            } else {
                $lines[] = $current;
                $current = $word;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private function coverGradient(int $id): array
    {
        $palette = [
            ['#667eea', '#764ba2'],
            ['#f093fb', '#f5576c'],
            ['#4facfe', '#00f2fe'],
            ['#43e97b', '#38f9d7'],
            ['#fa709a', '#fee140'],
            ['#30cfd0', '#330867'],
            ['#a8edea', '#fed6e3'],
            ['#5ee7df', '#b490ca'],
            ['#d299c2', '#fef9d7'],
            ['#f6d365', '#fda085'],
            ['#fbc2eb', '#a6c1ee'],
            ['#84fab0', '#8fd3f4'],
            ['#a1c4fd', '#c2e9fb'],
            ['#ff9a9e', '#fecfef'],
            ['#fccb90', '#d57eeb'],
            ['#e0c3fc', '#8ec5fc'],
            ['#c79081', '#dfa579'],
            ['#96fbc4', '#f9f586'],
            ['#7f7fd5', '#86a8e7'],
            ['#13547a', '#80d0c7'],
        ];

        $colors = $palette[$id % count($palette)];

        return $colors;
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
