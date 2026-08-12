<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = Loan::with('book', 'user', 'processor');

        if (! $request->user()->isStaff()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('book', function ($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
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

        $loans = $query->latest()->get();

        $isStaff = $request->user()->isStaff();
        $isAdmin = $request->user()->isAdmin();

        $loansData = $loans->map(function (Loan $loan) use ($isStaff) {
            $statusKey = $loan->isReturned()
                ? ($loan->denda > 0 ? 'returned_late' : 'returned_ontime')
                : ($loan->isOverdue() ? 'overdue' : 'active');

            $statusLabel = $loan->isReturned()
                ? ($loan->denda > 0 ? 'Telat' : 'Tepat')
                : ($loan->isOverdue() ? 'Terlambat' : 'Dipinjam');

            $statusDetail = $loan->isReturned()
                ? ''
                : ($loan->getDaysLate() > 0 ? $loan->getDaysLate().'h' : '');

            $statusBadge = match ($statusKey) {
                'returned_ontime' => 'green',
                'returned_late', 'overdue' => 'red',
                default => 'yellow',
            };

            $dendaText = '-';
            $dendaSub = '';
            $dendaAction = null;
            if ($loan->denda > 0) {
                $dendaText = 'Rp'.number_format($loan->denda, 0, ',', '.');
                $dendaSub = $loan->status_denda === 'lunas' ? 'Lunas' : 'Belum bayar';
                if ($loan->status_denda === 'belum_bayar' && $isStaff) {
                    $dendaAction = route('loans.pay-denda', $loan);
                }
            } elseif (! $loan->isReturned() && $loan->isOverdue()) {
                $dendaText = 'Rp'.number_format($loan->getPotentialDenda(), 0, ',', '.');
                $dendaSub = 'Rp'.number_format($loan->getDendaPerDay(), 0, ',', '.').'/hari';
            }

            return [
                'id' => $loan->id,
                'book_title' => $loan->book->title ?? '-',
                'isbn' => $loan->book->isbn ?? '-',
                'cover_image' => $loan->book->cover_url,
                'borrower_name' => $loan->user->name ?? '-',
                'borrower_nisn' => $loan->user->nisn ?? '-',
                'user_url' => $isStaff && $loan->user ? route('users.show', $loan->user) : null,
                'loan_date' => $loan->loan_date->format('d/m/Y'),
                'loan_date_iso' => $loan->loan_date->format('Y-m-d'),
                'duration_days' => $loan->duration_days,
                'due_date' => $loan->due_date->format('d/m/Y'),
                'returned_at' => $loan->returned_at ? $loan->returned_at->format('d/m/Y') : '-',
                'status_key' => $statusKey,
                'status_label' => $statusLabel,
                'status_detail' => $statusDetail,
                'status_badge' => $statusBadge,
                'denda_text' => $dendaText,
                'denda_sub' => $dendaSub,
                'denda_action' => $dendaAction,
                'processor_name' => $loan->processor->name ?? '-',
                'processed_at' => $loan->returned_at ? $loan->returned_at->format('d/m H:i') : '',
            ];
        })->values();

        $statsQuery = Loan::query();
        if (! $request->user()->isStaff()) {
            $statsQuery->where('user_id', $request->user()->id);
        }

        $totalThisMonth = (clone $statsQuery)->whereMonth('loan_date', Carbon::now()->month)
            ->whereYear('loan_date', Carbon::now()->year)
            ->count();
        $activeCount = (clone $statsQuery)->whereNull('returned_at')->count();

        return view('loans.index', compact('loans', 'loansData', 'totalThisMonth', 'activeCount', 'isStaff', 'isAdmin'));
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
            'duration_days' => 'required|integer|between:1,90',
            'denda_per_day' => 'required|integer|between:1,100000',
        ]);

        $durationDays = (int) $request->duration_days;
        $dendaPerDay = (int) $request->denda_per_day;
        $maxDuration = Loan::maxDurationForDenda($dendaPerDay);

        if ($durationDays > $maxDuration) {
            return back()->withErrors([
                'duration_days' => 'Durasi maksimal untuk denda Rp'.number_format($dendaPerDay, 0, ',', '.').'/hari adalah '.$maxDuration.' hari. Pilih durasi lebih pendek atau naikkan denda (durasi naik proporsional: Rp'.number_format(Loan::TARIF_DENDA_PER_HARI, 0, ',', '.').'/hari = '.Loan::BASE_DURATION_DAYS.' hari).',
            ])->withInput();
        }

        $dueDate = Carbon::today()->addDays($durationDays);

        $book = Book::where('isbn', $request->isbn)->first();

        if (! $book) {
            return back()->withErrors(['isbn' => 'Buku dengan ISBN tersebut tidak ditemukan.'])->withInput();
        }

        $activeLoanCount = Loan::where('user_id', $request->user()->id)
            ->whereNull('returned_at')
            ->count();

        if ($activeLoanCount >= Loan::MAX_ACTIVE_LOANS) {
            return back()->withErrors([
                'isbn' => 'Anda sudah mencapai batas maksimal '.Loan::MAX_ACTIVE_LOANS.' buku yang dipinjam. Kembalikan dulu buku yang sudah dipinjam.',
            ])->withInput();
        }

        return DB::transaction(function () use ($request, $book, $dueDate, $durationDays, $dendaPerDay) {
            $user = User::whereKey($request->user()->id)->lockForUpdate()->first();
            $book = Book::where('isbn', $book->isbn)->lockForUpdate()->first();

            if ($book->stock <= 0) {
                return back()->withErrors(['isbn' => 'Stok buku "'.$book->title.'" sudah habis.'])->withInput();
            }

            $activeLoanCount = Loan::where('user_id', $user->id)
                ->whereNull('returned_at')
                ->count();

            if ($activeLoanCount >= Loan::MAX_ACTIVE_LOANS) {
                return back()->withErrors([
                    'isbn' => 'Anda sudah mencapai batas maksimal '.Loan::MAX_ACTIVE_LOANS.' buku yang dipinjam. Kembalikan dulu buku yang sudah dipinjam.',
                ])->withInput();
            }

            $existingActiveLoan = Loan::where('book_id', $book->id)
                ->whereNull('returned_at')
                ->first();

            if ($existingActiveLoan) {
                return back()->withErrors([
                    'isbn' => 'Buku "'.$book->title.'" sedang dipinjam oleh '.$existingActiveLoan->user->name.'. Harus dikembalikan pada '.$existingActiveLoan->due_date->format('d/m/Y').'.',
                ])->withInput();
            }

            $myExistingLoan = Loan::where('user_id', $request->user()->id)
                ->where('book_id', $book->id)
                ->whereNull('returned_at')
                ->first();

            if ($myExistingLoan) {
                return back()->withErrors(['isbn' => 'Anda sudah meminjam buku "'.$book->title.'" dan belum dikembalikan.'])->withInput();
            }

            Loan::create([
                'user_id' => $request->user()->id,
                'book_id' => $book->id,
                'loan_date' => Carbon::today(),
                'due_date' => $dueDate,
                'duration_days' => $durationDays,
                'denda_per_day' => $dendaPerDay,
            ]);

            $book->decrement('stock');

            return redirect()->route('loans.index')
                ->with('success', 'Buku "'.$book->title.'" berhasil dipinjam selama '.$durationDays.' hari. Harus dikembalikan pada '.$dueDate->format('d/m/Y').'. Denda keterlambatan Rp'.number_format($dendaPerDay, 0, ',', '.').'/hari.');
        });
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
        if (! $book) {
            return response()->json(['error' => 'Buku dengan ISBN tersebut tidak ditemukan.'], 404);
        }

        $loan = Loan::where('book_id', $book->id)
            ->whereNull('returned_at')
            ->first();

        if (! $loan) {
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
            'denda_per_day' => $loan->getDendaPerDay(),
            'is_overdue' => $loan->isOverdue(),
        ]);
    }

    public function storeReturn(Request $request)
    {
        $request->validate([
            'loan_id' => 'required_without:isbn|nullable|exists:loans,id',
            'isbn' => 'required_without:loan_id|nullable|string',
            'confirm_received' => 'required',
        ]);

        if ($request->confirm_received !== '1') {
            return back()->withErrors(['confirm_received' => 'Anda harus mengonfirmasi bahwa buku telah diterima secara fisik.'])->withInput();
        }

        if ($request->filled('isbn')) {
            $book = Book::where('isbn', $request->isbn)->first();
            if (! $book) {
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

        if (! $loan) {
            return back()->withErrors(['isbn' => 'Pinjaman tidak ditemukan atau buku sudah dikembalikan.'])->withInput();
        }

        $daysLate = $loan->getDaysLate();
        $denda = Loan::calculateDenda($daysLate, $loan->getDendaPerDay());

        $loan->update([
            'returned_at' => Carbon::today(),
            'denda' => $denda,
            'status_denda' => $denda > 0 ? 'belum_bayar' : 'lunas',
            'processed_by' => $request->user()->id,
        ]);

        $loan->book->increment('stock');

        $msg = 'Buku "'.$loan->book->title.'" berhasil dikembalikan oleh '.$request->user()->name.'.';
        if ($denda > 0) {
            $msg .= ' Denda: Rp'.number_format($denda, 0, ',', '.');
        }

        return redirect()->route('loans.index')
            ->with('success', $msg);
    }

    public function payDenda(Loan $loan)
    {
        if ($loan->status_denda !== 'belum_bayar') {
            return back()->with('error', 'Denda untuk "'.$loan->book->title.'" tidak berstatus belum bayar.');
        }

        $loan->update(['status_denda' => 'lunas']);

        return back()->with('success', 'Denda untuk "'.$loan->book->title.'" ditandai sebagai lunas.');
    }

    public function export(Request $request)
    {
        $query = Loan::with('book', 'user', 'processor');

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

        $filename = 'riwayat_peminjaman_'.Carbon::now()->format('Y-m-d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($loans) {
            $sanitize = function ($value): string {
                $value = (string) $value;
                if ($value !== '' && str_contains('=+-@', $value[0])) {
                    return "'".$value;
                }

                return $value;
            };

            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Judul Buku', 'ISBN', 'Peminjam', 'NISN', 'Tanggal Pinjam', 'Jatuh Tempo', 'Tanggal Kembali', 'Status', 'Denda (Rp)', 'Status Denda', 'Diproses Oleh']);

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
                    $sanitize($loan->book->title ?? '-'),
                    $sanitize($loan->book->isbn ?? '-'),
                    $sanitize($loan->user->name ?? '-'),
                    $sanitize($loan->user->nisn ?? '-'),
                    $loan->loan_date->format('d/m/Y'),
                    $loan->due_date->format('d/m/Y'),
                    $loan->returned_at ? $loan->returned_at->format('d/m/Y') : '-',
                    $status,
                    $loan->denda ?? 0,
                    $loan->denda > 0 ? ($loan->status_denda === 'lunas' ? 'Lunas' : 'Belum Bayar') : '-',
                    $sanitize($loan->processor ? $loan->processor->name : '-'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
