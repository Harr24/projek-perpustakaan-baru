<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

            // Update status record peminjaman
            $borrowing->status = 'approved'; // Seharusnya 'borrowed' agar konsisten
            $borrowing->approved_at = $approvalDate;
            $borrowing->approved_by = auth()->id();
            
            // Hitung dan simpan tanggal jatuh tempo
            $borrowing->due_date = $approvalDate->copy()->addWeekdays(7);
            
            // Update status salinan buku
            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'borrowed';

            // Simpan kedua perubahan
            $borrowing->save();
            $bookCopy->save();
        });

        return redirect()->route('admin.petugas.approvals.index')->with('success', 'Pengajuan peminjaman berhasil dikonfirmasi.');
    }

    // ==========================================================
    // METHOD REJECT YANG DISEMPURNAKAN
    // ==========================================================
    public function reject(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        DB::transaction(function () use ($borrowing) {
            // 1. Kembalikan status buku menjadi 'tersedia'
            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'tersedia';
            $bookCopy->save();

            // 2. Ubah status peminjaman menjadi 'rejected'
            $borrowing->status = 'rejected';
            
            // 3. (BARU) Tambahkan jejak audit untuk penolakan
            $borrowing->rejected_at = Carbon::now();
            $borrowing->rejected_by = auth()->id();
            
            $borrowing->save();
        });

        return redirect()->back()->with('success', 'Pengajuan pinjaman berhasil ditolak.');
    }
    // ==========================================================
    
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

                $borrowing->status = 'approved'; // Seharusnya 'borrowed'
                $borrowing->approved_at = $approvalDate;
                $borrowing->approved_by = auth()->id();
                
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
