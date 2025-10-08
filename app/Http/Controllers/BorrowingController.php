<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function store(Request $request)
    {
        $request->validate(['book_copy_id' => 'required|exists:book_copies,id', 'due_at' => 'required|date']);
        $user = Auth::user();
        $bookCopy = BookCopy::find($request->input('book_copy_id'));
        if ($user->account_status !== 'active' || $bookCopy->status !== 'tersedia') {
            return redirect()->route('catalog.index')->with('error', 'Gagal mengajukan pinjaman.');
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
        return redirect()->route('borrow.history')->with('success', 'Pengajuan pinjaman berhasil dikirim.');
    }

    public function storeBulk(Request $request)
    {
        $request->validate(['book_id' => 'required|exists:books,id', 'quantity' => 'required|integer|min:1']);
        $user = Auth::user();
        $book = Book::find($request->book_id);
        $quantity = $request->quantity;

        // ==========================================================
        // PENGECEKAN YANG PERLU DITAMBAHKAN
        // ==========================================================
        $hasUnpaidFines = Borrowing::where('user_id', $user->id)->where('fine_amount', '>', 0)->where('fine_status', 'unpaid')->exists();
        if ($user->account_status !== 'active' || $hasUnpaidFines) {
            return redirect()->back()->with('error', 'Gagal, akun Anda belum aktif atau masih memiliki denda.');
        }
        // ==========================================================
        
        if ($user->role !== 'guru' || !$book->is_textbook) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan.');
        }

        $availableCopies = BookCopy::where('book_id', $book->id)->where('status', 'tersedia')->take($quantity)->get();
        if ($availableCopies->count() < $quantity) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi. Hanya tersedia ' . $availableCopies->count() . ' eksemplar.');
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
            Borrowing::create(['user_id' => $user->id, 'book_copy_id' => $copy->id, 'borrowed_at' => Carbon::now(), 'due_at' => $dueDate, 'status' => 'pending']);
        }
        return redirect()->route('borrow.history')->with('success', $quantity . ' pengajuan pinjaman berhasil dikirim.');
    }
}