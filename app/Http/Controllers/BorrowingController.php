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
        $user = Auth::user();
        if ($book_copy->status !== 'tersedia') {
            return redirect()->back()->with('error', 'Eksemplar buku ini sedang tidak tersedia.');
        }

        $hasUnpaidFines = Borrowing::where('user_id', $user->id)->where('fine_amount', '>', 0)->where('fine_status', 'unpaid')->exists();
        if ($hasUnpaidFines) {
            return redirect()->route('catalog.show', $book_copy->book_id)->with('error', 'Anda memiliki denda yang belum lunas.');
        }

        // ==========================================================
        // --- TAMBAHAN: Validasi Batas Maksimal 3 Buku (Siswa) ---
        // ==========================================================
        if ($user->role == 'siswa') {
            // Hitung semua buku yang statusnya 'aktif' (sedang dipinjam atau menunggu persetujuan)
            $activeLoanCount = Borrowing::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'dipinjam', 'overdue'])
                ->count();
            
            if ($activeLoanCount >= 3) {
                return redirect()->route('catalog.show', $book_copy->book_id)
                         ->with('error', 'Gagal! Siswa hanya boleh meminjam maksimal 3 buku sekaligus (termasuk yang menunggu konfirmasi).');
            }
        }
        // ==========================================================


        // ==========================================================
        // --- PERBAIKAN: Menggunakan 'paket' ---
        // ==========================================================
        $isBookPackage = ($book_copy->book->book_type == 'paket');

        if ($isBookPackage && $user->role == 'siswa') {
            $hasActiveTextbookLoan = Borrowing::where('user_id', $user->id)
                ->whereIn('status', ['borrowed', 'pending'])
                ->whereHas('bookCopy.book', function ($query) {
                    $query->where('book_type', 'paket');
                })->exists();
            
            if ($hasActiveTextbookLoan) {
                return redirect()->route('catalog.show', $book_copy->book_id)->with('error', 'Anda tidak bisa meminjam lebih dari 1 buku paket.');
            }
        }
        // ==========================================================
        
        $borrowDate = Carbon::now();
        $dueDate = null; // Default null

        // ==========================================================
        // --- PERBAIKAN: Logika Batas Waktu ---
        // ==========================================================
        if ($book_copy->book->book_type !== 'laporan') {
            $dueDate = Carbon::now();
            $daysAdded = 0;
            while ($daysAdded < 7) { 
                $dueDate->addDay();
                if (!$dueDate->isSaturday() && !$dueDate->isSunday()) {
                    $daysAdded++;
                }
            }
        }
        // ==========================================================

        return view('borrowings.create', compact('book_copy', 'borrowDate', 'dueDate'));
    }

    public function store(BookCopy $book_copy)
    {
        $user = Auth::user();

        // ==========================================================
        // --- TAMBAHAN: Validasi Batas Maksimal 3 Buku (Siswa) ---
        // ==========================================================
        // Ini adalah validasi utama di sisi server
        if ($user->role == 'siswa') {
            $activeLoanCount = Borrowing::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'dipinjam', 'overdue'])
                ->count();
            
            if ($activeLoanCount >= 3) {
                return redirect()->back()
                         ->with('error', 'Gagal! Anda sudah mencapai batas maksimal 3 peminjaman buku (termasuk yang menunggu konfirmasi).');
            }
        }
        // ==========================================================


        // ==========================================================
        // --- Validasi Buku Laporan (Limit 1) ---
        // ==========================================================
        $book = $book_copy->book; // Ambil buku induknya

        if ($book->book_type == 'laporan') {
            $activeReportLoan = Borrowing::where('user_id', $user->id)
                ->whereHas('bookCopy.book', function ($query) {
                    $query->where('book_type', 'laporan');
                })
                ->whereIn('status', ['pending', 'borrowed']) 
                ->exists(); 

            if ($activeReportLoan) {
                return redirect()->back()->with('error', 'Anda hanya dapat meminjam 1 Buku Laporan pada satu waktu.');
            }
        }
        // ==========================================================


        // ==========================================================
        // --- PERBAIKAN: Menggunakan 'paket' ---
        // ==========================================================
        $isBookPackage = ($book_copy->book->book_type == 'paket');

        if ($user->role == 'siswa' && $isBookPackage) {
            $hasActiveTextbookLoan = Borrowing::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'borrowed']) 
                ->whereHas('bookCopy.book', function ($query) {
                    $query->where('book_type', 'paket');
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

        // ==========================================================
        // --- PERBAIKAN: Logika Batas Waktu (Dibuat Konsisten) ---
        // ==========================================================
        $dueDate = null; // Default null untuk 'laporan'
        
        if ($book_copy->book->book_type !== 'laporan') {
            $dueDate = Carbon::now();
            $daysAdded = 0;
            while ($daysAdded < 7) { 
                $dueDate->addDay();
                if (!$dueDate->isSaturday() && !$dueDate->isSunday()) {
                    $daysAdded++;
                }
            }
        }
        // ==========================================================

        Borrowing::create([
            'user_id' => $user->id,
            'book_copy_id' => $book_copy->id,
            'borrowed_at' => Carbon::now(),
            'due_at' => $dueDate, // Terapkan due_date baru (bisa null)
            'status' => 'pending',
        ]);

        return redirect()->route('borrow.history')->with('success', 'Pengajuan pinjaman berhasil dikirim. Silakan tunggu konfirmasi dari petugas.');
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

        $hasUnpaidFines = Borrowing::where('user_id', $user->id)->where('fine_amount', '>', 0)->where('fine_status', 'unpaid')->exists();
        if ($user->account_status !== 'active' || $hasUnpaidFines) {
            return redirect()->back()->with('error', 'Gagal, akun Anda belum aktif atau masih memiliki denda.');
        }

        // ==========================================================
        // --- Validasi Buku Laporan (Blokir Bulk) ---
        // ==========================================================
        if ($book->book_type == 'laporan') {
            return redirect()->back()->with('error', 'Buku Laporan tidak dapat dipinjam secara massal. Silakan pinjam satu per satu.');
        }
        // ==========================================================

        
        // ==========================================================
        // --- PERBAIKAN: Menggunakan 'paket' ---
        // ==========================================================
        $isBookPackage = ($book->book_type == 'paket');

        // Aksi ini hanya untuk guru DAN buku 'paket'
        if ($user->role !== 'guru' || !$isBookPackage) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan. Pinjaman massal hanya untuk Guru dan Tipe Buku Paket.');
        }
        // ==========================================================
        
        // CATATAN: Kita tidak menambahkan validasi "Max 3" di sini,
        // karena method ini khusus untuk GURU, bukan SISWA.

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

                // ==========================================================
                // --- PERBAIKAN: Logika Batas Waktu ---
                // ==========================================================
                $dueDate = null; 

                if ($book->book_type !== 'laporan') {
                    $dueDate = Carbon::now();
                    $daysAdded = 0;
                    while ($daysAdded < 7) { 
                        $dueDate->addDay();
                        if (!$dueDate->isSaturday() && !$dueDate->isSunday()) {
                            $daysAdded++;
                        }
                    }
                }
                // ==========================================================

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