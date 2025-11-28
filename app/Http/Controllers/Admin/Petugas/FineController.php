<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\FinePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Carbon\Carbon;

class FineController extends Controller
{
    /**
     * Menampilkan denda yang BELUM LUNAS.
     */
    public function index()
    {
        $unpaidFines = Borrowing::where('fine_amount', '>', 0)
                                ->where('fine_status', 'unpaid')
                                ->with('user', 'bookCopy.book')
                                ->latest()
                                ->get();

        return view('admin.petugas.fines.index', compact('unpaidFines'));
    }

    /**
     * Menangani Pembayaran Cicilan Denda.
     */
    public function payInstallment(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $amountToPay = (int) $request->input('amount');
        
        if ($borrowing->fine_status == 'paid') {
            return redirect()->back()->with('error', 'Denda ini sudah lunas.');
        }

        $totalFine = $borrowing->fine_amount;
        $alreadyPaid = $borrowing->fine_paid ?? 0;
        $remainingFine = $totalFine - $alreadyPaid;

        if ($amountToPay > $remainingFine) {
            return redirect()->back()->with('error', 'Pembayaran melebihi sisa denda.');
        }

        try {
            DB::transaction(function () use ($borrowing, $amountToPay) {
                // 1. Update Borrowing
                $borrowing->fine_paid += $amountToPay; 
                
                if ($borrowing->fine_paid >= $borrowing->fine_amount) {
                    $borrowing->fine_paid = $borrowing->fine_amount;
                    $borrowing->fine_status = 'paid';
                }
                
                $borrowing->save();

                // 2. Catat di Log Pembayaran
                FinePayment::create([
                    'borrowing_id' => $borrowing->id,
                    'processed_by_user_id' => Auth::id(),
                    'amount_paid' => $amountToPay,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }

        $newRemaining = $remainingFine - $amountToPay;
        $msg = $borrowing->fine_status == 'paid' 
            ? 'Pembayaran lunas!' 
            : 'Cicilan Rp ' . number_format($amountToPay) . ' diterima. Sisa: Rp ' . number_format($newRemaining);

        return redirect()->back()->with('success', $msg);
    }


    /**
     * Riwayat Transaksi (Cicilan)
     */
    public function history(Request $request)
    {
        $years = FinePayment::select(DB::raw('YEAR(created_at) as year'))
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->get();

        // --- PERBAIKAN 1: Gunakan 'processedBy' bukan 'processor' ---
        $query = FinePayment::with(['borrowing.user', 'borrowing.bookCopy.book', 'processedBy']);

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('borrowing', function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($u) use ($searchTerm) {
                    $u->where('name', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orWhereHas('bookCopy.book', function ($b) use ($searchTerm) {
                    $b->where('title', 'LIKE', '%' . $searchTerm . '%');
                });
            });
        }

        $totalIncome = (clone $query)->sum('amount_paid');

        $payments = $query->latest('created_at')
                          ->paginate(15)
                          ->withQueryString();

        return view('admin.petugas.fines.history', compact('payments', 'years', 'totalIncome'));
    }

    /**
     * Export Excel
     */
     public function export(Request $request)
     {
        // --- PERBAIKAN 2: Gunakan 'processedBy' di sini juga ---
        $query = FinePayment::with(['borrowing.user', 'borrowing.bookCopy.book', 'processedBy']);

        if ($request->filled('year')) { $query->whereYear('created_at', $request->year); }
        if ($request->filled('month')) { $query->whereMonth('created_at', $request->month); }
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('borrowing', function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($u) use ($searchTerm) {
                    $u->where('name', 'LIKE', '%' . $searchTerm . '%');
                })->orWhereHas('bookCopy.book', function ($b) use ($searchTerm) {
                    $b->where('title', 'LIKE', '%' . $searchTerm . '%');
                });
            });
        }

        $payments = $query->latest('created_at')->get();

        $fileName = 'laporan-uang-denda-' . Carbon::now()->format('Ymd-His') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);

        $writer = SimpleExcelWriter::create($filePath)
            ->addRow([
                'Tanggal Bayar', 
                'Petugas Penerima',
                'Nama Siswa', 
                'Kelas', 
                'Judul Buku', 
                'Nominal Bayar (Rp)', 
                'Status Akhir'
            ]);

        foreach ($payments as $payment) {
            $writer->addRow([
                'Tanggal Bayar'    => $payment->created_at->format('d/m/Y H:i'),
                // --- PERBAIKAN 3: Panggil relasi yang benar ---
                'Petugas Penerima' => $payment->processedBy->name ?? 'System', 
                'Nama Siswa'       => $payment->borrowing->user->name ?? 'User Dihapus',
                'Kelas'            => $payment->borrowing->user->class_name ?? '-',
                'Judul Buku'       => $payment->borrowing->bookCopy->book->title ?? '-',
                'Nominal Bayar (Rp)' => $payment->amount_paid,
                'Status Akhir'     => $payment->borrowing->fine_status == 'paid' ? 'Lunas' : 'Belum Lunas',
            ]);
        }

        return response()->download($filePath)->deleteFileAfterSend(true);
     }
}