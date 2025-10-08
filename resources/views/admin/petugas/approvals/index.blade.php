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
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-2 text-warning"></i>
                    Peminjaman
                </h5>
                <span class="badge bg-warning text-dark">{{ count($pendingBorrowings) }} Menunggu</span>
            </div>
        </div>
        
        <div class="card-body p-0">
            {{-- Desktop Table View --}}
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4">Nama Peminjam</th>
                            <th class="py-3">Judul Buku</th>
                            <th class="py-3">Kode Buku</th>
                            <th class="py-3">Tanggal Pengajuan</th>
                            <th class="py-3 pe-4 text-center" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingBorrowings as $borrow)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                        {{ strtoupper(substr($borrow->user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-medium">{{ $borrow->user->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-dark">{{ $borrow->bookCopy->book->title }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $borrow->bookCopy->book_code }}</span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $borrow->created_at->format('d M Y, H:i') }}
                                </small>
                            </td>
                            <td class="pe-4">
                                <div class="d-flex gap-2 justify-content-center">
                                    <form action="{{ route('admin.petugas.approvals.approve', $borrow) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Konfirmasi">
                                            <i class="bi bi-check-lg me-1"></i>Konfirmasi
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.petugas.approvals.reject', $borrow) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" title="Tolak">
                                            <i class="bi bi-x-lg me-1"></i>Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
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

            {{-- Mobile Card View --}}
            <div class="d-lg-none p-3">
                @forelse ($pendingBorrowings as $borrow)
                <div class="card mb-3 border shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="avatar-circle bg-primary text-white me-3" style="width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0;">
                                {{ strtoupper(substr($borrow->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-semibold">{{ $borrow->user->name }}</h6>
                                <p class="mb-0 text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $borrow->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="border-top pt-3 mb-3">
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">Judul Buku</small>
                                <span class="fw-medium">{{ $borrow->bookCopy->book->title }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1">Kode Buku</small>
                                <span class="badge bg-secondary">{{ $borrow->bookCopy->book_code }}</span>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <form action="{{ route('admin.petugas.approvals.approve', $borrow) }}" method="POST" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-lg me-1"></i>Konfirmasi
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.petugas.approvals.reject', $borrow) }}" method="POST" class="flex-fill" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?');">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-x-lg me-1"></i>Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 d-block mb-3 text-muted opacity-25"></i>
                    <p class="text-muted mb-0">Tidak ada pengajuan pinjaman baru.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Custom Styles --}}
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .table > tbody > tr:hover {
        background-color: #f8f9fa;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .avatar-circle {
        font-size: 0.875rem;
    }
    
    @media (max-width: 991.98px) {
        .card-body .card {
            border-radius: 0.5rem;
        }
    }
</style>
@endsection