<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Carbon\Carbon;

class SuperadminFineController extends Controller
{
    /**
     * Menampilkan riwayat denda yang sudah lunas untuk Superadmin.
     */
    public function history(Request $request)
    {
        $search = $request->input('search');
        $year = $request->input('year');
        $month = $request->input('month');

        $query = Borrowing::where('fine_status', 'paid')
                            ->where('fine_amount', '>', 0)
                            ->with([
                                'user', 
                                'bookCopy.book', 
                                'finePayments.processedBy'
                            ]);

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($subQ) use ($search) { 
                 $subQ->whereHas('user', function ($userQ) use ($search) {
                     $userQ->where('name', 'LIKE', "%{$search}%");
                 })->orWhereHas('bookCopy.book', function ($bookQ) use ($search) {
                     $bookQ->where('title', 'LIKE', "%{$search}%");
                 });
            });
        });

        $query->when($year, fn($q) => $q->whereYear('updated_at', $year));
        $query->when($month, fn($q) => $q->whereMonth('updated_at', $month));

        $totalFineQuery = clone $query;
        $totalFine = $totalFineQuery->sum('fine_amount');

        $paidFines = $query->orderBy('updated_at', 'desc')->paginate(15)->withQueryString();

        $years = Borrowing::select(DB::raw('YEAR(updated_at) as year'))
                            ->where('fine_status', 'paid')
                            ->where('fine_amount', '>', 0)
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->get();

        return view('admin.Superadmin.fines.history', compact('paidFines', 'totalFine', 'years'));
    }

    /**
     * Menghapus data riwayat denda secara permanen / Reset.
     */
    public function destroy(Borrowing $fine)
    {
        if ($fine->fine_status !== 'paid' || $fine->fine_amount <= 0) {
            return redirect()->route('admin.superadmin.fines.history')
                             ->with('error', 'Hanya riwayat denda yang sudah lunas yang dapat dihapus.');
        }

        try {
             // Reset field denda agar hilang dari history
             $fine->fine_amount = 0;
             $fine->late_days = 0;
             $fine->fine_status = 'unpaid'; 
             $fine->save();

            return redirect()->route('admin.superadmin.fines.history')
                             ->with('success', 'Riwayat denda berhasil dihapus.');

        } catch (\Exception $e) {
             Log::error('Error resetting fine history ID: '.$fine->id.' : ' . $e->getMessage());
            return redirect()->route('admin.superadmin.fines.history')
                             ->with('error', 'Gagal menghapus riwayat denda.');
        }
    }

    /**
     * Export riwayat denda lunas ke Excel.
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $year = $request->input('year');
        $month = $request->input('month');

        // 1. Query Data
        $query = Borrowing::where('fine_status', 'paid')
                            ->where('fine_amount', '>', 0)
                            ->with(['user', 'bookCopy.book', 'finePayments.processedBy'])
                            ->orderBy('updated_at', 'desc');

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($subQ) use ($search) { 
                 $subQ->whereHas('user', fn($uq) => $uq->where('name', 'LIKE', "%{$search}%"))
                      ->orWhereHas('bookCopy.book', fn($bq) => $bq->where('title', 'LIKE', "%{$search}%"));
            });
        });
        $query->when($year, fn($q) => $q->whereYear('updated_at', $year));
        $query->when($month, fn($q) => $q->whereMonth('updated_at', $month));

        $fines = $query->get();

        if ($fines->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        // 2. Buat File Excel
        $fileName = 'riwayat-denda-lunas-' . Carbon::now()->format('Ymd-His') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);

        $writer = SimpleExcelWriter::create($filePath);
        
        // 3. Loop Data dengan PERBAIKAN LOGIKA KELAS
        foreach ($fines as $fine) {
            $lastPayment = $fine->finePayments->last(); 

            // LOGIKA BARU: Gabungkan Class dan Major
            // Hasilnya misal: "XI DKV 1" atau "Lulus"
            $kelasString = '-';
            if ($fine->user) {
                $kelasPart = $fine->user->class ?? ''; // Ambil kolom 'class'
                $jurusanPart = $fine->user->major ?? ''; // Ambil kolom 'major'
                // Gabungkan dengan spasi, lalu trim jika salah satu kosong
                $kelasString = trim("$kelasPart $jurusanPart");
                
                if (empty($kelasString)) {
                    $kelasString = '-';
                }
            }

            $writer->addRow([
                'Nama Peminjam'   => $fine->user?->name ?? 'N/A',
                'Kelas'           => $kelasString, // <--- INI YANG DIPERBAIKI
                'Judul Buku'      => $fine->bookCopy?->book?->title ?? 'N/A',
                'Kode Eksemplar'  => $fine->bookCopy?->book_code ?? 'N/A',
                'Jumlah Denda'    => $fine->fine_amount,
                'Tanggal Lunas'   => $lastPayment ? $lastPayment->created_at->format('d-m-Y H:i') : '-',
                'Diproses Oleh'   => $lastPayment?->processedBy?->name ?? 'Sistem',
            ]);
        }
        
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}