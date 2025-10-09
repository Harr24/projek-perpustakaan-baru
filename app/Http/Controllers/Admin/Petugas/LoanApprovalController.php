<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanApprovalController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan peminjaman yang masih 'pending'.
     */
    public function index()
    {
        $pendingBorrowings = Borrowing::where('status', 'pending')
                                        ->with('user', 'bookCopy.book')
                                        ->latest()->get();
        return view('admin.petugas.approvals.index', compact('pendingBorrowings'));
    }

    /**
     * Menyetujui satu pengajuan peminjaman.
     */
    public function approve(Borrowing $borrowing)
    {
        // Validasi untuk mencegah aksi ganda
        if ($borrowing->status !== 'pending') {
            return redirect()->route('admin.petugas.approvals.index')->with('error', 'Status peminjaman ini sudah diproses sebelumnya.');
        }

        // Gunakan Transaction untuk memastikan semua perubahan data berhasil
        DB::transaction(function () use ($borrowing) {
            // 1. Update status record peminjaman
            $borrowing->status = 'approved';
            $borrowing->approved_at = now(); // Catat waktu persetujuan
            $borrowing->approved_by = auth()->id(); // Catat siapa yang menyetujui
            
            // 2. Update status salinan buku
            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'borrowed';

            // 3. Simpan kedua perubahan
            $borrowing->save();
            $bookCopy->save();
        });

        return redirect()->route('admin.petugas.approvals.index')->with('success', 'Pengajuan peminjaman berhasil dikonfirmasi.');
    }

    /**
     * Menolak satu pengajuan peminjaman.
     */
    public function reject(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        DB::transaction(function () use ($borrowing) {
            // Kembalikan status salinan buku menjadi 'tersedia'
            $bookCopy = $borrowing->bookCopy;
            $bookCopy->status = 'tersedia';
            $bookCopy->save();

            // Ubah status peminjaman menjadi 'rejected'
            $borrowing->status = 'rejected';
            $borrowing->save();
        });

        return redirect()->back()->with('success', 'Pengajuan pinjaman berhasil ditolak.');
    }
    
    /**
     * Menyetujui beberapa pengajuan peminjaman sekaligus (aksi massal).
     */
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
                $borrowing->status = 'approved';
                $borrowing->approved_at = now();
                $borrowing->approved_by = auth()->id();
                $borrowing->save();

                $bookCopy = $borrowing->bookCopy;
                $bookCopy->status = 'borrowed';
                $bookCopy->save();
            }
        });

        return redirect()->back()->with('success', $borrowingsToApprove->count() . ' peminjaman berhasil dikonfirmasi.');
    }
}

