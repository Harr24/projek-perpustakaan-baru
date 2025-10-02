<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Public\BookCatalogController;
use App\Http\Controllers\Admin\Petugas\VerificationController;
use App\Http\Controllers\Admin\Petugas\GenreController;
use App\Http\Controllers\Admin\Petugas\BookController;
use App\Http\Controllers\Admin\Superadmin\SuperadminPetugasController;
use App\Http\Controllers\ProfileController; // Pastikan ini ada
use App\Http\Controllers\BorrowingController; // Pastikan ini ada
use App\Http\Controllers\Admin\Superadmin\MemberController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// == RUTE PUBLIK & TAMU ==
Route::get('/', [BookCatalogController::class, 'index'])->name('catalog.index');
Route::get('/book/{book}', [BookCatalogController::class, 'show'])->name('catalog.show');

Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    
    // Rute untuk halaman sukses setelah registrasi
    Route::get('register/success', [AuthController::class, 'registrationSuccess'])->name('register.success');
    
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});


// == RUTE UNTUK PENGGUNA YANG SUDAH LOGIN ==
Route::middleware('auth')->group(function () {
    
    // Rute umum (semua role bisa akses)
    Route::get('dashboard', function(){ return view('dashboard'); })->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Rute untuk peminjaman
    Route::post('/borrow/{book_copy}', [BorrowingController::class, 'store'])->name('borrow.store');

    // == RUTE KHUSUS UNTUK ROLE PETUGAS ==
    Route::middleware('role:petugas')->prefix('admin/petugas')->name('admin.petugas.')->group(function () {
        Route::get('/verifikasi-siswa', [VerificationController::class, 'index'])->name('verification.index');
        Route::post('/verifikasi-siswa/{user}/approve', [VerificationController::class, 'approve'])->name('verification.approve');
        Route::post('/verifikasi-siswa/{user}/reject', [VerificationController::class, 'reject'])->name('verification.reject');
        Route::get('/verifikasi-siswa/lihat-kartu/{user}', [VerificationController::class, 'showStudentCard'])->name('verification.showCard');
        
        Route::resource('genres', GenreController::class);
        Route::resource('books', BookController::class);
    });

    // == RUTE KHUSUS UNTUK ROLE SUPERADMIN ==
    Route::middleware('role:superadmin')->prefix('admin/superadmin')->name('admin.superadmin.')->group(function () {
        Route::resource('petugas', SuperadminPetugasController::class);
        Route::resource('members', MemberController::class)->except(['create', 'store']);

    });

});



