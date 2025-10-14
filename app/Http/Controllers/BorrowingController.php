<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // ... Method ini tidak perlu diubah ...
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

    public function store(BookCopy $book_copy)
    {
        $user = Auth::user();

        // ==========================================================
        // LOGIKA BARU: Batasi peminjaman buku paket untuk siswa
        // ==========================================================
        if ($user->role == 'siswa' && $book_copy->book->is_textbook) {
            $hasActiveTextbookLoan = Borrowing::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'borrowed']) // Cek pinjaman yang masih aktif (pending atau sudah dipinjam)
                ->whereHas('bookCopy.book', function ($query) {
                    $query->where('is_textbook', true);
                })->exists();

            if ($hasActiveTextbookLoan) {
                return redirect()->back()->with('error', 'Siswa hanya dapat meminjam satu buku paket dalam satu waktu.');
            }
        }
        // ==========================================================

        if ($user->account_status !== 'active' || $book_copy->status !== 'tersedia') {
            return redirect()->route('catalog.index')->with('error', 'Gagal mengajukan pinjaman. Akun atau buku tidak valid.');
        }
        
        $book_copy->status = 'pending';
        $book_copy->save();

        Borrowing::create([
            'user_id' => $user->id,
            'book_copy_id' => $book_copy->id,
            'borrowed_at' => Carbon::now(),
            'due_at' => now()->addDays(7),
            'status' => 'pending',
        ]);

        return redirect()->route('borrow.history')->with('success', 'Pengajuan pinjaman berhasil dikirim. Silakan tunggu konfirmasi dari petugas.');
    }

    public function storeBulk(Request $request)
    {
        // ... Method ini tidak perlu diubah ...
        $request->validate([
            'book_id' => 'required|exists:books,id', 
            'quantity' => 'required|integer|min:1'
        ]);
        
        $user = Auth::user();
        $book = Book::find($request->book_id);
        $quantity = $request->quantity;

        $hasUnpaidFines = Borrowing::where('user_id', $user->id)->where('fine_amount', '>', 0)->where('fine_status', 'unpaid')->exists();
        if ($user->account_status !== 'active' || $hasUnpaidFines) {
            return redirect()->back()->with('error', 'Gagal, akun Anda belum aktif atau masih memiliki denda.');
        }
        
        if ($user->role !== 'guru' || !$book->is_textbook) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan.');
        }

        try {
            DB::transaction(function () use ($book, $quantity, $user) {
                $availableCopies = BookCopy::where('book_id', $book->id)
                                    ->where('status', 'tersedia')
                                    ->lockForUpdate()
                                    ->take($quantity)
                                    ->get();

                if ($availableCopies->count() < $quantity) {
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
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('borrow.history')->with('success', $quantity . ' pengajuan pinjaman berhasil dikirim.');
    }
}
