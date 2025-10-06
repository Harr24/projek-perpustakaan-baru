<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    /**
     * Menampilkan daftar buku yang sedang dipinjam atau terlambat.
     */
    public function index()
    {
        $activeBorrowings = Borrowing::whereIn('status', ['borrowed', 'overdue'])
                                    ->with('user', 'bookCopy.book')
                                    ->latest('borrowed_at')
                                    ->get();

        return view('admin.petugas.returns.index', compact('activeBorrowings'));
    }

    /**
     * Memproses pengembalian buku.
     */
    public function store(Request $request, Borrowing $borrowing)
    {
        // 1. Validasi untuk memastikan peminjaman ini valid untuk dikembalikan
        if (!in_array($borrowing->status, ['borrowed', 'overdue'])) {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dalam status aktif.');
        }

        // 2. Hitung hari keterlambatan (tidak termasuk Sabtu & Minggu)
        $dueDate = Carbon::parse($borrowing->due_at);
        $returnDate = Carbon::now();
        $lateDays = 0;

        if ($returnDate->isAfter($dueDate)) {
            // diffInDaysFiltered tidak menghitung hari akhir, jadi +1
            // dan callback untuk filter hari Sabtu & Minggu
            $lateDays = $dueDate->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isSaturday() && !$date->isSunday();
            }, $returnDate);
        }

        // 3. Hitung denda
        $fine = $lateDays * 1000; // Rp 1.000 per hari keterlambatan

        // 4. Update status peminjaman
        $borrowing->status = 'returned';
        $borrowing->returned_at = $returnDate;
        $borrowing->save();

        // 5. Update status eksemplar buku kembali menjadi 'tersedia'
        $bookCopy = $borrowing->bookCopy;
        $bookCopy->status = 'tersedia';
        $bookCopy->save();

        // 6. Buat pesan notifikasi
        $message = 'Buku berhasil dikembalikan.';
        if ($fine > 0) {
            $message .= ' Denda keterlambatan sebesar Rp ' . number_format($fine, 0, ',', '.') . ' (terlambat ' . $lateDays . ' hari kerja).';
        }

        return redirect()->back()->with('success', $message);
    }
}