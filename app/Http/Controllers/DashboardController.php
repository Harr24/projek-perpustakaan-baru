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
                // ==========================================================
                // KEMBALIKAN NAMA VARIABEL ke $pendingStudentsCount
                // ==========================================================
                $data['pendingStudentsCount'] = User::where('role', 'siswa')
                                                    ->where('account_status', 'pending')
                                                    ->count();

                // (Data lain yang sudah ada)
                $data['totalBuku'] = Book::count();
                $data['anggotaAktif'] = User::whereIn('role', ['siswa', 'guru'])
                                            ->where('account_status', 'active')
                                            ->count();
                $data['pengajuanPinjaman'] = Borrowing::where('status', 'pending')
                                                      ->count();
                // Pastikan menggunakan 'due_at'
                $data['terlambat'] = Borrowing::where('status', 'dipinjam')
                                            ->where('due_at', '<', now()) // Tetap gunakan due_at
                                            ->count();
                break;

            case 'siswa':
            case 'guru':
                // (Logika lama untuk link 'Riwayat Peminjaman')
                $data['hasBorrowings'] = $user->borrowings()->exists();

                // LOGIKA WIDGET STATUS DINAMIS (DIUBAH UNTUK GURU)
                $activeBorrowings = $user->borrowings()
                                        ->where('status', 'dipinjam')
                                        ->with('bookCopy.book') // Eager load relasi yg dibutuhkan
                                        ->latest('borrowed_at')
                                        ->get();

                if ($activeBorrowings->isNotEmpty()) {
                    if ($user->role == 'guru') {
                        $groupedBorrowings = $activeBorrowings->groupBy('bookCopy.book_id')
                            ->map(function ($items) {
                                $firstItem = $items->first();
                                if ($firstItem && $firstItem->bookCopy && $firstItem->bookCopy->book) {
                                    return (object) [ // Kembalikan sebagai object
                                        'book' => $firstItem->bookCopy->book,
                                        'count' => $items->count(),
                                        'earliest_borrowed' => $items->min('borrowed_at'),
                                        'latest_due' => $items->max('due_at'), // Gunakan due_at
                                    ];
                                }
                                return null;
                            })->filter();

                        $data['borrowingInfo'] = $groupedBorrowings;
                        $data['displayMode'] = 'grouped';
                    } else {
                        $data['borrowingInfo'] = $activeBorrowings;
                        $data['displayMode'] = 'individual';
                    }
                } else {
                    $data['borrowingInfo'] = null;
                    $data['displayMode'] = null;
                    $quote = [
                        'content' => 'Membaca adalah jendela dunia. Semakin banyak membaca, semakin banyak kita tahu.',
                        'author' => 'Pribahasa'
                    ];
                    $data['quote'] = $quote;
                }
                break;
        }

        // Kirim semua data ke view
        return view('dashboard', array_merge(['user' => $user], $data));
    }
}

