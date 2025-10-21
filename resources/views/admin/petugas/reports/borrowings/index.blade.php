@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">Laporan Peminjaman Buku</h1>
            <p class="text-muted mb-0 small">Rekapitulasi semua aktivitas peminjaman buku oleh siswa dan guru.</p>
        </div>
         <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- KOTAK FILTER --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-filter me-2"></i>Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.petugas.reports.borrowings.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    {{-- Filter Bulan --}}
                    <div class="col-md-3">
                        <label for="month" class="form-label small">Bulan</label>
                        <select name="month" id="month" class="form-select">
                            <option value="">-- Semua Bulan --</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    {{-- Filter Tahun --}}
                    <div class="col-md-2">
                        <label for="year" class="form-label small">Tahun</label>
                        <input type="number" name="year" id="year" class="form-control" placeholder="Tahun" value="{{ request('year', date('Y')) }}">
                    </div>
                    {{-- Filter Nama Peminjam --}}
                    <div class="col-md-4">
                        <label for="search" class="form-label small">Nama Peminjam</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari nama siswa atau guru..." value="{{ request('search') }}">
                    </div>
                    {{-- Tombol Aksi --}}
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel-fill me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.petugas.reports.borrowings.export', request()->query()) }}" class="btn btn-success" title="Export ke Excel">
                            <i class="bi bi-file-earmark-excel"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- KOTAK TABEL DATA --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-table me-2"></i>Hasil Laporan</h6>
        </div>
        <div class="card-body p-0">
             <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3 ps-4">No</th>
                            <th class="py-3">Nama Peminjam</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Kelas</th>
                            <th class="py-3">Buku yang Dipinjam</th>
                            <th class="py-3">Tgl. Pinjam</th>
                            <th class="py-3">Tgl. Kembali</th>
                            {{-- ========================================================== --}}
                            {{-- PENAMBAHAN 1: Kolom Header untuk Petugas --}}
                            {{-- ========================================================== --}}
                            <th class="py-3">Petugas Approval</th>
                            <th class="py-3 pe-4">Petugas Pengembalian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($borrowings as $borrowing)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration + $borrowings->firstItem() - 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.petugas.reports.users.history', $borrowing->user) }}" class="text-decoration-none fw-semibold">
                                        {{ $borrowing->user->name }}
                                    </a>
                                </td>
                                <td><span class="badge bg-secondary">{{ ucfirst($borrowing->user->role) }}</span></td>
                                <td>{{ $borrowing->user->class_name ?? 'N/A' }}</td>
                                <td>{{ $borrowing->bookCopy->book->title }}</td>
                                <td>{{ \Carbon\Carbon::parse($borrowing->borrowed_at)->format('d M Y') }}</td>
                                <td>{{ $borrowing->returned_at ? \Carbon\Carbon::parse($borrowing->returned_at)->format('d M Y') : '-' }}</td>
                                {{-- ========================================================== --}}
                                {{-- PENAMBAHAN 2: Menampilkan Nama Petugas --}}
                                {{-- ========================================================== --}}
                                <td>{{ $borrowing->approvedBy->name ?? 'N/A' }}</td>
                                <td class="pe-4">{{ $borrowing->returnedBy->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                {{-- PENAMBAHAN 3: Menyesuaikan colspan --}}
                                <td colspan="9" class="text-center py-5">
                                    <p class="text-muted mb-0">Tidak ada data peminjaman yang cocok dengan filter Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($borrowings->hasPages())
            <div class="card-footer">
                {{ $borrowings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
