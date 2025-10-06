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
        // 1. Ubah status peminjaman menjadi 'borrowed'
        $borrowing->status = 'borrowed';
        $borrowing->save();

        // 2. Ubah status salinan buku menjadi 'dipinjam'
        $bookCopy = $borrowing->bookCopy;
        $bookCopy->status = 'dipinjam';
        $bookCopy->save();

        return redirect()->back()->with('success', 'Peminjaman berhasil dikonfirmasi.');
    }

    // ==========================================================
    // TAMBAHAN: Method untuk menolak pengajuan
    // ==========================================================
    public function reject(Borrowing $borrowing)
    {
        // Pastikan hanya pengajuan pending yang bisa ditolak
        if ($borrowing->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        // 1. Kembalikan status salinan buku menjadi 'tersedia'
        $bookCopy = $borrowing->bookCopy;
        $bookCopy->status = 'tersedia';
        $bookCopy->save();

        // 2. Ubah status peminjaman menjadi 'rejected'
        $borrowing->status = 'rejected';
        $borrowing->save();

        return redirect()->back()->with('success', 'Pengajuan pinjaman berhasil ditolak.');
    }
}