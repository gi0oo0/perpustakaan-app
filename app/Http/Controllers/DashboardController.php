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
        $user = auth()->user();

        if (!$user->isStaff()) {
            return $this->memberDashboard($user);
        }

        $totalBooks = Book::count();
        $activeLoans = Loan::whereNull('returned_at')->count();
        $overdueLoans = Loan::whereNull('returned_at')
            ->where('due_date', '<', Carbon::today())
            ->count();
        $totalLoans = Loan::count();
        $returnedLoans = Loan::whereNotNull('returned_at')->count();

        $recentActivity = Loan::with('book', 'user')->latest('loan_date')->take(6)->get();

        return view('dashboard', compact(
            'totalBooks',
            'activeLoans',
            'overdueLoans',
            'totalLoans',
            'returnedLoans',
            'recentActivity'
        ));
    }

    private function memberDashboard($user)
    {
        $activeLoans = $user->loans()->with('book')
            ->whereNull('returned_at')
            ->latest('loan_date')
            ->get();
        $activeCount = $activeLoans->count();
        $overdueCount = $activeLoans->where('due_date', '<', Carbon::today())->count();
        $totalDenda = $user->loans()->where('status_denda', 'belum_bayar')->sum('denda');
        $totalThisMonth = $user->loans()
            ->whereMonth('loan_date', Carbon::now()->month)
            ->whereYear('loan_date', Carbon::now()->year)
            ->count();
        $dueSoon = $activeLoans
            ->where('due_date', '>=', Carbon::today())
            ->where('due_date', '<=', Carbon::today()->addDays(7))
            ->sortBy('due_date');
        $recentHistory = $user->loans()->with('book')
            ->whereNotNull('returned_at')
            ->latest('returned_at')
            ->take(5)
            ->get();

        $recentBooks = Book::latest('id')->take(6)->get();

        return view('dashboard', compact(
            'activeLoans',
            'activeCount',
            'overdueCount',
            'totalDenda',
            'totalThisMonth',
            'dueSoon',
            'recentHistory',
            'recentBooks'
        ));
    }

    public function stats()
    {
        // 1. Top 5 buku terpopuler (paling sering dipinjam)
        $topBooks = Loan::query()
            ->join('books', 'loans.book_id', '=', 'books.id')
            ->select('books.title as title', DB::raw('count(*) as total'))
            ->groupBy('books.title', 'books.id')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'title');

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
        $currentlyLoaned = Loan::whereNull('returned_at')->count();
        $overdue = Loan::whereNull('returned_at')
            ->where('due_date', '<', Carbon::today())
            ->count();
        $available = Book::where('stock', '>', 0)->count();

        // 4. Peminjaman per kategori
        $kategoriLoans = Loan::join('books', 'loans.book_id', '=', 'books.id')
            ->select('books.kategori as kategori', DB::raw('count(*) as total'))
            ->groupBy('books.kategori')
            ->orderByDesc('total')
            ->get()
            ->map(function ($r) {
                return ['kategori' => $r->kategori ?: 'Tanpa Kategori', 'total' => $r->total];
            });

        // 5. Aktivitas 7 hari terakhir
        $weeklyActivity = collect(range(6, 0))->map(function ($i) {
            $day = Carbon::today()->subDays($i);
            return [
                'label' => $day->translatedFormat('D'),
                'date' => $day->format('Y-m-d'),
                'total' => Loan::whereDate('loan_date', $day)->count(),
            ];
        });

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
            'kategoriLoans' => [
                'labels' => $kategoriLoans->pluck('kategori')->toArray(),
                'data' => $kategoriLoans->pluck('total')->toArray(),
            ],
            'weeklyActivity' => [
                'labels' => $weeklyActivity->pluck('label')->toArray(),
                'data' => $weeklyActivity->pluck('total')->toArray(),
            ],
        ]);
    }
}
