<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing; // Model untuk data peminjaman (yang berisi info denda)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk mengambil tahun unik
use Illuminate\Support\Facades\Log; // Untuk logging error

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

        // Query dasar untuk denda yang sudah lunas (paid) dan memiliki jumlah > 0
        // ==========================================================
        // --- REVISI: Eager load data petugas yang memproses ---
        // ==========================================================
        $query = Borrowing::where('fine_status', 'paid')
                            ->where('fine_amount', '>', 0)
                            ->with([
                                'user', // Siswa yang meminjam
                                'bookCopy.book', 
                                'finePayments.processedBy' // <-- TAMBAHAN BARU
                            ]);
        // ==========================================================


        // Filter pencarian nama pengguna atau judul buku
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($subQ) use ($search) { // Grouping WHERE clauses
                 $subQ->whereHas('user', function ($userQ) use ($search) {
                     $userQ->where('name', 'LIKE', "%{$search}%");
                 })->orWhereHas('bookCopy.book', function ($bookQ) use ($search) {
                     $bookQ->where('title', 'LIKE', "%{$search}%");
                 });
            });
        });


        // Filter berdasarkan tahun lunas (menggunakan updated_at)
        $query->when($year, function ($q) use ($year) {
            $q->whereYear('updated_at', $year);
        });

        // Filter berdasarkan bulan lunas (menggunakan updated_at)
        $query->when($month, function ($q) use ($month) {
            $q->whereMonth('updated_at', $month);
        });

        // Clone query sebelum pagination untuk menghitung total
        $totalFineQuery = clone $query;
        $totalFine = $totalFineQuery->sum('fine_amount');

        // Urutkan dan lakukan pagination
        $paidFines = $query->orderBy('updated_at', 'desc')->paginate(15)->withQueryString();

        // Ambil daftar tahun unik untuk dropdown filter
        $years = Borrowing::select(DB::raw('YEAR(updated_at) as year'))
                            ->where('fine_status', 'paid')
                            ->where('fine_amount', '>', 0)
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->get();

        // Kirim data ke view Superadmin
        // ==========================================================
        // --- PERBAIKAN: Nama view Anda kemungkinan 'admin.Superadmin.fines.history' ---
        // ==========================================================
        return view('admin.superadmin.fines.history', compact('paidFines', 'totalFine', 'years'));
    }

    /**
     * Menghapus data riwayat denda secara permanen (hanya Superadmin).
     * Menggunakan Route Model Binding ($fine otomatis diambil dari ID di URL).
     */
    public function destroy(Borrowing $fine)
    {
        // Validasi: Pastikan hanya denda lunas yang bisa dihapus riwayatnya
        if ($fine->fine_status !== 'paid' || $fine->fine_amount <= 0) {
            return redirect()->route('admin.superadmin.fines.history')
                             ->with('error', 'Hanya riwayat denda yang sudah lunas yang dapat dihapus.');
        }

        try {
            // Logika Hapus:
            // Opsi 1: Hapus record borrowing sepenuhnya (seperti kode Petugas sebelumnya)
            // $fine->delete();

            // Opsi 2 (Lebih Aman): Reset field denda saja, biarkan record borrowing sebagai history peminjaman
             $fine->fine_amount = 0;
             $fine->late_days = 0;
             $fine->fine_status = 'unpaid'; // Atau status baru seperti 'history_deleted'
             // Tambahkan field untuk mencatat siapa yg menghapus (opsional)
             // $fine->fine_history_deleted_by = auth()->id();
             // $fine->fine_history_deleted_at = now();
             $fine->save();


            return redirect()->route('admin.superadmin.fines.history')
                             ->with('success', 'Riwayat denda berhasil dihapus.'); // Ubah pesan jika hanya mereset

        } catch (\Exception $e) {
             Log::error('Error deleting/resetting fine history by superadmin (ID: '.$fine->id.'): ' . $e->getMessage());
            return redirect()->route('admin.superadmin.fines.history')
                             ->with('error', 'Gagal menghapus riwayat denda. Terjadi kesalahan sistem.');
        }
    }

     // Method Export bisa ditambahkan di sini jika Superadmin juga butuh export
     // public function export(Request $request) { ... salin dari FineController Petugas ... }
}