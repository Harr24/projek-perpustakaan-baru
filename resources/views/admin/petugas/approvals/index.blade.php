@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-2" style="color: #d9534f;">Kelola Pengajuan Pinjaman</h1>
                    <p class="text-muted mb-0 small">Daftar pengajuan peminjaman buku yang menunggu konfirmasi.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ========================================================== --}}
    {{-- PERUBAHAN UTAMA: Tabel dibungkus dengan <form>          --}}
    {{-- ========================================================== --}}
    <form action="{{ route('admin.petugas.approvals.approveMultiple') }}" method="POST" id="bulk-approve-form">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2 text-warning"></i>
                        Peminjaman Menunggu Konfirmasi
                        <span class="badge bg-warning text-dark ms-2">{{ $pendingBorrowings->count() }}</span>
                    </h5>
                    {{-- Tombol untuk submit form konfirmasi massal --}}
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check2-all me-1"></i> Konfirmasi yang Dipilih
                    </button>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-4" style="width: 5%;">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </th>
                                <th class="py-3">Nama Peminjam</th>
                                <th class="py-3">Judul Buku</th>
                                <th class="py-3">Kode Buku</th>
                                <th class="py-3">Tanggal Pengajuan</th>
                                <th class="py-3 pe-4 text-end" style="width: 200px;">Aksi Individual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingBorrowings as $borrow)
                            <tr>
                                <td class="ps-4">
                                    <input class="form-check-input" type="checkbox" name="borrowing_ids[]" value="{{ $borrow->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-2" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                            {{ strtoupper(substr($borrow->user->name, 0, 1)) }}
                                        </div>
                                        <span class="fw-medium">{{ $borrow->user->name }}</span>
                                    </div>
                                </td>
                                <td><span class="text-dark">{{ $borrow->bookCopy->book->title }}</span></td>
                                <td><span class="badge bg-secondary">{{ $borrow->bookCopy->book_code }}</span></td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $borrow->created_at->format('d M Y, H:i') }}
                                    </small>
                                </td>
                                <td class="pe-4 text-end">
                                    {{-- Aksi individual tidak perlu diubah --}}
                                    <div class="d-flex gap-2 justify-content-end">
                                        <form action="{{ route('admin.petugas.approvals.approve', $borrow) }}" method="POST" class="d-inline"> @csrf <button type="submit" class="btn btn-success btn-sm" title="Konfirmasi"><i class="bi bi-check-lg"></i></button></form>
                                        <form action="{{ route('admin.petugas.approvals.reject', $borrow) }}" method="POST" class="d-inline" onsubmit="return confirm('Tolak pengajuan ini?');"> @csrf <button type="submit" class="btn btn-danger btn-sm" title="Tolak"><i class="bi bi-x-lg"></i></button></form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
                                        <p class="mb-0">Tidak ada pengajuan pinjaman baru.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Script untuk fungsi "Pilih Semua" checkbox
    document.getElementById('selectAll').addEventListener('click', function(event) {
        const checkboxes = document.querySelectorAll('input[name="borrowing_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = event.target.checked;
        });
    });
</script>
@endpush