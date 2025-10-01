<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    /**
     * Menampilkan riwayat peminjaman milik user yang sedang login.
     */
    public function index()
    {
        $userId = Auth::id();

        // Ambil data peminjaman milik user, urutkan dari yang terbaru
        // Eager load relasi untuk menghindari N+1 query problem
        $borrowings = Borrowing::where('user_id', $userId)
                               ->with('bookCopy.book.genre') // Memuat relasi bertingkat
                               ->latest('borrowed_at')
                               ->get();

        return view('borrowings.index', compact('borrowings'));
    }

    /**
     * Menyimpan data peminjaman baru.
     */
    public function store(Request $request, BookCopy $book_copy)
    {
        $user = Auth::user();

        // 1. Pengecekan Keamanan dan Validasi
        // Pastikan user sudah di-approve
        if ($user->account_status !== 'active') {
            return redirect()->back()->with('error', 'Akun Anda belum aktif. Tidak dapat meminjam buku.');
        }

        // Pastikan salinan buku tersedia
        if ($book_copy->status !== 'tersedia') {
            return redirect()->back()->with('error', 'Buku ini sedang tidak tersedia untuk dipinjam.');
        }

        // 2. Buat data peminjaman baru
        Borrowing::create([
            'user_id' => $user->id,
            'book_copy_id' => $book_copy->id,
            'borrowed_at' => Carbon::now(),
            'due_at' => Carbon::now()->addDays(7), // Batas waktu peminjaman 7 hari
        ]);

        // 3. Update status salinan buku menjadi 'dipinjam'
        $book_copy->status = 'dipinjam';
        $book_copy->save();

        // 4. Redirect ke halaman riwayat dengan pesan sukses
        return redirect()->route('borrow.history')->with('success', 'Buku berhasil dipinjam!');
    }
}

