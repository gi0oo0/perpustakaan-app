<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Support\CoverGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            if ($request->status === 'available') {
                $query->where('stock', '>', 0);
            } elseif ($request->status === 'borrowed') {
                $query->where('stock', '<=', 0);
            }
        }

        $books = $query->latest()->get();
        $kategoriList = Book::getKategoriList();

        $booksJson = $books->map(function (Book $b) {
            return [
                'id' => $b->id,
                'title' => $b->title,
                'author' => $b->author,
                'isbn' => $b->isbn,
                'publisher' => $b->publisher,
                'publication_year' => $b->publication_year,
                'kategori' => $b->kategori,
                'stock' => $b->stock,
                'available' => $b->stock > 0,
                'description' => $b->description,
                'cover_image' => $b->cover_url,
                'url' => route('books.show', $b),
                'edit_url' => route('books.edit', $b),
                'borrow_url' => route('loans.borrow.create'),
            ];
        })->values();

        return view('books.index', compact('books', 'kategoriList', 'booksJson'));
    }

    public function search(Request $request)
    {
        $query = Book::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                   ->orWhere('author', 'like', "%{$q}%")
                   ->orWhere('isbn', 'like', "%{$q}%")
                   ->orWhere('kategori', 'like', "%{$q}%");
            });
        }

        return response()->json(
            $query->latest()->take(20)->get()->map(function (Book $b) {
                return [
                    'id' => $b->id,
                    'title' => $b->title,
                    'author' => $b->author,
                    'isbn' => $b->isbn,
                    'kategori' => $b->kategori,
                    'stock' => $b->stock,
                    'available' => $b->stock > 0,
                    'cover_image' => $b->cover_url,
                    'url' => route('books.show', $b),
                    'borrow_url' => route('loans.borrow.create'),
                ];
            })
        );
    }

    public function create()
    {
        $kategoriList = Book::getKategoriList();
        return view('books.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string|unique:books,isbn|max:255',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|digits:4',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=50,min_height=50|max:2048',
            'stock' => 'required|integer|min:0',
            'kategori' => 'nullable|string',
        ]);

        $input = $request->only([
            'isbn',
            'title',
            'author',
            'publisher',
            'publication_year',
            'description',
            'stock',
            'kategori',
        ]);

        if ($image = $request->file('cover_image')) {
            $destinationPath = 'images/covers/';
            $profileImage = date('YmdHis') . "." . $image->guessExtension();
            $image->move(public_path($destinationPath), $profileImage);
            $input['cover_image'] = $destinationPath . $profileImage;
        }

        $book = Book::create($input);

        if (!$book->cover_image) {
            $book->cover_image = CoverGenerator::ensure($book);
            $book->saveQuietly();
        }

        return redirect()->route('books.index')
                         ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Book $book)
    {
        $activeLoans = $book->loans()->whereNull('returned_at')->count();
        return view('books.show', compact('book', 'activeLoans'));
    }

    public function edit(Book $book)
    {
        $kategoriList = Book::getKategoriList();
        return view('books.edit', compact('book', 'kategoriList'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'isbn' => 'required|string|max:255|unique:books,isbn,' . $book->id,
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|digits:4',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=50,min_height=50|max:2048',
            'stock' => 'required|integer|min:0',
            'kategori' => 'nullable|string',
        ]);

        $input = $request->only([
            'isbn',
            'title',
            'author',
            'publisher',
            'publication_year',
            'description',
            'stock',
            'kategori',
        ]);

        if ($image = $request->file('cover_image')) {
            $this->deleteCover($book->cover_image);
            $destinationPath = 'images/covers/';
            $profileImage = date('YmdHis') . "." . $image->guessExtension();
            $image->move(public_path($destinationPath), $profileImage);
            $input['cover_image'] = $destinationPath . $profileImage;
        }

        $book->update($input);

        if (!$book->cover_image) {
            $book->cover_image = CoverGenerator::ensure($book);
            $book->saveQuietly();
        }

        return redirect()->route('books.index')
                         ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        $activeLoans = $book->loans()->whereNull('returned_at')->count();
        if ($activeLoans > 0) {
            return back()->with('error', 'Tidak dapat menghapus "' . $book->title . '" karena masih memiliki ' . $activeLoans . ' peminjaman aktif.');
        }

        $this->deleteCover($book->cover_image);

        $book->delete();

        return redirect()->route('books.index')
                         ->with('success', 'Buku berhasil dihapus.');
    }

    public function printLabel(Book $book)
    {
        $barcode = DNS2D::getBarcodePNG($book->isbn, 'QRCODE', 4, 4);
        return view('books.print-label', compact('book', 'barcode'));
    }

    public function printLabelBatch()
    {
        $books = Book::latest()->get();
        foreach ($books as $book) {
            $book->barcode_img = DNS2D::getBarcodePNG($book->isbn, 'QRCODE', 4, 4);
        }
        return view('books.print-label-batch', compact('books'));
    }

    public function showImport()
    {
        return view('books.import');
    }

    public function downloadTemplate()
    {
        $filename = 'template_import_buku.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['isbn', 'judul', 'penulis', 'penerbit', 'tahun_terbit', 'stok', 'kategori', 'cover_image']);
            fputcsv($file, ['9786020631231', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 3, 'Fiksi', 'https://example.com/cover/laskar-pelangi.jpg']);
            fputcsv($file, ['9789799731234', 'Atomic Habits', 'James Clear', 'Gramedia', 2018, 2, 'Pengembangan Diri', '']);
            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $rows = $this->parseCsv($request->file('csv_file'));

        if (empty($rows) || !isset($rows[0]['isbn'], $rows[0]['title'], $rows[0]['author'])) {
            return back()->with('error', 'Kolom wajib "isbn", "judul", dan "penulis" tidak ditemukan di dalam file CSV.');
        }

        $kategoriList = Book::getKategoriList();
        $kategoriValid = array_keys($kategoriList);

        $existingIsbns = Book::whereIn('isbn', array_column($rows, 'isbn'))
            ->pluck('isbn')
            ->flip();

        $imported = [];
        $failed = [];
        $fileIsbns = [];

        foreach ($rows as $row) {
            $isbn = trim((string) ($row['isbn'] ?? ''));
            $title = trim((string) ($row['title'] ?? ''));
            $author = trim((string) ($row['author'] ?? ''));
            $publisher = trim((string) ($row['publisher'] ?? ''));
            $publicationYear = trim((string) ($row['publication_year'] ?? ''));
            $stock = trim((string) ($row['stock'] ?? ''));
            $kategori = trim((string) ($row['kategori'] ?? ''));
            $description = trim((string) ($row['description'] ?? ''));
            $coverUrl = trim((string) ($row['cover_image'] ?? ''));

            $errors = [];

            if ($isbn === '') {
                $errors[] = 'ISBN kosong';
            }
            if ($title === '') {
                $errors[] = 'Judul kosong';
            }
            if ($author === '') {
                $errors[] = 'Penulis kosong';
            }
            if ($isbn !== '' && ($existingIsbns->has($isbn) || isset($fileIsbns[$isbn]))) {
                $errors[] = 'ISBN sudah terdaftar';
            }
            if ($publicationYear !== '' && !preg_match('/^\d{4}$/', $publicationYear)) {
                $errors[] = 'Tahun terbit harus 4 digit angka';
            }
            if ($stock !== '') {
                if (!preg_match('/^\d+$/', $stock)) {
                    $errors[] = 'Stok harus angka bulat';
                } elseif ((int) $stock < 0) {
                    $errors[] = 'Stok tidak boleh negatif';
                }
            }
            if ($kategori !== '' && !in_array($kategori, $kategoriValid, true)) {
                $errors[] = 'Kategori tidak dikenali (gunakan: ' . implode(', ', $kategoriValid) . ')';
            }

            $coverPath = null;
            if ($coverUrl !== '' && empty($errors)) {
                $coverPath = $this->downloadCover($coverUrl);
                if ($coverPath === null) {
                    $errors[] = 'Gagal mengunduh cover. Pastikan URL gambar valid (http/https) dan berformat gambar.';
                }
            }

            $fileIsbns[$isbn] = true;

            if (!empty($errors)) {
                $failed[] = ['isbn' => $isbn, 'title' => $title, 'errors' => $errors];
                continue;
            }

            $book = Book::create([
                'isbn' => $isbn,
                'title' => $title,
                'author' => $author,
                'publisher' => $publisher !== '' ? $publisher : null,
                'publication_year' => $publicationYear !== '' ? (int) $publicationYear : null,
                'stock' => $stock !== '' ? (int) $stock : 0,
                'kategori' => $kategori !== '' ? $kategori : null,
                'description' => $description !== '' ? $description : null,
                'cover_image' => $coverPath,
            ]);

            if (!$book->cover_image) {
                $book->cover_image = CoverGenerator::ensure($book);
                $book->saveQuietly();
            }

            $imported[] = [
                'isbn' => $isbn,
                'title' => $title,
                'author' => $author,
                'publisher' => $publisher,
                'publication_year' => $publicationYear,
                'stock' => $stock,
                'kategori' => $kategori,
                'cover_image' => $coverPath ? asset($coverPath) : null,
            ];
        }

        return view('books.import', compact('imported', 'failed'));
    }

    private function parseCsv($file): array
    {
        $content = file_get_contents($file->getRealPath());

        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        $firstLine = strtok($content, "\r\n");
        $comma = substr_count($firstLine, ',');
        $semicolon = substr_count($firstLine, ';');
        $tab = substr_count($firstLine, "\t");
        $delimiter = ',';
        if ($semicolon > $comma && $semicolon >= $tab) {
            $delimiter = ';';
        } elseif ($tab > $comma && $tab > $semicolon) {
            $delimiter = "\t";
        }

        $temp = tmpfile();
        fwrite($temp, $content);
        fseek($temp, 0);

        $rows = [];
        $header = null;
        while (($line = fgetcsv($temp, 0, $delimiter)) !== false) {
            if ($header === null) {
                $header = array_map(fn ($col) => strtolower(trim((string) $col)), $line);
                continue;
            }
            if (count(array_filter($line, fn ($col) => trim((string) $col) !== '')) === 0) {
                continue;
            }
            $rows[] = $line;
        }
        fclose($temp);

        $map = [
            'isbn' => 'isbn',
            'judul' => 'title',
            'judul buku' => 'title',
            'title' => 'title',
            'nama buku' => 'title',
            'penulis' => 'author',
            'pengarang' => 'author',
            'author' => 'author',
            'penerbit' => 'publisher',
            'publisher' => 'publisher',
            'tahun' => 'publication_year',
            'tahun terbit' => 'publication_year',
            'tahun_terbit' => 'publication_year',
            'publication_year' => 'publication_year',
            'stok' => 'stock',
            'stock' => 'stock',
            'jumlah' => 'stock',
            'kategori' => 'kategori',
            'category' => 'kategori',
            'cover' => 'cover_image',
            'cover_image' => 'cover_image',
            'gambar sampul' => 'cover_image',
            'deskripsi' => 'description',
            'description' => 'description',
        ];

        $result = [];
        foreach ($rows as $line) {
            $row = [];
            foreach ($header as $i => $col) {
                if (isset($map[$col])) {
                    $row[$map[$col]] = trim((string) ($line[$i] ?? ''));
                }
            }
            $result[] = $row;
        }

        return $result;
    }

    private function downloadCover(string $url): ?string
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (!in_array(strtolower((string) $scheme), ['http', 'https'], true)) {
            return null;
        }

        try {
            $response = Http::timeout(15)->get($url);
        } catch (\Throwable $e) {
            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $mime = strtolower(explode(';', $response->header('Content-Type', ''))[0]);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        if (!isset($extensions[$mime])) {
            return null;
        }

        $body = $response->body();
        if ($body === '' || strlen($body) > 2097152) {
            return null;
        }

        $directory = public_path('images/covers');
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $filename = 'cover_' . date('YmdHis') . '_' . substr(md5($url . microtime()), 0, 8) . '.' . $extensions[$mime];
        file_put_contents($directory . DIRECTORY_SEPARATOR . $filename, $body);

        return 'images/covers/' . $filename;
    }

    private function deleteCover(?string $coverImage): void
    {
        if (!$coverImage || !str_starts_with($coverImage, 'images/covers/')) {
            return;
        }

        $path = public_path($coverImage);
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
