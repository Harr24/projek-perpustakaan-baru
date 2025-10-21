<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

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
        // PENAMBAHAN: Eager load relasi 'approvedBy' dan 'returnedBy'
        // ==========================================================
        $borrowingsQuery = Borrowing::with(['user', 'bookCopy.book', 'approvedBy', 'returnedBy'])
                                      ->whereNotNull('returned_at') // Ambil semua yang sudah dikembalikan
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
        $borrowings = $borrowingsQuery->paginate(15)->withQueryString();
        
        return view('admin.petugas.reports.borrowings.index', compact('borrowings'));
    }

    /**
     * Menampilkan riwayat peminjaman satu user.
     */
    public function showUserHistory(User $user)
    {
        // PENAMBAHAN: Eager load juga di sini
        $history = Borrowing::with(['bookCopy.book', 'approvedBy', 'returnedBy'])
                            ->where('user_id', $user->id)
                            ->latest('borrowed_at')
                            ->get();
        return view('admin.petugas.reports.users.history', compact('user', 'history'));
    }

    /**
     * Method export menggunakan cara Spatie SimpleExcel
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $month = $request->input('month');
        $year = $request->input('year', date('Y'));

        // ==========================================================
        // PENAMBAHAN: Eager load relasi 'approvedBy' dan 'returnedBy'
        // ==========================================================
        $borrowingsQuery = Borrowing::with(['user', 'bookCopy.book', 'approvedBy', 'returnedBy'])
                                      ->whereNotNull('returned_at')
                                      ->latest('returned_at');

        // Terapkan filter ke query
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

        // PENAMBAHAN: Tambah kolom Petugas di header Excel
        $writer->addRow([
            'Nama Peminjam', 'Role', 'Kelas', 'Judul Buku', 'Tanggal Pinjam', 'Tanggal Kembali', 'Petugas Approval', 'Petugas Pengembalian'
        ]);

        foreach ($dataToExport as $item) {
            // PENAMBAHAN: Tambah data nama petugas di setiap baris Excel
            $writer->addRow([
                'Nama Peminjam'         => $item->user->name,
                'Role'                  => ucfirst($item->user->role),
                'Kelas'                 => $item->user->class_name ?? 'N/A',
                'Judul Buku'            => $item->bookCopy->book->title,
                'Tanggal Pinjam'        => \Carbon\Carbon::parse($item->borrowed_at)->format('d-m-Y'),
                'Tanggal Kembali'       => $item->returned_at ? \Carbon\Carbon::parse($item->returned_at)->format('d-m-Y') : '-',
                'Petugas Approval'      => $item->approvedBy->name ?? 'N/A', // Gunakan relasi approvedBy
                'Petugas Pengembalian'  => $item->returnedBy->name ?? 'N/A', // Gunakan relasi returnedBy
            ]);
        }
        
        return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);
    }
}
