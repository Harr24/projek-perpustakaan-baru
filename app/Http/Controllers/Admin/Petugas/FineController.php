<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelWriter; // Pastikan use statement ini ada
use Carbon\Carbon;

// ==========================================================
// --- TAMBAHAN BARU: Import Model FinePayment dan Auth ---
// ==========================================================
use App\Models\FinePayment;
use Illuminate\Support\Facades\Auth;
// ==========================================================


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
                                ->latest()->get();

        return view('admin.petugas.fines.index', compact('unpaidFines'));
    }

    /**
     * ==========================================================
     * --- METHOD BARU: Menangani Pembayaran Cicilan Denda ---
     * ==========================================================
     */
    public function payInstallment(Request $request, Borrowing $borrowing)
    {
        // 1. Validasi input dari form (harus ada angka)
        $request->validate([
            'amount' => 'required|numeric|min:1' // Minimal bayar 1
        ]);

        $amountToPay = (int) $request->input('amount');
        
        // 2. Cek status denda
        if ($borrowing->fine_status == 'paid') {
            return redirect()->back()->with('error', 'Denda ini sudah lunas.');
        }

        // 3. Hitung sisa denda
        $totalFine = $borrowing->fine_amount;
        $alreadyPaid = $borrowing->fine_paid ?? 0;
        $remainingFine = $totalFine - $alreadyPaid;

        // 4. Validasi jika pembayaran melebihi sisa denda
        if ($amountToPay > $remainingFine) {
            $message = 'Jumlah pembayaran (Rp ' . number_format($amountToPay) . ')';
            $message .= ' melebihi sisa denda (Rp ' . number_format($remainingFine) . ').';
            return redirect()->back()->with('error', $message);
        }

        // 5. Update database
        try {
            DB::transaction(function () use ($borrowing, $amountToPay) {
                
                // A. Update tabel 'borrowings'
                $borrowing->fine_paid += $amountToPay; 
                
                // Cek apakah lunas
                if ($borrowing->fine_paid >= $borrowing->fine_amount) {
                    $borrowing->fine_paid = $borrowing->fine_amount; // Pastikan tidak lebih
                    $borrowing->fine_status = 'paid'; // Ubah status jadi lunas
                }
                
                $borrowing->save();

                // ==========================================================
                // --- B. TAMBAHAN BARU: Catat di Log Pembayaran ---
                // ==========================================================
                FinePayment::create([
                    'borrowing_id' => $borrowing->id,
                    'processed_by_user_id' => Auth::id(), // ID Petugas yang sedang login
                    'amount_paid' => $amountToPay,
                    'created_at' => now(), // Catat waktu transaksi
                    'updated_at' => now(),
                ]);
                // ==========================================================

            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }

        // 6. Beri pesan sukses
        if ($borrowing->fine_status == 'paid') {
            return redirect()->back()->with('success', 'Pembayaran diterima dan denda telah lunas.');
        } else {
            // Jika belum lunas, tampilkan sisa denda
            $newRemaining = $remainingFine - $amountToPay;
            $message = 'Cicilan diterima. Sisa denda: Rp ' . number_format($newRemaining);
            return redirect()->back()->with('success', $message);
        }
    }


    /**
     * Menampilkan riwayat denda yang SUDAH LUNAS.
     */
    public function history(Request $request)
    {
        // Ambil tahun unik untuk filter dropdown
        $years = Borrowing::where('fine_status', 'paid')
                            ->where('fine_amount', '>', 0)
                            ->select(DB::raw('YEAR(updated_at) as year'))
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->get();

        // Query dasar untuk riwayat denda lunas
        $query = Borrowing::where('fine_status', 'paid')
                            ->where('fine_amount', '>', 0)
                            ->with(['user', 'bookCopy.book']) // Eager load relasi
                            ->latest('updated_at'); // Urutkan berdasarkan tanggal lunas terbaru

        // Terapkan filter
        if ($request->filled('year')) {
            $query->whereYear('updated_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('updated_at', $request->month);
        }
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            // Pencarian berdasarkan nama user ATAU judul buku
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($subQ) use ($searchTerm) {
                    $subQ->where('name', 'LIKE', '%' . $searchTerm . '%');
                })->orWhereHas('bookCopy.book', function ($subQ) use ($searchTerm) {
                        $subQ->where('title', 'LIKE', '%' . $searchTerm . '%');
                    });
            });
        }

        // Clone query sebelum pagination untuk menghitung total
        $totalFineQuery = clone $query;
        $totalFine = $totalFineQuery->sum('fine_amount');

        // Lakukan pagination setelah filter
        $paidFines = $query->paginate(15)->withQueryString(); // Gunakan paginate

        return view('admin.petugas.fines.history', compact('paidFines', 'years', 'totalFine'));
    }

    /**
     * Export riwayat denda lunas ke Excel.
     */
     public function export(Request $request)
     {
        // Query untuk mengambil data (sama seperti history, tapi tanpa pagination)
        $query = Borrowing::where('fine_status', 'paid')
                            ->where('fine_amount', '>', 0)
                            ->with(['user', 'bookCopy.book']) // Eager load
                            ->latest('updated_at');

        // Terapkan filter yang sama
        if ($request->filled('year')) { $query->whereYear('updated_at', $request->year); }
        if ($request->filled('month')) { $query->whereMonth('updated_at', $request->month); }
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($subQ) use ($searchTerm) {
                    $subQ->where('name', 'LIKE', '%' . $searchTerm . '%');
                })->orWhereHas('bookCopy.book', function ($subQ) use ($searchTerm) {
                        $subQ->where('title', 'LIKE', '%' . $searchTerm . '%');
                    });
            });
        }

        $fines = $query->get();

        // Buat nama file dinamis
        $fileName = 'riwayat-denda-lunas-' . Carbon::now()->format('Ymd-His') . '.xlsx';
        // Simpan sementara di storage/app
        $filePath = storage_path('app/' . $fileName);

        // Buat file Excel dan tulis datanya
        $writer = SimpleExcelWriter::create($filePath)
            ->addRow([ // Header kolom
                'Nama Peminjam', 'Kelas', 'Judul Buku', 'Kode Buku', 'Jumlah Denda', 'Tanggal Lunas'
            ]);

        foreach ($fines as $fine) {
            $writer->addRow([
                // Menggunakan null coalescing operator (??) untuk handle jika relasi null (misal user dihapus)
                'Nama Peminjam' => $fine->user?->name ?? 'Pengguna Dihapus',
                'Kelas'         => $fine->user?->class_name ?? 'N/A',
                'Judul Buku'    => $fine->bookCopy?->book?->title ?? 'Buku Dihapus',
                'Kode Buku'     => $fine->bookCopy?->book_code ?? 'N/A',
                'Jumlah Denda'  => $fine->fine_amount,
                'Tanggal Lunas' => $fine->updated_at ? $fine->updated_at->format('d-m-Y H:i:s') : 'N/A',
            ]);
        }

        // Gunakan response()->download() untuk mengirim file ke browser dan menghapusnya setelah terkirim
        return response()->download($filePath)->deleteFileAfterSend(true);
     }
}