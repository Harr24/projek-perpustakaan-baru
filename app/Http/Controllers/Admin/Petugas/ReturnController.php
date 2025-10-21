<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    /**
     * Menampilkan daftar buku yang sedang dipinjam, dengan fungsionalitas PENCARIAN.
     */
    public function index(Request $request) // Terima object Request
    {
        $search = $request->input('search'); // Ambil input dari form pencarian

        // Mulai query dasar
        $query = Borrowing::whereIn('status', ['dipinjam', 'overdue'])
                            ->with('user', 'bookCopy.book');

        // Jika ada input pencarian, tambahkan filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Cari berdasarkan nama peminjam
                $q->whereHas('user', function ($subq) use ($search) {
                    $subq->where('name', 'like', '%' . $search . '%');
                })
                // ATAU cari berdasarkan judul buku
                ->orWhereHas('bookCopy.book', function ($subq) use ($search) {
                    $subq->where('title', 'like', '%' . $search . '%');
                });
            });
        }
        
        $activeBorrowings = $query->latest('approved_at')->get();

        // Kirim data dan variabel 'search' ke view
        return view('admin.petugas.returns.index', compact('activeBorrowings', 'search'));
    }

    /**
     * Memproses pengembalian buku tunggal. (Tidak ada perubahan)
     */
    public function store(Borrowing $borrowing)
    {
        if (!in_array($borrowing->status, ['dipinjam', 'overdue'])) {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dalam status aktif.');
        }

        DB::transaction(function () use ($borrowing) {
            $dueDate = Carbon::parse($borrowing->due_date);
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
        });
        
        $message = 'Buku berhasil dikembalikan.';
        if ($borrowing->fine_amount > 0) {
            $message .= ' Denda keterlambatan sebesar Rp ' . number_format($borrowing->fine_amount, 0, ',', '.') . ' (terlambat ' . $borrowing->late_days . ' hari kerja) tercatat.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Memproses pengembalian buku massal. (Tidak ada perubahan)
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

        DB::transaction(function () use ($borrowingsToReturn, &$totalReturned, &$totalFine) {
            foreach ($borrowingsToReturn as $borrowing) {
                $dueDate = Carbon::parse($borrowing->due_date);
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
        });

        $message = $totalReturned . ' buku berhasil dikembalikan.';
        if ($totalFine > 0) {
            $message .= ' Total denda yang tercatat sebesar Rp ' . number_format($totalFine, 0, ',', '.');
        }

        return redirect()->back()->with('success', $message);
    }
}
