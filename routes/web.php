<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('books', BookController::class);

    Route::get('/books/{book}/print-label', [BookController::class, 'printLabel'])->name('books.print-label');
    Route::get('/books-print-label-batch', [BookController::class, 'printLabelBatch'])->name('books.print-label-batch');

    Route::get('/loans/borrow', [LoanController::class, 'createBorrow'])->name('loans.borrow.create');
    Route::post('/loans/borrow', [LoanController::class, 'storeBorrow'])->name('loans.borrow.store');
    Route::get('/loans/return', [LoanController::class, 'createReturn'])->name('loans.return.create');
    Route::post('/loans/return', [LoanController::class, 'storeReturn'])->name('loans.return.store');
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
});

require __DIR__.'/auth.php';
