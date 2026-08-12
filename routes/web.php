<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
    ->middleware(['auth', 'staff'])
    ->name('dashboard.stats');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Book viewing - all authenticated users
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/search', [BookController::class, 'search'])->name('books.search');

    // Book management - admin only (registered before /books/{book} so 'create' isn't captured as a book id)
    Route::middleware('admin')->group(function () {
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
        Route::get('/books/{book}/print-label', [BookController::class, 'printLabel'])->name('books.print-label');
        Route::get('/books-print-label-batch', [BookController::class, 'printLabelBatch'])->name('books.print-label-batch');
    });

    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

    // Loans - all users can view own, borrow
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/borrow', [LoanController::class, 'createBorrow'])->name('loans.borrow.create');
    Route::post('/loans/borrow', [LoanController::class, 'storeBorrow'])->name('loans.borrow.store');

    // Return, pay denda, export - staff only (admin + staff)
    Route::middleware('staff')->group(function () {
        Route::get('/loans/return', [LoanController::class, 'createReturn'])->name('loans.return.create');
        Route::post('/loans/return/check', [LoanController::class, 'checkReturn'])->name('loans.return.check');
        Route::post('/loans/return', [LoanController::class, 'storeReturn'])->name('loans.return.store');
        Route::post('/loans/{loan}/pay-denda', [LoanController::class, 'payDenda'])->name('loans.pay-denda');
        Route::get('/loans/export/csv', [LoanController::class, 'export'])->name('loans.export');
    });

    // User management - admin only
    Route::middleware('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/import', [UserController::class, 'showImport'])->name('users.import');
        Route::post('/users/import', [UserController::class, 'import'])->name('users.import.store');
        Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });
});

require __DIR__.'/auth.php';
