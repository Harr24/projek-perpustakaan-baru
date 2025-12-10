<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Carbon\Carbon;

class BorrowingReportController extends Controller
{
    /**
     * Menampilkan halaman laporan dengan filter.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $month = $request->input('month');
        $year = $request->input('year', date('Y'));

        // ==========================================================
        // --- PERUBAHAN: Filter status 'returned' DAN 'missing' ---
        // ==========================================================
        $borrowingsQuery = Borrowing::with(['user', 'bookCopy.book', 'approvedBy', 'returnedBy'])
                                      ->whereIn('status', ['returned', 'missing']) // <-- Ambil yang dikembalikan & hilang
                                      ->latest('returned_at');

        // Terapkan filter berdasarkan pencarian nama peminjam
        $borrowingsQuery->when($search, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });

        // Terapkan filter bulan
        $borrowingsQuery->when($month, function ($query, $month) {
            return $query->whereMonth('borrowed_at', $month);
        });
        
        // Terapkan filter tahun
        $borrowingsQuery->whereYear('borrowed_at', $year);

        $borrowings = $borrowingsQuery->paginate(15)->withQueryString();
        
        return view('admin.petugas.reports.borrowings.index', compact('borrowings'));
    }

    /**
     * Menampilkan riwayat peminjaman satu user.
     */
    public function showUserHistory(User $user)
    {
        // Eager load relasi yang dibutuhkan untuk riwayat
        $history = Borrowing::with(['bookCopy.book', 'approvedBy', 'returnedBy'])
                            ->where('user_id', $user->id)
                            ->latest('borrowed_at')
                            ->get();
        return view('admin.petugas.reports.users.history', compact('user', 'history'));
    }

    /**
     * Mengekspor data laporan ke file Excel.
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $month = $request->input('month');
        $year = $request->input('year', date('Y'));

        // ==========================================================
        // --- PERUBAHAN: Filter status 'returned' DAN 'missing' (Sama dgn Index) ---
        // ==========================================================
        $borrowingsQuery = Borrowing::with(['user', 'bookCopy.book', 'approvedBy', 'returnedBy'])
                                      ->whereIn('status', ['returned', 'missing']) // <-- Konsisten dengan index
                                      ->latest('returned_at');

        $borrowingsQuery->when($search, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });
        $borrowingsQuery->when($month, function ($query, $month) {
            return $query->whereMonth('borrowed_at', $month);
        });
        $borrowingsQuery->whereYear('borrowed_at', $year);
        
        $dataToExport = $borrowingsQuery->get();

        $fileName = 'laporan-peminjaman-' . date('d-m-Y') . '.xlsx';
        $writer = SimpleExcelWriter::create(storage_path('app/' . $fileName));

        // ==========================================================
        // --- PERUBAHAN: Tambah Header 'Status Pengembalian' ---
        // ==========================================================
        $writer->addRow([
            'Nama Peminjam', 'Role', 'Kelas', 'Judul Buku', 'Kode Eksemplar', 
            'Tanggal Pinjam', 'Tanggal Kembali', 'Status Pengembalian', // <-- Kolom Baru
            'Petugas Approval', 'Petugas Pengembalian'
        ]);

        foreach ($dataToExport as $item) {
            // Logika Status Text untuk Excel
            $statusText = ($item->status === 'missing') ? 'HILANG' : 'Dikembalikan';

            $writer->addRow([
                'Nama Peminjam'         => $item->user->name,
                'Role'                  => ucfirst($item->user->role),
                'Kelas'                 => $item->user->class_name ?? 'N/A',
                'Judul Buku'            => $item->bookCopy->book->title,
                'Kode Eksemplar'        => $item->bookCopy->book_code,
                'Tanggal Pinjam'        => Carbon::parse($item->borrowed_at)->format('d-m-Y'),
                'Tanggal Kembali'       => $item->returned_at ? Carbon::parse($item->returned_at)->format('d-m-Y') : '-',
                'Status Pengembalian'   => $statusText, // <-- Isi Data Baru
                'Petugas Approval'      => $item->approvedBy->name ?? 'N/A',
                'Petugas Pengembalian'  => $item->returnedBy->name ?? 'N/A',
            ]);
        }
        
        return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);
    }
}