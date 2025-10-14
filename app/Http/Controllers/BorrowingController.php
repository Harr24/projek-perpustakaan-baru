<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // 1. WAJIB DI-IMPORT UNTUK TRANSACTION

class BorrowingController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $borrowings = Borrowing::where('user_id', $userId)->with('bookCopy.book.genre')->latest('borrowed_at')->get();
        return view('borrowings.index', compact('borrowings'));
    }

    public function create(BookCopy $book_copy)
    {
        $user = Auth::user();
        if ($book_copy->status !== 'tersedia') {
            return redirect()->back()->with('error', 'Eksemplar buku ini sedang tidak tersedia.');
        }

        $hasUnpaidFines = Borrowing::where('user_id', $user->id)->where('fine_amount', '>', 0)->where('fine_status', 'unpaid')->exists();
        if ($hasUnpaidFines) {
            return redirect()->route('catalog.show', $book_copy->book_id)->with('error', 'Anda memiliki denda yang belum lunas.');
        }

        if ($book_copy->book->is_textbook && $user->role == 'siswa') {
            $hasActiveTextbookLoan = Borrowing::where('user_id', $user->id)
                ->whereIn('status', ['borrowed', 'pending'])
                ->whereHas('bookCopy.book', function ($query) {
                    $query->where('is_textbook', true);
                })->exists();
            if ($hasActiveTextbookLoan) {
                return redirect()->route('catalog.show', $book_copy->book_id)->with('error', 'Anda tidak bisa meminjam lebih dari 1 buku paket.');
            }
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

    public function store(Request $request, BookCopy $book_copy)
    {
        $request->validate(['due_at' => 'sometimes|date']); // 'sometimes' jika tidak selalu ada
        $user = Auth::user();

        if ($user->account_status !== 'active' || $book_copy->status !== 'tersedia') {
            return redirect()->route('catalog.index')->with('error', 'Gagal mengajukan pinjaman.');
        }

        $book_copy->status = 'pending';
        $book_copy->save();

        Borrowing::create([
            'user_id' => $user->id,
            'book_copy_id' => $book_copy->id,
            'borrowed_at' => Carbon::now(),
            'due_at' => new Carbon($request->input('due_at', now()->addDays(7))), // Default jika due_at tidak ada
            'status' => 'pending',
        ]);

        return redirect()->route('borrow.history')->with('success', 'Pengajuan pinjaman berhasil dikirim.');
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id', 
            'quantity' => 'required|integer|min:1'
        ]);
        
        $user = Auth::user();
        $book = Book::find($request->book_id);
        $quantity = $request->quantity;

        // Validasi awal di luar transaksi
        $hasUnpaidFines = Borrowing::where('user_id', $user->id)->where('fine_amount', '>', 0)->where('fine_status', 'unpaid')->exists();
        if ($user->account_status !== 'active' || $hasUnpaidFines) {
            return redirect()->back()->with('error', 'Gagal, akun Anda belum aktif atau masih memiliki denda.');
        }
        
        if ($user->role !== 'guru' || !$book->is_textbook) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan.');
        }

        // ==========================================================
        // 2. LOGIKA UTAMA DIBUNGKUS DALAM DB::TRANSACTION
        // ==========================================================
        try {
            DB::transaction(function () use ($book, $quantity, $user) {
                $availableCopies = BookCopy::where('book_id', $book->id)
                                    ->where('status', 'tersedia')
                                    ->lockForUpdate() // Mengunci baris untuk mencegah race condition
                                    ->take($quantity)
                                    ->get();

                if ($availableCopies->count() < $quantity) {
                    // Melempar exception akan otomatis me-rollback transaksi
                    throw new \Exception('Stok tidak mencukupi. Hanya tersedia ' . $availableCopies->count() . ' eksemplar.');
                }

                $dueDate = Carbon::now();
                $daysAdded = 0;
                while ($daysAdded < 7) {
                    $dueDate->addDay();
                    if (!$dueDate->isSaturday() && !$dueDate->isSunday()) {
                        $daysAdded++;
                    }
                }

                foreach ($availableCopies as $copy) {
                    $copy->status = 'pending';
                    $copy->save();
                    
                    Borrowing::create([
                        'user_id' => $user->id, 
                        'book_copy_id' => $copy->id, 
                        'borrowed_at' => Carbon::now(), 
                        'due_at' => $dueDate, 
                        'status' => 'pending'
                    ]);
                }
            });
        } catch (\Exception $e) {
            // Jika transaksi gagal (misal karena stok tidak cukup), kembalikan dengan pesan error
            return redirect()->back()->with('error', $e->getMessage());
        }
        // ==========================================================

        return redirect()->route('borrow.history')->with('success', $quantity . ' pengajuan pinjaman berhasil dikirim.');
    }
}