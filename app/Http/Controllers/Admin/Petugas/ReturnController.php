<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Jangan lupa import DB

class ReturnController extends Controller
{
    /**
     * Menampilkan daftar buku yang sedang dipinjam atau terlambat.
     */
    public function index()
    {
        // ==========================================================
        // PERBAIKAN UTAMA: Mencari status 'approved' yang sudah kita set
        // saat menyetujui peminjaman. Status 'overdue' juga tetap dicari.
        // ==========================================================
        $activeBorrowings = Borrowing::whereIn('status', ['dipinjam', 'overdue'])
                                        ->with('user', 'bookCopy.book')
                                        // PERBAIKAN KECIL: Urutkan berdasarkan tanggal disetujui
                                        ->latest('approved_at')
                                        ->get();

        return view('admin.petugas.returns.index', compact('activeBorrowings'));
    }

    /**
     * Memproses pengembalian buku tunggal.
     */
    public function store(Borrowing $borrowing) // Hapus Request $request karena tidak dipakai
    {
        // PERBAIKAN: Validasi juga harus mencari status 'approved'
        if (!in_array($borrowing->status, ['dipinjam', 'overdue'])) {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dalam status aktif.');
        }

        // BEST PRACTICE: Bungkus dengan transaction untuk keamanan data
        DB::transaction(function () use ($borrowing) {
            $dueDate = Carbon::parse($borrowing->due_date); // Pastikan nama kolom 'due_date'
            $returnDate = Carbon::now();
            $lateDays = 0;

            if ($returnDate->isAfter($dueDate)) {
                $lateDays = $dueDate->diffInDaysFiltered(function (Carbon $date) {
                    return !$date->isSaturday() && !$date->isSunday();
                }, $returnDate);
            }

            $fine = $lateDays * 1000; // Asumsi denda 1000 per hari

            // Update record peminjaman
            $borrowing->status = 'returned';
            $borrowing->returned_at = $returnDate;
            $borrowing->fine_amount = $fine;
            $borrowing->late_days = $lateDays;
            $borrowing->save();

            // Update status salinan buku
            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'tersedia';
            $bookCopy->save();
        });
        
        // Buat pesan dinamis setelah transaction berhasil
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
        // PERBAIKAN: Cari juga status 'approved'
       $borrowingsToReturn = Borrowing::whereIn('id', $borrowingIds)->whereIn('status', ['dipinjam', 'overdue'])->get();

        if ($borrowingsToReturn->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada buku valid yang dipilih untuk dikembalikan.');
        }

        $totalReturned = 0;
        $totalFine = 0;

        // BEST PRACTICE: Satu transaction untuk semua proses
        DB::transaction(function () use ($borrowingsToReturn, &$totalReturned, &$totalFine) {
            foreach ($borrowingsToReturn as $borrowing) {
                $dueDate = Carbon::parse($borrowing->due_date); // Pastikan nama kolom 'due_date'
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