<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
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
                'cover_image' => $b->cover_image ? asset($b->cover_image) : null,
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
                    'cover_image' => $b->cover_image ? asset($b->cover_image) : null,
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

        Book::create($input);

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
