<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // <-- WAJIB: Import Carbon untuk manipulasi tanggal

class LoanApprovalController extends Controller
{
    public function index()
    {
        $pendingBorrowings = Borrowing::where('status', 'pending')
                                        ->with('user', 'bookCopy.book')
                                        ->latest()->get();
        return view('admin.petugas.approvals.index', compact('pendingBorrowings'));
    }

    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return redirect()->route('admin.petugas.approvals.index')->with('error', 'Status peminjaman ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($borrowing) {
            $approvalDate = Carbon::now();

            // 1. Update status record peminjaman
            $borrowing->status = 'approved';
            $borrowing->approved_at = $approvalDate;
            $borrowing->approved_by = auth()->id();
            
            // ==========================================================
            // PERBAIKAN UTAMA: Hitung dan simpan tanggal jatuh tempo
            // Tambahkan 7 hari kerja (Senin-Jumat) dari tanggal persetujuan.
            // ==========================================================
            $borrowing->due_date = $approvalDate->copy()->addWeekdays(7);

            // 2. Update status salinan buku
            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'borrowed';

            // 3. Simpan kedua perubahan
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

        DB::transaction(function () use ($borrowingsToApprove) {
            foreach ($borrowingsToApprove as $borrowing) {
                $approvalDate = Carbon::now();

                $borrowing->status = 'approved';
                $borrowing->approved_at = $approvalDate;
                $borrowing->approved_by = auth()->id();
                
                // PERBAIKAN UTAMA (untuk Aksi Massal)
                $borrowing->due_date = $approvalDate->copy()->addWeekdays(7);
                
                $borrowing->save();

                $bookCopy = $borrowing->bookCopy;
                $bookCopy->status = 'borrowed';
                $bookCopy->save();
            }
        });

        return redirect()->back()->with('success', $borrowingsToApprove->count() . ' peminjaman berhasil dikonfirmasi.');
    }
}