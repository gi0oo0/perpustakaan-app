<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $activeLoans = Loan::whereNull('returned_at')->count();
        $overdueLoans = Loan::whereNull('returned_at')
            ->where('due_date', '<', Carbon::today())
            ->count();
        $totalLoans = Loan::count();
        $returnedLoans = Loan::whereNotNull('returned_at')->count();

        return view('dashboard', compact(
            'totalBooks',
            'activeLoans',
            'overdueLoans',
            'totalLoans',
            'returnedLoans'
        ));
    }

    public function stats()
    {
        // 1. Top 5 buku terpopuler (paling sering dipinjam)
        $topBooks = Loan::select('book_id', DB::raw('count(*) as total'))
            ->groupBy('book_id')
            ->orderByDesc('total')
            ->limit(5)
            ->join('books', 'loans.book_id', '=', 'books.id')
            ->pluck('total', 'books.title');

        // 2. Peminjaman per bulan (6 bulan terakhir)
        $monthlyLoans = Loan::select(
                DB::raw("DATE_FORMAT(loan_date, '%Y-%m') as month"),
                DB::raw('count(*) as total')
            )
            ->where('loan_date', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // 3. Status buku
        $totalBooks = Book::sum('stock');
        $currentlyLoaned = Loan::whereNull('returned_at')->count();
        $overdue = Loan::whereNull('returned_at')
            ->where('due_date', '<', Carbon::today())
            ->count();
        $available = max(0, $totalBooks - $currentlyLoaned);

        return response()->json([
            'topBooks' => [
                'labels' => $topBooks->keys()->toArray(),
                'data' => $topBooks->values()->toArray(),
            ],
            'monthlyLoans' => [
                'labels' => $monthlyLoans->keys()->map(function ($m) {
                    return Carbon::parse($m)->translatedFormat('M Y');
                })->toArray(),
                'data' => $monthlyLoans->values()->toArray(),
            ],
            'bookStatus' => [
                'labels' => ['Tersedia', 'Dipinjam', 'Terlambat'],
                'data' => [$available, $currentlyLoaned, $overdue],
            ],
        ]);
    }
}
