@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-2 page-title">Daftar Akun Guru</h1>
                    <p class="text-muted mb-0 small">Berikut adalah daftar semua akun guru yang terdaftar dalam sistem.</p>
                </div>
                <a href="{{ route('admin.petugas.teachers.create') }}" class="btn btn-danger">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Akun Guru
                </a>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Card --}}
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-person-badge me-2 text-danger"></i>
                    Data Guru
                </h5>
                <span class="badge bg-secondary">{{ count($teachers) }} Guru</span>
            </div>
        </div>
        
        <div class="card-body p-0">
            {{-- Desktop Table View --}}
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3 ps-4" style="width: 5%;">No</th>
                            <th class="py-3">Nama Guru</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Mata Pelajaran</th>
                            <th class="py-3 pe-4 text-center" style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $index => $teacher)
                        <tr>
                            <td class="ps-4">
                                <span class="text-muted fw-medium">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-danger text-white me-3">
                                        {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="fw-semibold text-dark">{{ $teacher->name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-envelope me-2"></i>
                                    <span>{{ $teacher->email }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    <i class="bi bi-book me-1"></i>
                                    {{ $teacher->subject }}
                                </span>
                            </td>
                            <td class="pe-4 text-center">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Aktif
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox d-block"></i>
                                    <p class="mb-2 fw-medium">Belum Ada Data Guru</p>
                                    <p class="small text-muted mb-3">Silakan tambahkan akun guru terlebih dahulu.</p>
                                    <a href="{{ route('admin.petugas.teachers.create') }}" class="btn btn-danger btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Guru Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile/Tablet Card View --}}
            <div class="d-lg-none p-3">
                @forelse ($teachers as $index => $teacher)
                <div class="card mb-3 border shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="avatar-circle bg-danger text-white me-3" style="width: 50px; height: 50px; font-size: 1.25rem;">
                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ $teacher->name }}</h6>
                                        <span class="badge bg-success small">
                                            <i class="bi bi-check-circle me-1"></i>Aktif
                                        </span>
                                    </div>
                                    <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-top pt-3">
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-envelope me-1"></i> Email
                                </small>
                                <span class="fw-medium">{{ $teacher->email }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-book me-1"></i> Mata Pelajaran
                                </small>
                                <span class="badge bg-primary">{{ $teacher->subject }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <i class="bi bi-inbox d-block"></i>
                    <p class="mb-2 fw-medium">Belum Ada Data Guru</p>
                    <p class="small text-muted mb-3">Silakan tambahkan akun guru terlebih dahulu.</p>
                    <a href="{{ route('admin.petugas.teachers.create') }}" class="btn btn-danger btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Guru Sekarang
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Card Footer with Info --}}
        @if(count($teachers) > 0)
        <div class="card-footer bg-light border-top">
            <div class="d-flex justify-content-between align-items-center text-muted small">
                <span>
                    <i class="bi bi-info-circle me-1"></i>
                    Total {{ count($teachers) }} guru terdaftar
                </span>
                <span>
                    <i class="bi bi-clock me-1"></i>
                    Diperbarui: {{ now()->format('d M Y, H:i') }}
                </span>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Custom Styles --}}
<style>
    .btn-danger {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(217, 83, 79, 0.3);
    }

    .card-footer {
        padding: 1rem 1.5rem;
    }

    .badge {
        font-weight: 500;
        padding: 0.5rem 0.75rem;
    }

    @media (max-width: 991.98px) {
        .avatar-circle {
            width: 50px !important;
            height: 50px !important;
            font-size: 1.25rem !important;
        }
    }
</style>
@endsection