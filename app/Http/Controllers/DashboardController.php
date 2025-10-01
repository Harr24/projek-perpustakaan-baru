<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Inisialisasi variabel hitungan notifikasi.
        $pendingStudentsCount = 0;

        // Ambil user yang sedang login.
        $user = Auth::user();

        // Hanya hitung notifikasi jika yang login adalah 'petugas'.
        if ($user && $user->role == 'petugas') {
            
            // PERUBAHAN UTAMA DI SINI:
            // Mengubah logika untuk menghitung siswa berdasarkan 'account_status'.
            $pendingStudentsCount = User::where('role', 'siswa')
                                          ->where('account_status', 'pending') // Menggunakan logika baru
                                          ->count();
        }

        // Kirim data hitungan ke view 'dashboard'.
        // Variabel $pendingStudentsCount sekarang bisa diakses di dashboard.blade.php
        return view('dashboard', [
            'pendingStudentsCount' => $pendingStudentsCount
        ]);
    }
}

