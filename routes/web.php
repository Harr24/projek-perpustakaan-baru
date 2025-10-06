<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Public\BookCatalogController;
use App\Http\Controllers\Admin\Petugas\VerificationController;
use App\Http\Controllers\Admin\Petugas\GenreController;
use App\Http\Controllers\Admin\Petugas\BookController;
use App\Http\Controllers\Admin\Superadmin\SuperadminPetugasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\Admin\Superadmin\MemberController;
use App\Http\Controllers\Admin\Petugas\TeacherController;
use App\Http\Controllers\Admin\Petugas\LoanApprovalController;
use App\Http\Controllers\Admin\Petugas\ReturnController;
use App\Http\Controllers\Admin\Petugas\FineController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// == RUTE PUBLIK & TAMU ==
Route::get('/', [BookCatalogController::class, 'index'])->name('catalog.index');
Route::get('/book/{book}', [BookCatalogController::class, 'show'])->name('catalog.show');
Route::get('/book-cover/{book}', [BookCatalogController::class, 'showCover'])->name('book.cover');


Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('register/success', [AuthController::class, 'registrationSuccess'])->name('register.success');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});


// == RUTE UNTUK PENGGUNA YANG SUDAH LOGIN ==
Route::middleware('auth')->group(function () {
    
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // RUTE BARU UNTUK PENGAJUAN PINJAMAN
    Route::get('/borrow/request/{book_copy}', [BorrowingController::class, 'create'])->name('borrow.create');
    Route::post('/borrow/request', [BorrowingController::class, 'store'])->name('borrow.store');
    Route::get('/my-borrowings', [BorrowingController::class, 'index'])->name('borrow.history');


    // == RUTE KHUSUS UNTUK ROLE PETUGAS ==
    Route::middleware('role:petugas')->prefix('admin/petugas')->name('admin.petugas.')->group(function () {
        Route::get('/verifikasi-siswa', [VerificationController::class, 'index'])->name('verification.index');
        Route::post('/verifikasi-siswa/{user}/approve', [VerificationController::class, 'approve'])->name('verification.approve');
        Route::post('/verifikasi-siswa/{user}/reject', [VerificationController::class, 'reject'])->name('verification.reject');
        Route::get('/verifikasi-siswa/lihat-kartu/{user}', [VerificationController::class, 'showStudentCard'])->name('verification.showCard');
        
        Route::resource('genres', GenreController::class);
        Route::resource('books', BookController::class);

        // Rute untuk Kelola Akun Guru
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');

        // Rute untuk konfirmasi peminjaman
        Route::get('/approvals', [LoanApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{borrowing}/approve', [LoanApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{borrowing}/reject', [LoanApprovalController::class, 'reject'])->name('approvals.reject');
        
        // ==========================================================
        // Rute untuk Manajemen Pengembalian
        // ==========================================================
        Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
        Route::post('/returns/{borrowing}', [ReturnController::class, 'store'])->name('returns.store');
        // Denda
        Route::get('/fines', [FineController::class, 'index'])->name('fines.index');
        Route::post('/fines/{borrowing}/pay', [FineController::class, 'markAsPaid'])->name('fines.pay');

        //
        Route::get('/fines/history', [FineController::class, 'history'])->name('fines.history');
    });

    // == RUTE KHUSUS UNTUK ROLE SUPERADMIN ==
    Route::middleware('role:superadmin')->prefix('admin/superadmin')->name('admin.superadmin.')->group(function () {
        Route::resource('petugas', SuperadminPetugasController::class);
        Route::resource('members', MemberController::class)->except(['create', 'store']);
    });

});