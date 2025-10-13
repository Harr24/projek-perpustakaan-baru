@extends('layouts.app') {{-- Sesuaikan dengan layout admin-mu --}}

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">Riwayat Peminjaman</h1>
            <p class="text-muted mb-0 small">Menampilkan semua aktivitas peminjaman oleh: <strong class="text-dark">{{ $user->name }}</strong></p>
        </div>
         <a href="{{ route('admin.petugas.reports.borrowings.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Laporan
        </a>
    </div>

    {{-- KARTU INFO PENGGUNA --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="card-title fw-bold">{{ $user->name }}</h5>
                    <p class="card-text text-muted mb-2"><i class="bi bi-envelope-fill me-2"></i>{{ $user->email }}</p>
                    <p class="card-text text-muted mb-2"><i class="bi bi-person-badge-fill me-2"></i>{{ $user->class_name ?? 'Kelas tidak tersedia' }}</p>
                    
                    {{-- ========================================================== --}}
                    {{-- PERUBAHAN DI SINI: Tambahkan Nomor WhatsApp --}}
                    {{-- ========================================================== --}}
                    <p class="card-text text-muted mb-0"><i class="bi bi-whatsapp me-2"></i>{{ $user->phone_number ?? 'Nomor tidak tersedia' }}</p>
                    
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge fs-6 bg-info">{{ ucfirst($user->role) }}</span>
                </div>
            </div>
        </div>
    </div>
    
    {{-- TABEL RIWAYAT --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Daftar Buku yang Pernah Dipinjam</h6>
        </div>
        <div class="card-body p-0">
             <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3 ps-4">No</th>
                            <th class="py-3">Judul Buku</th>
                            <th class="py-3">Kode Salinan</th>
                            <th class="py-3">Tgl. Pinjam</th>
                            <th class="py-3">Tgl. Kembali</th>
                            <th class="py-3 pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $item)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>{{ $item->bookCopy->book->title }}</td>
                                <td><span class="badge bg-light text-dark">{{ $item->bookCopy->book_code }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($item->borrowed_at)->format('d M Y') }}</td>
                                <td>{{ $item->returned_at ? \Carbon\Carbon::parse($item->returned_at)->format('d M Y') : 'Belum Kembali' }}</td>
                                <td class="pe-4">
                                    @if($item->status == 'returned' || $item->status == 'dikembalikan')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @elseif($item->status == 'borrowed' || $item->status == 'dipinjam')
                                        <span class="badge bg-warning text-dark">Dipinjam</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="text-muted mb-0">Pengguna ini belum pernah meminjam buku.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection