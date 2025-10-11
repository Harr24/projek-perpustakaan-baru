@extends('layouts.app') {{-- Pastikan ini sesuai dengan layout utama Anda --}}

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Halaman --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold mb-2" style="color: #d9534f;">Kelola Buku</h1>
            <p class="text-muted mb-0 small">Daftar semua koleksi buku yang ada di perpustakaan.</p>
        </div>
        <div class="d-flex gap-2">
             <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
            <a href="{{ route('admin.petugas.books.create') }}" class="btn btn-danger btn-sm">
                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Buku Baru
            </a>
        </div>
    </div>

    {{-- Notifikasi Sukses/Error --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            {{-- ======================================================= --}}
            {{-- PERUBAHAN 1: Mengganti Form Pencarian dengan Filter Lengkap --}}
            {{-- ======================================================= --}}
            <form action="{{ route('admin.petugas.books.index') }}" method="GET" class="row g-2 align-items-center">
                {{-- Filter by Genre --}}
                <div class="col-md-4">
                    <select name="genre_id" class="form-select form-select-sm">
                        <option value="">-- Semua Genre --</option>
                        @foreach ($genres as $genre)
                            <option value="{{ $genre->id }}" {{ request('genre_id') == $genre->id ? 'selected' : '' }}>
                                {{ $genre->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Search Input --}}
                <div class="col-md-8">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul atau penulis..." value="{{ request('search') }}">
                        <button class="btn btn-danger" type="submit"><i class="bi bi-search"></i> Cari</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4" style="width: 5%;">No</th>
                            <th class="py-3" style="width: 10%;">Sampul</th>
                            <th class="py-3">Judul & Penulis</th>
                            <th class="py-3">Genre</th>
                            <th class="py-3">Tahun</th>
                            <th class="py-3">Stok</th>
                            <th class="py-3 pe-4 text-end" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($books as $book)
                        <tr>
                            {{-- Penomoran yang benar untuk paginasi, kodemu sudah bagus! --}}
                            <td class="ps-4">{{ $loop->iteration + ($books->currentPage() - 1) * $books->perPage() }}</td>
                            <td>
                                <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : 'https://placehold.co/80x120/E91E63/FFFFFF?text=No+Cover' }}" 
                                     alt="Cover" class="img-fluid rounded" style="width: 60px; height: 90px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold">{{ $book->title }}</div>
                                <small class="text-muted">{{ $book->author }}</small>
                            </td>
                            <td><span class="badge bg-secondary">{{ $book->genre->name ?? 'N/A' }}</span></td>
                            <td>{{ $book->publication_year ?? 'N/A' }}</td>
                            <td>
                                <span class="badge 
                                    @if($book->copies_count > 10) bg-success
                                    @elseif($book->copies_count > 0) bg-warning text-dark
                                    @else bg-danger @endif">
                                    {{ $book->copies_count }} Salinan
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.petugas.books.show', $book) }}" class="btn btn-info btn-sm" title="Detail"><i class="bi bi-eye-fill"></i></a>
                                    <a href="{{ route('admin.petugas.books.edit', $book) }}" class="btn btn-warning btn-sm" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('admin.petugas.books.destroy', $book) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus buku ini dan semua salinannya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-search display-4 d-block mb-3 opacity-25"></i>
                                    <p class="mb-0">
                                        {{-- ======================================================= --}}
                                        {{-- PERUBAHAN 2: Pesan Kosong yang Lebih Informatif --}}
                                        {{-- ======================================================= --}}
                                        @if(request('search') || request('genre_id'))
                                            Tidak ada buku yang cocok dengan kriteria filter Anda.
                                        @else
                                            Belum ada data buku. Silakan tambahkan buku baru.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($books->hasPages())
            <div class="card-footer bg-white">
                {{-- Link Paginasi, kodemu sudah benar! --}}
                {{ $books->links() }}
            </div>
        @endif
    </div>
</div>
@endsection