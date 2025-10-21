<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request; // <-- Pastikan ini di-import
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanApprovalController extends Controller
{
    public function index(Request $request) // <-- Terima objek Request
    {
        $search = $request->input('search'); // <-- Ambil input search

        $query = Borrowing::where('status', 'pending')
                            ->with('user', 'bookCopy.book');

        // LOGIKA FILTER BERDASARKAN NAMA PEMINJAM
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        
        $pendingBorrowings = $query->latest()->get();

        // Pass variabel search ke view agar nilai search tetap terlihat di input
        return view('admin.petugas.approvals.index', compact('pendingBorrowings', 'search')); 
    }

    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return redirect()->route('admin.petugas.approvals.index')->with('error', 'Status peminjaman ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($borrowing) {
            $approvalDate = Carbon::now();

            $borrowing->status = 'dipinjam';
            $borrowing->approved_at = $approvalDate;
            $borrowing->approved_by = auth()->id();
            $borrowing->due_date = $approvalDate->copy()->addWeekdays(7);
            
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

        DB::transaction(function () use ($borrowingsToApprove) {
            foreach ($borrowingsToApprove as $borrowing) {
                $approvalDate = Carbon::now();

                $borrowing->status = 'dipinjam';
                $borrowing->approved_at = $approvalDate;
                $borrowing->approved_by = auth()->id();
                $borrowing->due_date = $approvalDate->copy()->addWeekdays(7);
                $borrowing->save();

                $bookCopy = $borrowing->bookCopy;
                $bookCopy->status = 'dipinjam';
                $bookCopy->save();
            }
        });

        return redirect()->back()->with('success', $borrowingsToApprove->count() . ' peminjaman berhasil dikonfirmasi.');
    }
}
