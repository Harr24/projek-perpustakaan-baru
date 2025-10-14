<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Carbon\Carbon;

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

    public function history(Request $request)
    {
        $years = Borrowing::where('fine_status', 'paid')
                            ->select(DB::raw('YEAR(updated_at) as year'))
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->get();

        $query = Borrowing::where('fine_status', 'paid')
                            ->with('user', 'bookCopy.book')
                            ->latest('updated_at');

        if ($request->filled('year')) {
            $query->whereYear('updated_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('updated_at', $request->month);
        }
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $paidFines = $query->get();
        $totalFine = $paidFines->sum('fine_amount');

        return view('admin.petugas.fines.history', compact('paidFines', 'years', 'totalFine'));
    }

    public function destroy(Borrowing $borrowing)
    {
        if ($borrowing->fine_status !== 'paid') {
            return redirect()->back()->with('error', 'Hanya riwayat denda yang sudah lunas yang bisa dihapus.');
        }
        $borrowing->delete();
        return redirect()->back()->with('success', 'Riwayat denda berhasil dihapus secara permanen.');
    }

    /**
     * ==========================================================
     * PERUBAHAN UTAMA ADA DI METHOD INI
     * ==========================================================
     */
     public function export(Request $request)
     {
        // 1. Query untuk mengambil data (tetap sama)
        $query = Borrowing::where('fine_status', 'paid')
                            ->with('user', 'bookCopy.book')
                            ->latest('updated_at');

        if ($request->filled('year')) { $query->whereYear('updated_at', $request->year); }
        if ($request->filled('month')) { $query->whereMonth('updated_at', $request->month); }
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $fines = $query->get();
        $fileName = 'riwayat-denda-' . Carbon::now()->format('Y-m-d') . '.xlsx';
        $filePath = storage_path('app/' . $fileName); // <-- Tentukan path penyimpanan sementara yang aman

        // 2. Buat file Excel dan tulis datanya
        $writer = SimpleExcelWriter::create($filePath)
            ->addRow([
                'Nama Peminjam', 'Kelas', 'Judul Buku', 'Kode Buku', 'Jumlah Denda', 'Tanggal Lunas'
            ]);

        foreach ($fines as $fine) {
            $writer->addRow([
                'Nama Peminjam' => $fine->user?->name ?? 'Pengguna Dihapus',
                'Kelas'         => $fine->user?->class_name ?? 'N/A',
                'Judul Buku'    => $fine->bookCopy?->book?->title ?? 'Buku Dihapus',
                'Kode Buku'     => $fine->bookCopy?->book_code ?? 'N/A',
                'Jumlah Denda'  => $fine->fine_amount,
                'Tanggal Lunas' => $fine->updated_at->format('d-m-Y H:i:s'),
            ]);
        }

        // 3. Gunakan response()->download() dari Laravel untuk mengirim file dan menghapusnya
        return response()->download($filePath)->deleteFileAfterSend(true);
     }
}