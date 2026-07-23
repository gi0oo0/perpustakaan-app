<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->loans()->with('book');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('returned_at');
            } elseif ($request->status === 'returned') {
                $query->whereNotNull('returned_at');
            } elseif ($request->status === 'overdue') {
                $query->whereNull('returned_at')->where('due_date', '<', Carbon::today());
            }
        }

        $loans = $query->latest()->paginate(10)->withQueryString();
        return view('loans.index', compact('loans'));
    }

    public function createBorrow()
    {
        $books = Book::where('stock', '>', 0)->latest()->get();
        return view('loans.borrow', compact('books'));
    }

    public function storeBorrow(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string',
        ]);

        $book = Book::where('isbn', $request->isbn)->first();

        if (!$book) {
            return back()->withErrors(['isbn' => 'Buku dengan ISBN tersebut tidak ditemukan.'])->withInput();
        }

        if ($book->stock <= 0) {
            return back()->withErrors(['isbn' => 'Stok buku "' . $book->title . '" sudah habis.'])->withInput();
        }

        $existingActiveLoan = Loan::where('user_id', $request->user()->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->first();

        if ($existingActiveLoan) {
            return back()->withErrors(['isbn' => 'Anda sudah meminjam buku "' . $book->title . '" dan belum dikembalikan.'])->withInput();
        }

        Loan::create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'loan_date' => Carbon::today(),
            'due_date' => Carbon::today()->addDays(7),
        ]);

        $book->decrement('stock');

        return redirect()->route('loans.index')
                         ->with('success', 'Buku "' . $book->title . '" berhasil dipinjam. Harus dikembalikan pada ' . Carbon::today()->addDays(7)->format('d/m/Y') . '.');
    }

    public function createReturn()
    {
        $activeLoans = Loan::where('user_id', request()->user()->id)
            ->whereNull('returned_at')
            ->with('book')
            ->get();

        return view('loans.return', compact('activeLoans'));
    }

    public function storeReturn(Request $request)
    {
        $request->validate([
            'loan_id' => 'required_without:isbn|nullable|exists:loans,id',
            'isbn' => 'required_without:loan_id|nullable|string',
        ]);

        if ($request->filled('isbn')) {
            $book = Book::where('isbn', $request->isbn)->first();
            if (!$book) {
                return back()->withErrors(['isbn' => 'Buku dengan ISBN tersebut tidak ditemukan.'])->withInput();
            }
            $loan = Loan::where('user_id', $request->user()->id)
                ->where('book_id', $book->id)
                ->whereNull('returned_at')
                ->first();
        } else {
            $loan = Loan::where('id', $request->loan_id)
                ->where('user_id', $request->user()->id)
                ->whereNull('returned_at')
                ->first();
        }

        if (!$loan) {
            return back()->withErrors(['isbn' => 'Pinjaman tidak ditemukan atau buku sudah dikembalikan.'])->withInput();
        }

        $loan->update([
            'returned_at' => Carbon::today(),
        ]);

        $loan->book->increment('stock');

        return redirect()->route('loans.index')
                         ->with('success', 'Buku "' . $loan->book->title . '" berhasil dikembalikan.');
    }
}
