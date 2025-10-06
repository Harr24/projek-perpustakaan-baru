<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- TAMBAHKAN INI

class FineController extends Controller
{
    public function index()
    {
        $unpaidFines = Borrowing::where('fine_amount', '>', 0)
                                ->where('fine_status', 'unpaid')
                                ->with('user', 'bookCopy.book')
                                ->latest()->get();

        return view('admin.petugas.fines.index', compact('unpaidFines'));
    }

    public function markAsPaid(Borrowing $borrowing)
    {
        if ($borrowing->fine_amount > 0 && $borrowing->fine_status == 'unpaid') {
            $borrowing->fine_status = 'paid';
            $borrowing->save();

            return redirect()->back()->with('success', 'Denda untuk ' . $borrowing->user->name . ' berhasil ditandai lunas.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }

    /**
     * ==========================================================
     * PERUBAHAN UTAMA DI SINI: Method history() yang baru
     * ==========================================================
     */
    public function history(Request $request)
    {
        // 1. Ambil data bulan & tahun yang ada transaksinya untuk dropdown filter
        $months = Borrowing::where('fine_status', 'paid')
                            ->select(DB::raw('YEAR(updated_at) as year, MONTH(updated_at) as month, MONTHNAME(updated_at) as month_name'))
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->orderBy('month', 'desc')
                            ->get();

        // 2. Query dasar untuk mengambil denda yang sudah lunas
        $query = Borrowing::where('fine_status', 'paid')
                          ->with('user', 'bookCopy.book')
                          ->latest('updated_at');

        // 3. Terapkan filter jika ada
        if ($request->filled('month_year')) {
            [$year, $month] = explode('-', $request->month_year);
            $query->whereYear('updated_at', $year)->whereMonth('updated_at', $month);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $paidFines = $query->get();

        return view('admin.petugas.fines.history', compact('paidFines', 'months'));
    }
}