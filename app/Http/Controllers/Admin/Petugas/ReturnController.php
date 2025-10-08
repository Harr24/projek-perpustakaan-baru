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
     * Memproses pengembalian buku tunggal.
     */
    public function store(Request $request, Borrowing $borrowing)
    {
        if (!in_array($borrowing->status, ['borrowed', 'overdue'])) {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dalam status aktif.');
        }

        $dueDate = Carbon::parse($borrowing->due_at);
        $returnDate = Carbon::now();
        $lateDays = 0;

        if ($returnDate->isAfter($dueDate)) {
            $lateDays = $dueDate->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isSaturday() && !$date->isSunday();
            }, $returnDate);
        }

        $fine = $lateDays * 1000;

        $borrowing->status = 'returned';
        $borrowing->returned_at = $returnDate;
        $borrowing->fine_amount = $fine;
        $borrowing->late_days = $lateDays;
        $borrowing->save();

        $bookCopy = $borrowing->bookCopy;
        $bookCopy->status = 'tersedia';
        $bookCopy->save();

        $message = 'Buku berhasil dikembalikan.';
        if ($fine > 0) {
            $message .= ' Denda keterlambatan sebesar Rp ' . number_format($fine, 0, ',', '.') . ' (terlambat ' . $lateDays . ' hari kerja) tercatat.';
        }

        return redirect()->back()->with('success', $message);
    }

    // ==========================================================
    // TAMBAHAN: Method baru untuk pengembalian massal
    // ==========================================================
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'borrowing_ids' => 'required|array',
            'borrowing_ids.*' => 'exists:borrowings,id',
        ]);

        $borrowingIds = $request->input('borrowing_ids');
        $borrowingsToReturn = Borrowing::whereIn('id', $borrowingIds)->whereIn('status', ['borrowed', 'overdue'])->get();

        if ($borrowingsToReturn->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada buku valid yang dipilih untuk dikembalikan.');
        }

        $totalReturned = 0;
        $totalFine = 0;

        foreach ($borrowingsToReturn as $borrowing) {
            $dueDate = Carbon::parse($borrowing->due_at);
            $returnDate = Carbon::now();
            $lateDays = 0;

            if ($returnDate->isAfter($dueDate)) {
                $lateDays = $dueDate->diffInDaysFiltered(fn($date) => !$date->isSaturday() && !$date->isSunday(), $returnDate);
            }

            $fine = $lateDays * 1000;
            $totalFine += $fine;

            $borrowing->status = 'returned';
            $borrowing->returned_at = $returnDate;
            $borrowing->fine_amount = $fine;
            $borrowing->late_days = $lateDays;
            $borrowing->save();

            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'tersedia';
            $bookCopy->save();
            
            $totalReturned++;
        }

        $message = $totalReturned . ' buku berhasil dikembalikan.';
        if ($totalFine > 0) {
            $message .= ' Total denda yang tercatat sebesar Rp ' . number_format($totalFine, 0, ',', '.');
        }

        return redirect()->back()->with('success', $message);
    }
}