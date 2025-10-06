<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class FineController extends Controller
{
    /**
     * Menampilkan daftar semua denda yang belum lunas.
     */
    public function index()
    {
        // Ambil semua data peminjaman yang punya denda dan statusnya belum lunas
        $unpaidFines = Borrowing::where('fine_amount', '>', 0)
                                ->where('fine_status', 'unpaid')
                                ->with('user', 'bookCopy.book') // Ambil relasi untuk ditampilkan
                                ->latest()
                                ->get();

        return view('admin.petugas.fines.index', compact('unpaidFines'));
    }

    /**
     * Menandai denda sebagai lunas.
     */
    public function markAsPaid(Borrowing $borrowing)
    {
        // Pastikan ada denda untuk ditandai lunas
        if ($borrowing->fine_amount > 0 && $borrowing->fine_status == 'unpaid') {
            $borrowing->fine_status = 'paid';
            $borrowing->save();

            return redirect()->back()->with('success', 'Denda untuk ' . $borrowing->user->name . ' berhasil ditandai lunas.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }
}