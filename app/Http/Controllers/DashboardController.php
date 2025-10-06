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
        // Ambil user yang sedang login.
        $user = Auth::user();

        // Inisialisasi variabel-variabel yang akan dikirim ke view.
        $pendingStudentsCount = 0;
        $hasBorrowings = false;

        // Hanya hitung notifikasi jika yang login adalah 'petugas'.
        if ($user && $user->role == 'petugas') {
            $pendingStudentsCount = User::where('role', 'siswa')
                                        ->where('account_status', 'pending')
                                        ->count();
        }
        
        // ==========================================================
        // TAMBAHAN: Cek riwayat peminjaman jika user adalah siswa atau guru
        // ==========================================================
        if ($user && in_array($user->role, ['siswa', 'guru'])) {
            $hasBorrowings = $user->borrowings()->exists();
        }

        // Kirim semua data yang relevan ke view 'dashboard'.
        return view('dashboard', [
            'pendingStudentsCount' => $pendingStudentsCount,
            'hasBorrowings' => $hasBorrowings
        ]);
    }
}