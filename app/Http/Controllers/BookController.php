<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Milon\Barcode\BarcodeServiceProvider;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

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

        $books = $query->latest()->paginate(10)->withQueryString();
        return view('books.index', compact('books'));
    }

    public function create()
    {
        return view('books.create');
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
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'stock' => 'required|integer|min:0',
        ]);

        $input = $request->all();

        if ($image = $request->file('cover_image')) {
            $destinationPath = 'images/covers/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
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
        return view('books.edit', compact('book'));
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
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'stock' => 'required|integer|min:0',
        ]);

        $input = $request->all();

        if ($image = $request->file('cover_image')) {
            if ($book->cover_image && file_exists(public_path($book->cover_image))) {
                unlink(public_path($book->cover_image));
            }
            $destinationPath = 'images/covers/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move(public_path($destinationPath), $profileImage);
            $input['cover_image'] = $destinationPath . $profileImage;
        }

        $book->update($input);

        return redirect()->route('books.index')
                         ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image && file_exists(public_path($book->cover_image))) {
            unlink(public_path($book->cover_image));
        }

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
}
