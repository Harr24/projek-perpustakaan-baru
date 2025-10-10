<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama dengan data yang relevan untuk setiap role.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $data = []; // Inisialisasi array untuk data tambahan

        // Siapkan data spesifik berdasarkan role pengguna
        switch ($user->role) {
            case 'petugas':
            case 'superadmin':
                // Untuk petugas & superadmin, hitung siswa yang menunggu verifikasi
                $data['pendingStudentsCount'] = User::where('role', 'siswa')
                                                    ->where('account_status', 'pending')
                                                    ->count();
                break;
            
            case 'siswa':
            case 'guru':
                // Untuk siswa & guru, cek apakah mereka punya riwayat peminjaman
                $data['hasBorrowings'] = $user->borrowings()->exists();
                break;
        }

        // Kirim data user dan data tambahan yang sudah disiapkan ke view 'dashboard'.
        return view('dashboard', array_merge(['user' => $user], $data));
    }
}

