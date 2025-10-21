<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Import Model yang sudah kita pastikan benar
use App\Models\Book;
use App\Models\Borrowing; 

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
                
                // (Logika lama untuk badge notifikasi)
                $data['pendingStudentsCount'] = User::where('role', 'siswa')
                                                    ->where('account_status', 'pending')
                                                    ->count();

                // === Logika untuk Kartu Statistik ===

                // 1. Hitung total buku
                $data['totalBuku'] = Book::count();

                // 2. Hitung anggota aktif
                $data['anggotaAktif'] = User::whereIn('role', ['siswa', 'guru'])
                                            ->where('account_status', 'active') 
                                            ->count();

                // 3. Hitung pengajuan peminjaman (status 'pending')
                // === PERBAIKAN: Menggunakan kolom 'status' ===
                $data['pengajuanPinjaman'] = Borrowing::where('status', 'pending') 
                                                       ->count();

                // 4. Hitung buku yang terlambat
                // === PERBAIKAN: Menggunakan kolom 'status' dan 'due_date' ===
                $data['terlambat'] = Borrowing::where('status', 'dipinjam')
                                             ->where('due_date', '<', now()) // <-- Diubah
                                             ->count();
                
                break;
            
            case 'siswa':
            case 'guru':
                // (Logika lama untuk member)
                $data['hasBorrowings'] = $user->borrowings()->exists();
                break;
        }

        // Kirim semua data ke view
        return view('dashboard', array_merge(['user' => $user], $data));
    }
}