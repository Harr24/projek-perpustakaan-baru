<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Pastikan DB di-import
use Illuminate\Support\Facades\Auth;
use App\Models\BookCopy; 

class ReturnController extends Controller
{
    /**
     * Menampilkan daftar buku yang sedang dipinjam, dengan fungsionalitas PENCARIAN.
     */
    public function index(Request $request)
    {
        // ... (Tidak ada perubahan di method index) ...
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

        // ==========================================================
        // --- TAMBAHAN: Ambil Daftar Tanggal Merah ---
        // ==========================================================
        // Mengambil TANGGAL saja (format Y-m-d) dari tabel holidays
        $holidayDates = DB::table('holidays')
                            ->pluck('holiday_date')
                            ->map(fn($dateStr) => (new Carbon($dateStr))->format('Y-m-d'))
                            ->toArray();
        // ==========================================================

        DB::transaction(function () use ($borrowing, $holidayDates) { // <-- Kirim $holidayDates ke transaction
            $returnDate = Carbon::now();

            $book = $borrowing->bookCopy->book;
            $dueDate = $borrowing->due_date ? Carbon::parse($borrowing->due_date) : null; 
            
            $lateDays = 0;
            $fine = 0; 

            if ($book->book_type != 'laporan' && $dueDate && $returnDate->isAfter($dueDate)) {
                
                // ==========================================================
                // --- MODIFIKASI: Logika Filter Denda ---
                // ==========================================================
                $lateDays = $dueDate->diffInDaysFiltered(function (Carbon $date) use ($holidayDates) {
                    // Cek 1: Apakah akhir pekan (Sabtu/Minggu)?
                    $isWeekend = $date->isSaturday() || $date->isSunday();
                    
                    // Cek 2: Apakah tanggal merah (dari database)?
                    $isHoliday = in_array($date->format('Y-m-d'), $holidayDates);

                    // Kembalikan TRUE (hitung hari) HANYA JIKA BUKAN akhir pekan DAN BUKAN tanggal merah
                    return !$isWeekend && !$isHoliday;
                }, $returnDate);
                // ==========================================================

                $fine = $lateDays * 1000; 
            }

            $borrowing->status = 'returned';
            $borrowing->returned_at = $returnDate;
            $borrowing->fine_amount = $fine;
            $borrowing->late_days = $lateDays;
            $borrowing->returned_by = Auth::id();
            $borrowing->fine_status = ($fine > 0) ? 'unpaid' : 'paid'; 
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

        // ==========================================================
        // --- TAMBAHAN: Ambil Daftar Tanggal Merah (untuk massal) ---
        // ==========================================================
        $holidayDates = DB::table('holidays')
                            ->pluck('holiday_date')
                            ->map(fn($dateStr) => (new Carbon($dateStr))->format('Y-m-d'))
                            ->toArray();
        // ==========================================================

        $totalReturned = 0;
        $totalFine = 0;
        $petugasId = Auth::id();

        DB::transaction(function () use ($borrowingsToReturn, &$totalReturned, &$totalFine, $petugasId, $holidayDates) { // <-- Kirim $holidayDates
            foreach ($borrowingsToReturn as $borrowing) {
                $returnDate = Carbon::now();

                $book = $borrowing->bookCopy->book;
                $dueDate = $borrowing->due_date ? Carbon::parse($borrowing->due_date) : null; 
                
                $lateDays = 0;
                $fine = 0; 

                if ($book->book_type != 'laporan' && $dueDate && $returnDate->isAfter($dueDate)) {
                    // ==========================================================
                    // --- MODIFIKASI: Logika Filter Denda ---
                    // ==========================================================
                    $lateDays = $dueDate->diffInDaysFiltered(function (Carbon $date) use ($holidayDates) {
                        $isWeekend = $date->isSaturday() || $date->isSunday();
                        $isHoliday = in_array($date->format('Y-m-d'), $holidayDates);
                        return !$isWeekend && !$isHoliday;
                    }, $returnDate);
                    // ==========================================================
                    
                    $fine = $lateDays * 1000;
                }
                
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

    /**
     * Menandai Buku Hilang
     */
    public function markAsLost(Borrowing $borrowing)
    {
        // ... (Tidak ada perubahan di method markAsLost) ...
        if (!in_array($borrowing->status, ['dipinjam', 'overdue'])) {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dalam status aktif.');
        }
        $lostFineAmount = 0; 
        DB::transaction(function () use ($borrowing, $lostFineAmount) {
            $processDate = Carbon::now();
            $bookCopy = $borrowing->bookCopy;
            if ($bookCopy) {
                $bookCopy->status = 'hilang'; 
                $bookCopy->save();
            } else {
                 throw new \Exception("Data eksemplar buku tidak ditemukan untuk peminjaman ID: {$borrowing->id}");
            }
            $borrowing->status = 'returned'; 
            $borrowing->returned_at = $processDate; 
            $borrowing->late_days = 0; 
            $borrowing->fine_amount = $lostFineAmount; 
            $borrowing->fine_status = 'paid'; 
            $borrowing->returned_by = Auth::id(); 
            $borrowing->save();
        });
        
        $bookTitle = $borrowing->bookCopy && $borrowing->bookCopy->book ? $borrowing->bookCopy->book->title : '[Judul Tidak Ditemukan]';
        $bookCode = $borrowing->bookCopy ? $borrowing->bookCopy->book_code : '[Kode Tidak Ditemukan]';
        $message = "Buku '{$bookTitle}' (Eksemplar: {$bookCode}) berhasil ditandai sebagai hilang.";
        return redirect()->back()->with('success', $message);
    }
}