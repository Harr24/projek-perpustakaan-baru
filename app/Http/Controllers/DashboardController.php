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
                // (Logika admin... biarkan saja, sudah benar)
                $data['pendingStudentsCount'] = User::where('role', 'siswa')
                                                    ->where('account_status', 'pending')
                                                    ->count();
                $data['totalBuku'] = Book::count();
                $data['anggotaAktif'] = User::whereIn('role', ['siswa', 'guru'])
                                            ->where('account_status', 'active')
                                            ->count();
                $data['pengajuanPinjaman'] = Borrowing::where('status', 'pending')
                                                      ->count();
                $data['terlambat'] = Borrowing::where('status', 'dipinjam')
                                           ->where('due_date', '<', now())
                                           ->count();
                break;

            case 'siswa':
            case 'guru':
                // (Logika lama untuk link 'Riwayat Peminjaman')
                $data['hasBorrowings'] = $user->borrowings()->exists();

                // ==================================================
                // LOGIKA WIDGET STATUS DINAMIS (DIUBAH UNTUK GURU)
                // ==================================================

                // 1. Ambil SEMUA pinjaman yang AKTIF (status 'dipinjam')
                $activeBorrowings = $user->borrowings()
                                        ->where('status', 'dipinjam')
                                        ->with('bookCopy.book') // Eager load relasi yg dibutuhkan
                                        ->latest('borrowed_at')
                                        ->get();

                // 2. Cek apakah ada pinjaman aktif
                if ($activeBorrowings->isNotEmpty()) {

                    // --- PERUBAHAN UTAMA: Logika Pengelompokan untuk Guru ---
                    if ($user->role == 'guru') {
                        // Kelompokkan berdasarkan ID buku induk (book_id dari bookCopy)
                        $groupedBorrowings = $activeBorrowings->groupBy('bookCopy.book_id')
                            ->map(function ($items) {
                                // Ambil data buku dari item pertama (semua sama dlm grup)
                                $firstItem = $items->first();
                                // Pastikan relasi bookCopy dan book ada sebelum mengakses propertinya
                                if ($firstItem && $firstItem->bookCopy && $firstItem->bookCopy->book) {
                                    return (object) [ // Kembalikan sebagai object agar mudah diakses di Blade
                                        'book' => $firstItem->bookCopy->book, // Model Book
                                        'count' => $items->count(), // Jumlah eksemplar
                                        'earliest_borrowed' => $items->min('borrowed_at'), // Tanggal pinjam paling awal
                                        'latest_due' => $items->max('due_date'), // Tanggal jatuh tempo paling akhir
                                    ];
                                }
                                return null; // Kembalikan null jika relasi tidak ditemukan
                            })->filter(); // Hapus item null dari hasil map

                        // Kirim data yang sudah dikelompokkan
                        $data['borrowingInfo'] = $groupedBorrowings;
                        $data['displayMode'] = 'grouped'; // Flag untuk view
                    } else {
                        // Untuk SISWA, kirim data individual seperti sebelumnya
                        $data['borrowingInfo'] = $activeBorrowings;
                        $data['displayMode'] = 'individual'; // Flag untuk view
                    }
                    // --- Akhir Perubahan Utama ---

                } else {
                    // 3. Jika TIDAK ADA pinjaman aktif
                    $data['borrowingInfo'] = null; // Set null agar view tahu
                    $data['displayMode'] = null;
                    // Siapkan kutipan statis
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