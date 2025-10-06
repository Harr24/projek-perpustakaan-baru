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
        $borrowings = Borrowing::where('user_id', $userId)
                                ->with('bookCopy.book.genre')
                                ->latest('borrowed_at')
                                ->get();

        return view('borrowings.index', compact('borrowings'));
    }

    /**
     * Menampilkan halaman konfirmasi pengajuan pinjaman.
     */
    public function create(BookCopy $book_copy)
    {
        if ($book_copy->status !== 'tersedia') {
            return redirect()->back()->with('error', 'Eksemplar buku ini sedang tidak tersedia.');
        }

        $borrowDate = Carbon::now();
        $dueDate = Carbon::now();
        $daysAdded = 0;
        while ($daysAdded < 7) {
            $dueDate->addDay();
            if (!$dueDate->isSaturday() && !$dueDate->isSunday()) {
                $daysAdded++;
            }
        }

        return view('borrowings.create', compact('book_copy', 'borrowDate', 'dueDate'));
    }

    /**
     * Menyimpan pengajuan peminjaman baru dengan status 'pending'.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // ==========================================================
        // FITUR BARU: Cek Denda yang Belum Lunas
        // ==========================================================
        // Cek apakah user punya denda dari peminjaman sebelumnya yang belum lunas
        $hasUnpaidFines = Borrowing::where('user_id', $user->id)
                                    ->where('fine_amount', '>', 0)
                                    ->where('fine_status', 'unpaid')
                                    ->exists();
        
        if ($hasUnpaidFines) {
            // Jika punya, batalkan proses dan beri pesan error
            return redirect()->route('catalog.index')->with('error', 'Anda memiliki denda yang belum lunas. Mohon selesaikan pembayaran sebelum meminjam buku baru.');
        }
        // ==========================================================

        $request->validate([
            'book_copy_id' => 'required|exists:book_copies,id',
            'due_at' => 'required|date',
        ]);
        
        $bookCopy = BookCopy::find($request->input('book_copy_id'));

        if ($user->account_status !== 'active' || $bookCopy->status !== 'tersedia') {
            return redirect()->route('catalog.index')->with('error', 'Gagal mengajukan pinjaman. Akun Anda mungkin belum aktif atau buku sudah dalam proses pinjam.');
        }

        $bookCopy->status = 'pending';
        $bookCopy->save();

        Borrowing::create([
            'user_id' => $user->id,
            'book_copy_id' => $bookCopy->id,
            'borrowed_at' => Carbon::now(),
            'due_at' => new Carbon($request->input('due_at')),
            'status' => 'pending',
        ]);

        return redirect()->route('borrow.history')->with('success', 'Pengajuan pinjaman berhasil dikirim. Menunggu konfirmasi dari petugas.');
    }
}