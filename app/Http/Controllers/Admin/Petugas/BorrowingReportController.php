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

        $borrowingsQuery = Borrowing::with(['user', 'bookCopy.book'])
                                    // ==========================================================
                                    // PERUBAHAN 1: Sesuaikan status dengan database
                                    // ==========================================================
                                    ->where('status', 'returned') // Diubah dari 'dikembalikan'
                                    ->latest('borrowed_at');

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
        $history = Borrowing::with('bookCopy.book')->where('user_id', $user->id)->latest('borrowed_at')->get();
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

        $borrowingsQuery = Borrowing::with(['user', 'bookCopy.book'])
                                    // ==========================================================
                                    // PERUBAHAN 2: Sesuaikan status dengan database
                                    // ==========================================================
                                    ->where('status', 'returned') // Diubah dari 'dikembalikan'
                                    ->latest('borrowed_at');

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

        $writer->addRow([
            'Nama Peminjam', 'Role', 'Kelas', 'Judul Buku', 'Tanggal Pinjam', 'Tanggal Kembali'
        ]);

        foreach ($dataToExport as $item) {
            $writer->addRow([
                'Nama Peminjam'   => $item->user->name,
                'Role'            => ucfirst($item->user->role),
                'Kelas'           => $item->user->class_name ?? 'N/A',
                'Judul Buku'      => $item->bookCopy->book->title,
                'Tanggal Pinjam'  => \Carbon\Carbon::parse($item->borrowed_at)->format('d-m-Y'),
                'Tanggal Kembali' => $item->returned_at ? \Carbon\Carbon::parse($item->returned_at)->format('d-m-Y') : '-',
            ]);
        }
        
        return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);
    }
}