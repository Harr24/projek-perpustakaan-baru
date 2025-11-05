<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB; // Pastikan DB di-import
use Carbon\Carbon;

class LoanApprovalController extends Controller
{
    /**
     * Helper function untuk menghitung tanggal jatuh tempo
     * dengan melewatkan akhir pekan DAN tanggal merah dari database.
     */
    private function addWorkingDays(Carbon $startDate, int $daysToAdd, array $holidays): Carbon
    {
        $currentDate = $startDate->copy();
        $daysAdded = 0;
        
        while ($daysAdded < $daysToAdd) {
            $currentDate->addDay(); // Maju satu hari
            
            // Cek apakah hari ini valid (bukan weekend, bukan libur)
            $isWeekend = $currentDate->isSaturday() || $currentDate->isSunday();
            $isHoliday = in_array($currentDate->format('Y-m-d'), $holidays);
            
            // Jika BUKAN akhir pekan DAN BUKAN tanggal merah, baru kita hitung
            if (!$isWeekend && !$isHoliday) {
                $daysAdded++;
            }
        }
        return $currentDate;
    }

    public function index(Request $request) 
    {
        $search = $request->input('search'); 

        $query = Borrowing::where('status', 'pending')
                            ->with('user', 'bookCopy.book');

        // LOGIKA FILTER BERDASARKAN NAMA PEMINJAM
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        
        $pendingBorrowings = $query->latest()->get();
        return view('admin.petugas.approvals.index', compact('pendingBorrowings', 'search')); 
    }

    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return redirect()->route('admin.petugas.approvals.index')->with('error', 'Status peminjaman ini sudah diproses sebelumnya.');
        }

        // ==========================================================
        // --- TAMBAHAN: Ambil Daftar Tanggal Merah ---
        // ==========================================================
        $holidayDates = DB::table('holidays')
                            ->pluck('holiday_date')
                            ->map(fn($dateStr) => (new Carbon($dateStr))->format('Y-m-d'))
                            ->toArray();
        // ==========================================================


        DB::transaction(function () use ($borrowing, $holidayDates) { // <-- Kirim $holidayDates
            $approvalDate = Carbon::now();

            $book = $borrowing->bookCopy->book;
            
            // ==========================================================
            // --- PERBAIKAN: Logika Batas Waktu Baru ---
            // ==========================================================
            $dueDate = null; // Default: NULL (untuk 'laporan')

            // Atur batas waktu HANYA jika 'reguler' atau 'paket'
            if (in_array($book->book_type, ['reguler', 'paket'])) {
                // Gunakan helper function yang sudah ada (pintar)
                $dueDate = $this->addWorkingDays($approvalDate->copy(), 7, $holidayDates);
            }
            // Jika book_type adalah 'laporan', $dueDate akan tetap NULL. Ini sudah benar.
            // ==========================================================

            $borrowing->status = 'dipinjam';
            $borrowing->approved_at = $approvalDate;
            $borrowing->approved_by = auth()->id();
            $borrowing->due_date = $dueDate; // Terapkan $dueDate baru
            
            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'dipinjam';

            $borrowing->save();
            $bookCopy->save();
        });

        return redirect()->route('admin.petugas.approvals.index')->with('success', 'Pengajuan peminjaman berhasil dikonfirmasi.');
    }

    public function reject(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        DB::transaction(function () use ($borrowing) {
            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'tersedia';
            $bookCopy->save();

            $borrowing->status = 'rejected';
            $borrowing->rejected_at = Carbon::now();
            $borrowing->rejected_by = auth()->id();
            $borrowing->save();
        });

        return redirect()->back()->with('success', 'Pengajuan pinjaman berhasil ditolak.');
    }
    
    public function approveMultiple(Request $request)
    {
        $request->validate([
            'borrowing_ids' => 'required|array',
            'borrowing_ids.*' => 'exists:borrowings,id',
        ]);

        $borrowingIds = $request->input('borrowing_ids');
        $borrowingsToApprove = Borrowing::whereIn('id', $borrowingIds)->where('status', 'pending')->get();

        if ($borrowingsToApprove->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada pengajuan valid yang dipilih untuk dikonfirmasi.');
        }

        // ==========================================================
        // --- TAMBAHAN: Ambil Daftar Tanggal Merah (untuk massal) ---
        // ==========================================================
        $holidayDates = DB::table('holidays')
                            ->pluck('holiday_date')
                            ->map(fn($dateStr) => (new Carbon($dateStr))->format('Y-m-d'))
                            ->toArray();
        // ==========================================================

        DB::transaction(function () use ($borrowingsToApprove, $holidayDates) { // <-- Kirim $holidayDates
            foreach ($borrowingsToApprove as $borrowing) {
                $approvalDate = Carbon::now();

                $book = $borrowing->bookCopy->book;

                // ==========================================================
                // --- PERBAIKAN: Logika Batas Waktu Baru ---
                // ==========================================================
                $dueDate = null; // Default: NULL (untuk 'laporan')

                // Atur batas waktu HANYA jika 'reguler' atau 'paket'
                if (in_array($book->book_type, ['reguler', 'paket'])) {
                    // Gunakan helper function yang sudah ada (pintar)
                    $dueDate = $this->addWorkingDays($approvalDate->copy(), 7, $holidayDates);
                }
                // ==========================================================

                $borrowing->status = 'dipinjam';
                $borrowing->approved_at = $approvalDate;
                $borrowing->approved_by = auth()->id();
                $borrowing->due_date = $dueDate; 
                $borrowing->save();

                $bookCopy = $borrowing->bookCopy;
                $bookCopy->status = 'dipinjam';
                $bookCopy->save();
            }
        });

        return redirect()->back()->with('success', $borrowingsToApprove->count() . ' peminjaman berhasil dikonfirmasi.');
    }

    // ==========================================================
    // ===== Fungsi untuk Tolak Massal (Tidak Berubah) =====
    // ==========================================================
    public function rejectMultiple(Request $request)
    {
        $request->validate([
            'borrowing_ids' => 'required|array',
            'borrowing_ids.*' => 'exists:borrowings,id',
        ]);

        $borrowingIds = $request->input('borrowing_ids');
        $borrowingsToReject = Borrowing::whereIn('id', $borrowingIds)->where('status', 'pending')->get();

        if ($borrowingsToReject->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada pengajuan valid yang dipilih untuk ditolak.');
        }

        DB::transaction(function () use ($borrowingsToReject) {
            foreach ($borrowingsToReject as $borrowing) {
                // Logika dari fungsi reject()
                $bookCopy = $borrowing->bookCopy;
                $bookCopy->status = 'tersedia';
                $bookCopy->save();

                $borrowing->status = 'rejected';
                $borrowing->rejected_at = Carbon::now();
                $borrowing->rejected_by = auth()->id();
                $borrowing->save();
            }
        });

        return redirect()->back()->with('success', $borrowingsToReject->count() . ' peminjaman berhasil ditolak.');
    }
    // ==========================================================

}