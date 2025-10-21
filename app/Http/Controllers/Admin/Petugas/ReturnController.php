<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // <-- Import class Auth

class ReturnController extends Controller
{
    /**
     * Menampilkan daftar buku yang sedang dipinjam, dengan fungsionalitas PENCARIAN.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Borrowing::whereIn('status', ['dipinjam', 'overdue'])
                            ->with('user', 'bookCopy.book');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($subq) use ($search) {
                    $subq->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('bookCopy.book', function ($subq) use ($search) {
                    $subq->where('title', 'like', '%' . $search . '%');
                });
            });
        }
        
        $activeBorrowings = $query->latest('approved_at')->get();

        return view('admin.petugas.returns.index', compact('activeBorrowings', 'search'));
    }

    /**
     * Memproses pengembalian buku tunggal.
     */
    public function store(Borrowing $borrowing)
    {
        if (!in_array($borrowing->status, ['dipinjam', 'overdue'])) {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dalam status aktif.');
        }

        DB::transaction(function () use ($borrowing) {
            $returnDate = Carbon::now();
            $dueDate = Carbon::parse($borrowing->due_at); // Menggunakan due_at untuk konsistensi
            $lateDays = 0;

            if ($returnDate->isAfter($dueDate)) {
                $lateDays = $dueDate->diffInDaysFiltered(function (Carbon $date) {
                    return !$date->isSaturday() && !$date->isSunday();
                }, $returnDate);
            }

            $fine = $lateDays * 1000;

            // Update record peminjaman
            $borrowing->status = 'returned';
            $borrowing->returned_at = $returnDate;
            $borrowing->fine_amount = $fine;
            $borrowing->late_days = $lateDays;
            
            // ==========================================================
            // PENAMBAHAN: Simpan ID petugas yang memproses pengembalian
            // ==========================================================
            $borrowing->returned_by = Auth::id();
            
            $borrowing->save();

            // Update status salinan buku
            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'tersedia';
            $bookCopy->save();
        });
        
        $message = 'Buku berhasil dikembalikan.';
        if ($borrowing->fine_amount > 0) {
            $message .= ' Denda keterlambatan sebesar Rp ' . number_format($borrowing->fine_amount, 0, ',', '.') . ' (terlambat ' . $borrowing->late_days . ' hari kerja) tercatat.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Memproses pengembalian buku massal.
     */
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'borrowing_ids' => 'required|array',
            'borrowing_ids.*' => 'exists:borrowings,id',
        ]);

        $borrowingIds = $request->input('borrowing_ids');
        $borrowingsToReturn = Borrowing::whereIn('id', $borrowingIds)->whereIn('status', ['dipinjam', 'overdue'])->get();

        if ($borrowingsToReturn->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada buku valid yang dipilih untuk dikembalikan.');
        }

        $totalReturned = 0;
        $totalFine = 0;
        $petugasId = Auth::id(); // Ambil ID petugas saat ini

        DB::transaction(function () use ($borrowingsToReturn, &$totalReturned, &$totalFine, $petugasId) {
            foreach ($borrowingsToReturn as $borrowing) {
                $returnDate = Carbon::now();
                $dueDate = Carbon::parse($borrowing->due_at); // Menggunakan due_at
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

                // ==========================================================
                // PENAMBAHAN: Simpan ID petugas yang memproses pengembalian
                // ==========================================================
                $borrowing->returned_by = $petugasId;

                $borrowing->save();

                $bookCopy = $borrowing->bookCopy;
                $bookCopy->status = 'tersedia';
                $bookCopy->save();
                
                $totalReturned++;
            }
        });

        $message = $totalReturned . ' buku berhasil dikembalikan.';
        if ($totalFine > 0) {
            $message .= ' Total denda yang tercatat sebesar Rp ' . number_format($totalFine, 0, ',', '.');
        }

        return redirect()->back()->with('success', $message);
    }
}

