<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;

// Rute utama untuk katalog buku, dapat diakses oleh semua orang
Route::get('/', [BookController::class, 'index'])->name('catalog.index');
// Rute BARU khusus untuk data AJAX bagi guest
Route::get('/catalog-data', [BookController::class, 'catalogData'])->name('catalog.data');

// Rute '/dashboard' cerdas yang mengarahkan berdasarkan role
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role == 'admin' || $user->role == 'librarian') {
        return redirect()->route('loans.index');
    }
    return redirect()->route('catalog.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// KELOMPOK RUTE YANG WAJIB LOGIN
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Menggunakan 'except' untuk menghindari duplikasi rute index buku
    Route::resource('books', BookController::class)->except('index');

    Route::resource('categories', CategoryController::class);
    Route::resource('users', UserController::class);
    Route::put('loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::resource('loans', LoanController::class);
    Route::post('/borrow/{book}', [LoanController::class, 'borrow'])->name('loans.borrow');
});

require __DIR__ . '/auth.php';
