@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Kelola Pengajuan Pinjaman</h1>
            <p class="text-muted mb-0">Daftar pengajuan peminjaman buku yang menunggu konfirmasi.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Kode Buku</th>
                            <th>Tanggal Pengajuan</th>
                            <th style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingBorrowings as $borrow)
                        <tr>
                            <td>{{ $borrow->user->name }}</td>
                            <td>{{ $borrow->bookCopy->book->title }}</td>
                            <td>{{ $borrow->bookCopy->book_code }}</td>
                            <td>{{ $borrow->created_at->format('d M Y, H:i') }}</td>
                            
                            {{-- =============================================== --}}
                            {{-- PERUBAHAN DI SINI: Menambahkan Tombol Tolak  --}}
                            {{-- =============================================== --}}
                            <td class="d-flex gap-2">
                                {{-- Tombol Konfirmasi --}}
                                <form action="{{ route('admin.petugas.approvals.approve', $borrow) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Konfirmasi</button>
                                </form>
                            
                                {{-- Tombol Tolak --}}
                                <form action="{{ route('admin.petugas.approvals.reject', $borrow) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada pengajuan pinjaman baru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection