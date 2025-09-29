<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Petugas\VerificationController;
use App\Http\Controllers\Admin\Petugas\GenreController;
use App\Http\Controllers\Admin\Petugas\BookController; // <-- TAMBAHKAN INI

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Arahkan halaman utama ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// == RUTE UNTUK TAMU (YANG BELUM LOGIN) ==
Route::middleware('guest')->group(function () {
    // Menampilkan form
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

    // Memproses data form
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});


// == RUTE UNTUK PENGGUNA YANG SUDAH LOGIN ==
Route::middleware('auth')->group(function () {
    
    // Rute umum untuk semua role yang sudah login
    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // == RUTE KHUSUS UNTUK ROLE PETUGAS ==
    Route::middleware('role:petugas')->prefix('admin/petugas')->name('admin.petugas.')->group(function () {
        
        // Rute untuk verifikasi
        Route::get('/verifikasi-siswa', [VerificationController::class, 'index'])->name('verification.index');
        Route::post('/verifikasi-siswa/{user}/approve', [VerificationController::class, 'approve'])->name('verification.approve');
        Route::post('/verifikasi-siswa/{user}/reject', [VerificationController::class, 'reject'])->name('verification.reject');

        // Rute untuk mengelola Genre
        Route::resource('genres', GenreController::class);

        // Rute untuk mengelola Buku
        Route::resource('books', BookController::class); // <-- TAMBAHKAN INI

    });

    // == RUTE KHUSUS UNTUK ROLE SUPERADMIN ==
    // Route::middleware('role:superadmin')->prefix('admin/superadmin')->name('admin.superadmin.')->group(function () {
    //     // Rute untuk Superadmin
    // });
});