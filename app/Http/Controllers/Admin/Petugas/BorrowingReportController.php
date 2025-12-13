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
        
        // Ambil input status dari filter
        $status = $request->input('status'); 

        // Query dasar
        $borrowingsQuery = Borrowing::with(['user', 'bookCopy.book', 'approvedBy', 'returnedBy'])
                                      ->latest('returned_at');

        // LOGIKA FILTER STATUS
        if ($status) {
            // Jika ada filter (misal: 'missing'), cari yang statusnya itu saja
            $borrowingsQuery->where('status', $status);
        } else {
            // Jika filter kosong, tampilkan keduanya (returned & missing)
            $borrowingsQuery->whereIn('status', ['returned', 'missing']);
        }

        // Filter pencarian nama
        $borrowingsQuery->when($search, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });

        // Filter bulan
        $borrowingsQuery->when($month, function ($query, $month) {
            return $query->whereMonth('borrowed_at', $month);
        });
        
        // Filter tahun
        $borrowingsQuery->whereYear('borrowed_at', $year);

        $borrowings = $borrowingsQuery->paginate(15)->withQueryString();
        
        return view('admin.petugas.reports.borrowings.index', compact('borrowings'));
    }

    /**
     * Menampilkan riwayat peminjaman satu user.
     */
    public function showUserHistory(User $user)
    {
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
        $status = $request->input('status');

        // Query dasar (harus sama dengan index)
        $borrowingsQuery = Borrowing::with(['user', 'bookCopy.book', 'approvedBy', 'returnedBy'])
                                      ->latest('returned_at');

        // Filter Status
        if ($status) {
            $borrowingsQuery->where('status', $status);
        } else {
            $borrowingsQuery->whereIn('status', ['returned', 'missing']);
        }

        // Filter Nama, Bulan, Tahun
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

        $fileName = 'laporan-peminjaman-' . date('d-m-Y-H-i') . '.xlsx';
        $writer = SimpleExcelWriter::create(storage_path('app/' . $fileName));

        // Header Excel
        $writer->addRow([
            'Nama Peminjam', 'Role', 'Kelas / Mapel', 'Judul Buku', 'Kode Eksemplar', 
            'Tanggal Pinjam', 'Tanggal Kembali', 'Status Pengembalian',
            'Petugas Approval', 'Petugas Pengembalian'
        ]);

        foreach ($dataToExport as $item) {
            $statusText = ($item->status === 'missing') ? 'HILANG' : 'Dikembalikan';

            // ==========================================================
            // 🔥 LOGIKA PENENTUAN KELAS / MAPEL 🔥
            // ==========================================================
            $displayClass = 'N/A';

            if ($item->user) {
                if ($item->user->role == 'guru') {
                    // Jika Guru, ambil Subject (Mata Pelajaran)
                    $displayClass = $item->user->subject ?? 'Guru'; 
                } else {
                    // Jika Siswa, gabungkan Kelas + Jurusan
                    $kelas = $item->user->class;
                    $jurusan = $item->user->major;
                    
                    if (!empty($kelas) || !empty($jurusan)) {
                        $displayClass = trim("$kelas $jurusan");
                    }
                }
            }
            // ==========================================================

            $writer->addRow([
                'Nama Peminjam'         => $item->user->name ?? 'User Terhapus',
                'Role'                  => ucfirst($item->user->role ?? '-'),
                'Kelas / Mapel'         => $displayClass, // Menggunakan hasil logika di atas
                'Judul Buku'            => $item->bookCopy->book->title ?? '-',
                'Kode Eksemplar'        => $item->bookCopy->book_code ?? '-',
                'Tanggal Pinjam'        => Carbon::parse($item->borrowed_at)->format('d-m-Y'),
                'Tanggal Kembali'       => $item->returned_at ? Carbon::parse($item->returned_at)->format('d-m-Y') : '-',
                'Status Pengembalian'   => $statusText,
                'Petugas Approval'      => $item->approvedBy->name ?? 'N/A',
                'Petugas Pengembalian'  => $item->returnedBy->name ?? 'N/A',
            ]);
        }
        
        return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);
    }
}