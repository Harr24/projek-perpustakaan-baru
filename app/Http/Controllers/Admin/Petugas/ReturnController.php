<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\BookCopy; // <-- Pastikan ini di-import

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
            $dueDate = Carbon::parse($borrowing->due_at);
            $lateDays = 0;

            if ($returnDate->isAfter($dueDate)) {
                $lateDays = $dueDate->diffInDaysFiltered(function (Carbon $date) {
                    return !$date->isSaturday() && !$date->isSunday();
                }, $returnDate);
            }

            // Hitung denda keterlambatan SAJA
            $fine = $lateDays * 1000; // Asumsi denda terlambat 1000/hari

            $borrowing->status = 'returned';
            $borrowing->returned_at = $returnDate;
            $borrowing->fine_amount = $fine;
            $borrowing->late_days = $lateDays;
            $borrowing->returned_by = Auth::id();
            // Atur status denda berdasarkan jumlah denda
            $borrowing->fine_status = ($fine > 0) ? 'unpaid' : 'paid'; // Langsung lunas jika tidak ada denda
            $borrowing->save();

            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'tersedia';
            $bookCopy->save();
        });
        
        $message = 'Buku berhasil dikembalikan.';
        if ($borrowing->fine_amount > 0) {
            $message .= ' Denda keterlambatan sebesar Rp ' . number_format($borrowing->fine_amount, 0, ',', '.') . ' tercatat.';
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
        $petugasId = Auth::id();

        DB::transaction(function () use ($borrowingsToReturn, &$totalReturned, &$totalFine, $petugasId) {
            foreach ($borrowingsToReturn as $borrowing) {
                $returnDate = Carbon::now();
                $dueDate = Carbon::parse($borrowing->due_at);
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
                $borrowing->returned_by = $petugasId;
                $borrowing->fine_status = ($fine > 0) ? 'unpaid' : 'paid';
                $borrowing->save();

                $bookCopy = $borrowing->bookCopy;
                $bookCopy->status = 'tersedia';
                $bookCopy->save();
                
                $totalReturned++;
            }
        });

        $message = $totalReturned . ' buku berhasil dikembalikan.';
        if ($totalFine > 0) {
            $message .= ' Total denda keterlambatan yang tercatat sebesar Rp ' . number_format($totalFine, 0, ',', '.');
        }

        return redirect()->back()->with('success', $message);
    }

    // ==========================================================
    // METHOD BARU: Tandai Buku Hilang (LOGIKA DIUBAH)
    // ==========================================================
    public function markAsLost(Borrowing $borrowing)
    {
        // 1. Validasi Status Awal
        if (!in_array($borrowing->status, ['dipinjam', 'overdue'])) {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dalam status aktif.');
        }

        // --- MODIFIKASI: Denda buku hilang diatur menjadi 0 ---
        $lostFineAmount = 0; // Sebelumnya 50000

        DB::transaction(function () use ($borrowing, $lostFineAmount) {
            $processDate = Carbon::now();

            // 3. Update Status Buku (BookCopy) menjadi 'hilang'
            $bookCopy = $borrowing->bookCopy;
            if ($bookCopy) {
                $bookCopy->status = 'hilang'; // Ubah status eksemplar
                $bookCopy->save();
            } else {
                 // Batalkan jika data eksemplar tidak ditemukan
                 throw new \Exception("Data eksemplar buku tidak ditemukan untuk peminjaman ID: {$borrowing->id}");
            }

            // 4. Update Status Peminjaman (Borrowing)
            $borrowing->status = 'returned'; // Anggap transaksi selesai (bisa juga 'lost' jika Anda mau)
            $borrowing->returned_at = $processDate; // Catat tanggal hilang
            $borrowing->late_days = 0; // Denda terlambat dihapus
            
            // --- MODIFIKASI: Tetapkan denda menjadi 0 dan status lunas ---
            $borrowing->fine_amount = $lostFineAmount; // Set denda ke 0
            $borrowing->fine_status = 'paid'; // Set status lunas (karena 0)
            
            $borrowing->returned_by = Auth::id(); // Catat petugas
            $borrowing->save();
        });
        
        // 5. Redirect dengan Pesan Sukses
        // Pastikan relasi bookCopy dan book ada sebelum mengakses title
        $bookTitle = $borrowing->bookCopy && $borrowing->bookCopy->book ? $borrowing->bookCopy->book->title : '[Judul Tidak Ditemukan]';
        $bookCode = $borrowing->bookCopy ? $borrowing->bookCopy->book_code : '[Kode Tidak Ditemukan]';
        
        // --- MODIFIKASI: Hapus pesan denda ---
        $message = "Buku '{$bookTitle}' (Eksemplar: {$bookCode}) berhasil ditandai sebagai hilang.";
        // $message .= " Denda penggantian sebesar Rp ... tercatat."; // Baris ini dihapus

        return redirect()->back()->with('success', $message);
    }
}
