@extends('layouts.app')

@section('styles')
<style>
    :root{
        --brand-red: #c62828;
        --brand-red-dark: #a21f1f;
    }
    body{ font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, Arial; background:#f6f7fb; }
    .topbar{ background: linear-gradient(180deg, var(--brand-red), var(--brand-red-dark)); color:#fff; }
    .card-hero{ margin-top:-28px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
    .form-card{ border-left:4px solid var(--brand-red); }
    label.required::after{ content: " *"; color:#d11; }
    .help-text{ font-size:0.9rem; color:#6c757d; }
    .img-preview{ width:100%; height:auto; max-width:160px; max-height:240px; object-fit:cover; border:1px solid #e9ecef; border-radius:6px; background:#fff; display:block; }
    .copies-table th, .copies-table td { font-size: 0.9rem; padding: 0.5rem 0.75rem; vertical-align: middle;}
    @media (max-width:575.98px){
        .actions-row .btn { width:100%; }
        .actions-row .btn + .btn { margin-top:10px; }
        .preview-col { display:flex; justify-content:center; }
    }
    @media (min-width:576px){
        .actions-row .btn { min-width:140px; }
    }
</style>
@endsection

@section('content')
<main class="container py-4">
    <div class="row g-4">
        {{-- Kolom Kiri: Form Edit Utama --}}
        <div class="col-lg-8">
            <div class="card card-hero mb-3">
                <div class="card-body">
                    <h1 class="h4 mb-1">Edit Buku: <strong>{{ $book->title }}</strong></h1>
                    <p class="text-muted mb-0">Perbarui data buku. Kosongkan field sampul jika tidak ingin mengubah gambar.</p>
                </div>
            </div>

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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Error:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card form-card">
                <div class="card-body">
                    <form action="{{ route('admin.petugas.books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Judul Buku --}}
                        <div class="mb-3">
                            <label for="title" class="form-label required">Judul Buku</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $book->title) }}" required
                                   class="form-control @error('title') is-invalid @enderror" placeholder="Masukkan judul buku">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text help-text">Judul yang deskriptif memudahkan pencarian.</div>
                            @enderror
                        </div>

                        {{-- Penulis --}}
                        <div class="mb-3">
                            <label for="author" class="form-label required">Penulis</label>
                            <input type="text" id="author" name="author" value="{{ old('author', $book->author) }}" required
                                   class="form-control @error('author') is-invalid @enderror" placeholder="Nama penulis">
                            @error('author') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        {{-- Tahun Terbit --}}
                        <div class="mb-3 col-md-4">
                            <label for="publication_year" class="form-label">Tahun Terbit</label>
                            <input type="number" id="publication_year" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}"
                                   class="form-control @error('publication_year') is-invalid @enderror" placeholder="Contoh: 2023" min="1900" max="{{ date('Y') }}">
                            @error('publication_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Sinopsis --}}
                        <div class="mb-3">
                            <label for="synopsis" class="form-label">Sinopsis</label>
                            <textarea id="synopsis" name="synopsis" rows="4" class="form-control @error('synopsis') is-invalid @enderror">{{ old('synopsis', $book->synopsis) }}</textarea>
                            <div class="form-text help-text">Deskripsi singkat atau ringkasan cerita dari buku. (Opsional)</div>
                            @error('synopsis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Genre --}}
                        <div class="mb-3">
                            <label for="genre_id" class="form-label required">Genre</label>
                            <select id="genre_id" name="genre_id" required class="form-select @error('genre_id') is-invalid @enderror">
                                @foreach ($genres as $genre)
                                    <option value="{{ $genre->id }}" {{ (old('genre_id', $book->genre_id) == $genre->id) ? 'selected' : '' }}>
                                        {{ $genre->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('genre_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        {{-- Lokasi Rak --}}
                        <div class="mb-3">
                            <label for="shelf_id" class="form-label required">Lokasi Rak</label>
                            <select id="shelf_id" name="shelf_id" required class="form-select @error('shelf_id') is-invalid @enderror">
                                <option value="" disabled>-- Pilih Lokasi Rak --</option>
                                @foreach ($shelves as $shelf)
                                    <option value="{{ $shelf->id }}" {{ (old('shelf_id', $book->shelf_id) == $shelf->id) ? 'selected' : '' }}>
                                        {{ $shelf->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shelf_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Tipe Buku --}}
                        <div class="mb-3">
                            <label for="book_type" class="form-label required">Tipe Buku</label>
                            <select id="book_type" name="book_type" required class="form-select @error('book_type') is-invalid @enderror">
                                <option value="reguler" {{ (old('book_type', $book->book_type) == 'reguler') ? 'selected' : '' }}>
                                    Buku Reguler
                                </option>
                                <option value="paket" {{ (old('book_type', $book->book_type) == 'paket') ? 'selected' : '' }}>
                                    Buku Paket
                                </option>
                                <option value="laporan" {{ (old('book_type', $book->book_type) == 'laporan') ? 'selected' : '' }}>
                                    Buku Laporan
                                </option>
                            </select>
                            <div class="form-text help-text mt-0">Pilih tipe buku untuk menentukan aturan peminjaman.</div>
                            @error('book_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Sampul Buku --}}
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Sampul Buku Saat Ini</label>
                                @if($book->cover_image && Storage::disk('public')->exists($book->cover_image))
                                    <img id="currentCover" src="{{ Storage::url($book->cover_image) }}" alt="Cover" class="img-preview mb-2">
                                @else
                                    <div class="mb-2 text-muted small">Tidak ada gambar</div>
                                    <img id="currentCover" src="https://placehold.co/160x240/eef0f2/6c757d?text=No+Image" alt="Placeholder" class="img-preview mb-2">
                                @endif
                                <div class="form-text help-text">Ganti sampul hanya jika perlu.</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="cover_image" class="form-label">Ganti Sampul Buku (Kosongkan jika tidak diubah)</label>
                                <input class="form-control @error('cover_image') is-invalid @enderror" type="file" id="cover_image" name="cover_image" accept="image/*" onchange="previewImage(event)">
                                @error('cover_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="mt-3 preview-col text-center">
                                    <label class="form-label d-block">Preview Gambar Baru</label>
                                    <img id="newCoverPreview" class="img-preview" src="https://placehold.co/160x240/eef0f2/6c757d?text=Preview" alt="Preview baru">
                                </div>
                            </div>
                        </div>

                        {{-- Form untuk Tambah Stok --}}
                        <div class="mb-4 pt-3 border-top">
                            <label for="add_stock" class="form-label fw-semibold">Tambah Jumlah Stok (Eksemplar)</label>
                            <input type="number" id="add_stock" name="add_stock"
                                   class="form-control @error('add_stock') is-invalid @enderror"
                                   placeholder="Masukkan jumlah stok baru" min="1" max="100">
                            @error('add_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text help-text">
                                    Masukkan jumlah eksemplar baru yang ingin ditambahkan. Kode buku akan dibuat otomatis melanjutkan nomor terakhir. Kosongkan jika tidak ingin menambah stok.
                                </div>
                            @enderror
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex flex-column flex-sm-row gap-2 actions-row pt-3 border-top">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-save"></i> Update Buku & Stok
                            </button>
                            <a href="{{ route('admin.petugas.books.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-outline-danger ms-auto" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash"></i> Hapus Buku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Eksemplar --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-list-ol me-2"></i> Daftar Eksemplar (Stok)</h6>
                    <span class="badge bg-secondary">{{ $book->copies->count() }} Total</span>
                </div>
                <div class="card-body p-0">
                    @if ($book->copies->isNotEmpty())
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-striped table-hover mb-0 copies-table">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Kode Buku</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($book->copies as $copy)
                                        <tr>
                                            <td><span class="badge bg-secondary fw-normal">{{ $copy->book_code }}</span></td>
                                            <td>
                                                @if ($copy->status == 'tersedia')
                                                    <span class="badge bg-success">Tersedia</span>
                                                @elseif ($copy->status == 'dipinjam')
                                                    <span class="badge bg-warning text-dark">Dipinjam</span>
                                                @elseif ($copy->status == 'pending')
                                                    <span class="badge bg-info text-dark">Pending</span>
                                                @elseif ($copy->status == 'overdue')
                                                    <span class="badge bg-danger">Terlambat</span>
                                                @else
                                                    <span class="badge bg-dark">{{ ucfirst($copy->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($copy->status == 'tersedia')
                                                    <form action="{{ route('admin.petugas.books.copies.destroy', $copy->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus eksemplar {{ $copy->book_code }}?');" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm p-0 px-1" title="Hapus Eksemplar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @elseif ($copy->status == 'hilang')
                                                    <form action="{{ route('admin.petugas.books.copies.markFound', $copy->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menandai eksemplar {{ $copy->book_code }} sebagai DITEMUKAN?');" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-success btn-sm p-0 px-1" title="Tandai Ditemukan">
                                                            <i class="bi bi-check-circle-fill"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted" title="Tidak dapat dihapus (status: {{ $copy->status }})">
                                                        <i class="bi bi-lock-fill"></i>
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted">
                            Belum ada eksemplar untuk buku ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
 
{{-- Modal Konfirmasi Hapus Buku Utama --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus buku <strong>"{{ $book->title }}"</strong> beserta semua salinannya? Tindakan ini tidak dapat diurungkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.petugas.books.destroy', $book->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Buku</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('newCoverPreview');
            output.src = reader.result;
        };
        if(event.target.files[0]){
            reader.readAsDataURL(event.target.files[0]);
        } else {
            document.getElementById('newCoverPreview').src = "https://placehold.co/160x240/eef0f2/6c757d?text=Preview";
        }
    }

    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>
@endpush