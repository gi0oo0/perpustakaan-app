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
        $query = Loan::with('book', 'user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('book', function ($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%")
                       ->orWhere('isbn', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('returned_at')->where('due_date', '>=', Carbon::today());
            } elseif ($request->status === 'returned') {
                $query->whereNotNull('returned_at');
            } elseif ($request->status === 'overdue') {
                $query->whereNull('returned_at')->where('due_date', '<', Carbon::today());
            } elseif ($request->status === 'returned_late') {
                $query->whereNotNull('returned_at')->where('denda', '>', 0);
            } elseif ($request->status === 'returned_ontime') {
                $query->whereNotNull('returned_at')->where('denda', 0);
            }
        }

        if ($request->filled('date_from')) {
            $query->where('loan_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('loan_date', '<=', $request->date_to);
        }

        $loans = $query->latest()->paginate(15)->withQueryString();

        $totalThisMonth = Loan::whereMonth('loan_date', Carbon::now()->month)
            ->whereYear('loan_date', Carbon::now()->year)
            ->count();
        $activeCount = Loan::whereNull('returned_at')->count();

        return view('loans.index', compact('loans', 'totalThisMonth', 'activeCount'));
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

        $existingActiveLoan = Loan::where('book_id', $book->id)
            ->whereNull('returned_at')
            ->first();

        if ($existingActiveLoan) {
            return back()->withErrors([
                'isbn' => 'Buku "' . $book->title . '" sedang dipinjam oleh ' . $existingActiveLoan->user->name . '. Harus dikembalikan pada ' . $existingActiveLoan->due_date->format('d/m/Y') . '.'
            ])->withInput();
        }

        $myExistingLoan = Loan::where('user_id', $request->user()->id)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->first();

        if ($myExistingLoan) {
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
        $activeLoans = Loan::whereNull('returned_at')
            ->with('book', 'user')
            ->get();

        return view('loans.return', compact('activeLoans'));
    }

    public function checkReturn(Request $request)
    {
        $request->validate(['isbn' => 'required|string']);

        $book = Book::where('isbn', $request->isbn)->first();
        if (!$book) {
            return response()->json(['error' => 'Buku dengan ISBN tersebut tidak ditemukan.'], 404);
        }

        $loan = Loan::where('book_id', $book->id)
            ->whereNull('returned_at')
            ->first();

        if (!$loan) {
            return response()->json(['error' => 'Tidak ada peminjaman aktif untuk buku ini.'], 404);
        }

        return response()->json([
            'loan_id' => $loan->id,
            'book_title' => $loan->book->title,
            'borrower_name' => $loan->user->name,
            'borrower_nisn' => $loan->user->nisn,
            'loan_date' => $loan->loan_date->format('d/m/Y'),
            'due_date' => $loan->due_date->format('d/m/Y'),
            'days_late' => $loan->getDaysLate(),
            'potential_denda' => $loan->getPotentialDenda(),
            'is_overdue' => $loan->isOverdue(),
        ]);
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
            $loan = Loan::where('book_id', $book->id)
                ->whereNull('returned_at')
                ->first();
        } else {
            $loan = Loan::where('id', $request->loan_id)
                ->whereNull('returned_at')
                ->first();
        }

        if (!$loan) {
            return back()->withErrors(['isbn' => 'Pinjaman tidak ditemukan atau buku sudah dikembalikan.'])->withInput();
        }

        $daysLate = $loan->getDaysLate();
        $denda = Loan::calculateDenda($daysLate);

        $loan->update([
            'returned_at' => Carbon::today(),
            'denda' => $denda,
            'status_denda' => $denda > 0 ? 'belum_bayar' : 'lunas',
        ]);

        $loan->book->increment('stock');

        $msg = 'Buku "' . $loan->book->title . '" berhasil dikembalikan.';
        if ($denda > 0) {
            $msg .= ' Denda: Rp' . number_format($denda, 0, ',', '.');
        }

        return redirect()->route('loans.index')
                         ->with('success', $msg);
    }

    public function payDenda(Loan $loan)
    {
        $loan->update(['status_denda' => 'lunas']);
        return back()->with('success', 'Denda untuk "' . $loan->book->title . '" ditandai sebagai lunas.');
    }

    public function export(Request $request)
    {
        $query = Loan::with('book', 'user');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('returned_at')->where('due_date', '>=', Carbon::today());
            } elseif ($request->status === 'returned') {
                $query->whereNotNull('returned_at');
            } elseif ($request->status === 'overdue') {
                $query->whereNull('returned_at')->where('due_date', '<', Carbon::today());
            } elseif ($request->status === 'returned_late') {
                $query->whereNotNull('returned_at')->where('denda', '>', 0);
            } elseif ($request->status === 'returned_ontime') {
                $query->whereNotNull('returned_at')->where('denda', 0);
            }
        }

        if ($request->filled('date_from')) {
            $query->where('loan_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('loan_date', '<=', $request->date_to);
        }

        $loans = $query->latest()->get();

        $filename = 'riwayat_peminjaman_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($loans) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Judul Buku', 'ISBN', 'Peminjam', 'Tanggal Pinjam', 'Jatuh Tempo', 'Tanggal Kembali', 'Status', 'Denda (Rp)', 'Status Denda']);

            $no = 1;
            foreach ($loans as $loan) {
                if ($loan->isReturned()) {
                    $status = $loan->denda > 0 ? 'Dikembalikan (Telat)' : 'Dikembalikan (Tepat)';
                } elseif ($loan->isOverdue()) {
                    $status = 'Terlambat';
                } else {
                    $status = 'Dipinjam';
                }

                fputcsv($file, [
                    $no++,
                    $loan->book->title ?? '-',
                    $loan->book->isbn ?? '-',
                    $loan->user->name ?? '-',
                    $loan->loan_date->format('d/m/Y'),
                    $loan->due_date->format('d/m/Y'),
                    $loan->returned_at ? $loan->returned_at->format('d/m/Y') : '-',
                    $status,
                    $loan->denda ?? 0,
                    $loan->denda > 0 ? ($loan->status_denda === 'lunas' ? 'Lunas' : 'Belum Bayar') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
