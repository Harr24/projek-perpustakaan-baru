{{-- Menggunakan layout admin umum, ganti jika Superadmin punya layout khusus --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            {{-- Sesuaikan Judul untuk Superadmin --}}
            <h1 class="h3 fw-bold mb-1" style="color: #d9534f;">Manajemen Riwayat Denda (Superadmin)</h1>
            <p class="text-muted mb-0 small">Daftar semua denda keterlambatan yang sudah lunas.</p>
        </div>
         <div class="d-flex gap-2">
             <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                 <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
             </a>
             {{-- Tambahkan link ke Denda Aktif jika Superadmin perlu melihatnya --}}
             {{-- <a href="{{ route('admin.superadmin.fines.index') }}" class="btn btn-outline-danger btn-sm">
                 <i class="bi bi-clock-history me-1"></i> Lihat Denda Aktif
             </a> --}}
         </div>
    </div>

     {{-- Notifikasi Sukses/Error --}}
     @if(session('success'))
     <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
     @endif
     @if(session('error'))
     <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
     @endif

    {{-- Form Filter --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-3">
            {{-- Form action mengarah ke rute history Superadmin --}}
            <form action="{{ route('admin.superadmin.fines.history') }}" method="GET" class="row gx-2 gy-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="search" class="form-label small">Cari Nama/Judul</label>
                    <input type="text" name="search" id="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Nama peminjam atau judul...">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="year" class="form-label small">Tahun Lunas</label>
                    <select name="year" id="year" class="form-select form-select-sm">
                        <option value="">-- Semua --</option>
                        @foreach ($years as $year)
                            <option value="{{ $year->year }}" {{ request('year') == $year->year ? 'selected' : '' }}>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="month" class="form-label small">Bulan Lunas</label>
                    <select name="month" id="month" class="form-select form-select-sm">
                        <option value="">-- Semua --</option>
                        @php
                            $months = [
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                            ];
                        @endphp
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger btn-sm flex-grow-1"><i class="bi bi-funnel-fill"></i> Filter</button>
                        {{-- Tambahkan tombol Export jika diperlukan --}}
                        {{-- <a href="{{ route('admin.superadmin.fines.export', request()->query()) }}" class="btn btn-success btn-sm" title="Export ke Excel">
                            <i class="bi bi-file-earmark-excel-fill"></i> <span class="d-none d-lg-inline">Export</span>
                        </a> --}}
                         @if(request()->has('search') || request()->has('year') || request()->has('month'))
                            {{-- Link reset mengarah ke rute history Superadmin --}}
                            <a href="{{ route('admin.superadmin.fines.history') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Riwayat Denda --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white py-2">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-check-circle-fill me-2"></i>Denda Lunas</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0 small">
                    
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="py-2 px-3">Nama Peminjam</th>
                            <th class="py-2 px-3">Kelas</th>
                            <th class="py-2 px-3">Judul Buku</th>
                            <th class="py-2 px-3 text-end">Jml Denda</th>
                            <th class="py-2 px-3">Tgl Lunas</th>
                            <th class="py-2 px-3">Diproses Oleh</th> {{-- <-- KOLOM BARU --}}
                            <th class="py-2 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paidFines as $fine)
                            @php
                                // Ambil data pembayaran terakhir (yang paling baru)
                                $lastPayment = $fine->finePayments->last();
                            @endphp
                            <tr>
                                <td class="px-3">{{ $fine->user->name ?? 'Pengguna Dihapus' }}</td>
                                <td class="px-3">{{ $fine->user->class_name ?? 'N/A' }}</td>
                                <td class="px-3">
                                    {{ $fine->bookCopy->book->title ?? 'Buku Dihapus' }}
                                    <span class="d-block text-muted" style="font-size: 0.8em;">{{ $fine->bookCopy->book_code ?? 'Kode Dihapus' }}</span>
                                </td>
                                <td class="px-3 text-end">Rp{{ number_format($fine->fine_amount, 0, ',', '.') }}</td>
                                
                                {{-- UPDATE: Tgl Lunas diambil dari log pembayaran, bukan updated_at --}}
                                <td class="px-3">
                                    {{ $lastPayment ? $lastPayment->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </td>
                                
                                {{-- BARU: Tampilkan nama petugas yang memproses --}}
                                <td class="px-3">
                                    {{ $lastPayment && $lastPayment->processedBy ? $lastPayment->processedBy->name : 'N/A' }}
                                </td>
                                
                                <td class="px-3 text-center">
                                    <form action="{{ route('admin.superadmin.fines.destroy', $fine->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus riwayat denda ini secara permanen? Ini tidak bisa dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Riwayat Permanen">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- UPDATE: Colspan jadi 7 --}}
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-search d-block fs-1 mb-2 opacity-50"></i>
                                    Tidak ada data riwayat denda yang cocok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($paidFines->isNotEmpty())
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="3" class="px-3 py-2 text-end">Total Pemasukan (sesuai filter):</td>
                            <td class="px-3 py-2 text-end">
                                Rp {{ number_format($totalFine, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2" colspan="3"></td> {{-- UPDATE: Colspan jadi 3 --}}
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
             {{-- Pagination --}}
             @if ($paidFines->hasPages())
                 <div class="card-footer bg-white border-top-0 py-2">
                     {{ $paidFines->links() }}
                 </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection