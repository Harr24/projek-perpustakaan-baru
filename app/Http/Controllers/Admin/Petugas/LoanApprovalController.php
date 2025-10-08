<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;

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
        $borrowing->status = 'borrowed';
        $borrowing->save();

        $bookCopy = $borrowing->bookCopy;
        $bookCopy->status = 'dipinjam';
        $bookCopy->save();

        return redirect()->back()->with('success', 'Peminjaman berhasil dikonfirmasi.');
    }

    public function reject(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $bookCopy = $borrowing->bookCopy;
        $bookCopy->status = 'tersedia';
        $bookCopy->save();

        $borrowing->status = 'rejected';
        $borrowing->save();

        return redirect()->back()->with('success', 'Pengajuan pinjaman berhasil ditolak.');
    }
    
    // ==========================================================
    // TAMBAHAN: Method baru untuk konfirmasi massal
    // ==========================================================
    public function approveMultiple(Request $request)
    {
        // 1. Validasi: pastikan 'borrowing_ids' dikirim dan berupa array
        $request->validate([
            'borrowing_ids' => 'required|array',
            'borrowing_ids.*' => 'exists:borrowings,id', // Pastikan semua ID valid
        ]);

        $borrowingIds = $request->input('borrowing_ids');

        // 2. Ambil semua data peminjaman yang akan dikonfirmasi
        $borrowingsToApprove = Borrowing::whereIn('id', $borrowingIds)
                                        ->where('status', 'pending')
                                        ->get();

        if ($borrowingsToApprove->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada pengajuan valid yang dipilih untuk dikonfirmasi.');
        }

        // 3. Loop dan konfirmasi satu per satu
        foreach ($borrowingsToApprove as $borrowing) {
            // Ubah status peminjaman
            $borrowing->status = 'borrowed';
            $borrowing->save();

            // Ubah status eksemplar buku
            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'dipinjam';
            $bookCopy->save();
        }

        return redirect()->back()->with('success', $borrowingsToApprove->count() . ' peminjaman berhasil dikonfirmasi.');
    }
}