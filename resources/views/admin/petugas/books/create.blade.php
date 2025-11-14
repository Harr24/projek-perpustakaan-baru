@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            {{-- Menampilkan error validasi general (jika ada) --}}
            @if ($errors->any() && !$errors->has('books.*'))
                <div class="alert alert-danger">
                    <h5 class="alert-heading">Validasi Gagal!</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            {{-- Jangan tampilkan error 'books.*' di sini --}}
                            @if (!Str::startsWith($error, 'books.'))
                                <li>{{ $error }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h1 class="h4 mb-0 fw-bold">Tambah Buku Baru</h1>
                    <a href="{{ route('admin.petugas.books.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.petugas.books.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Layout 2 Kolom: Kiri (Data Teks), Kanan (Data Pilihan) --}}
                        <div class="row">
                            
                            {{-- KOLOM KIRI --}}
                            <div class="col-md-8">
                                
                                {{-- Judul Buku --}}
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Buku <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- Penulis --}}
                                <div class="mb-3">
                                    <label for="author" class="form-label">Penulis <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('author') is-invalid @enderror" 
                                           id="author" name="author" value="{{ old('author') }}" required>
                                    @error('author')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- Sinopsis --}}
                                <div class="mb-3">
                                    <label for="synopsis" class="form-label">Sinopsis (Opsional)</label>
                                    <textarea class="form-control @error('synopsis') is-invalid @enderror" 
                                              id="synopsis" name="synopsis" rows="6">{{ old('synopsis') }}</textarea>
                                    @error('synopsis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- Upload Cover --}}
                                <div class="mb-3">
                                    <label for="cover_image" class="form-label">Sampul Buku (Opsional)</label>
                                    <input class="form-control @error('cover_image') is-invalid @enderror" type="file" 
                                           id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/jpg">
                                    <small class="form-text text-muted">Maks. 2MB (JPG, PNG)</small>
                                    @error('cover_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            
                            {{-- KOLOM KANAN --}}
                            <div class="col-md-4">
                                
                                {{-- Genre --}}
                                <div class="mb-3">
                                    <label for="genre_id" class="form-label">Genre <span class="text-danger">*</span></label>
                                    <select class="form-select @error('genre_id') is-invalid @enderror" id="genre_id" name="genre_id" required>
                                        <option value="" disabled selected>-- Pilih Genre --</option>
                                        @foreach($genres as $genre)
                                            <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>
                                                {{ $genre->name }} ({{ $genre->genre_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('genre_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- ========================================================== --}}
                                {{-- ===== 🔥 INI DIA TAMBAHAN BARUNYA 🔥 ===== --}}
                                {{-- ========================================================== --}}
                                <div class="mb-3">
                                    <label for="shelf_id" class="form-label">Lokasi Rak <span class="text-danger">*</span></label>
                                    <select class="form-select @error('shelf_id') is-invalid @enderror" id="shelf_id" name="shelf_id" required>
                                        <option value="" disabled selected>-- Pilih Lokasi Rak --</option>
                                        @foreach($shelves as $shelf)
                                            <option value="{{ $shelf->id }}" {{ old('shelf_id') == $shelf->id ? 'selected' : '' }}>
                                                {{ $shelf->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shelf_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- ========================================================== --}}
                                
                                {{-- Tipe Buku --}}
                                <div class="mb-3">
                                    <label for="book_type" class="form-label">Tipe Buku <span class="text-danger">*</span></label>
                                    <select class="form-select @error('book_type') is-invalid @enderror" id="book_type" name="book_type" required>
                                        <option value="reguler" {{ old('book_type') == 'reguler' ? 'selected' : '' }}>Reguler (Bisa dipinjam)</option>
                                        <option value="paket" {{ old('book_type') == 'paket' ? 'selected' : '' }}>Paket (Buku Pelajaran)</option>
                                        <option value="laporan" {{ old('book_type') == 'laporan' ? 'selected' : '' }}>Laporan (Hanya baca di tempat)</option>
                                    </select>
                                    @error('book_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- Tahun Terbit --}}
                                <div class="mb-3">
                                    <label for="publication_year" class="form-label">Tahun Terbit (Opsional)</label>
                                    <input type="number" class="form-control @error('publication_year') is-invalid @enderror" 
                                           id="publication_year" name="publication_year" value="{{ old('publication_year') }}" 
                                           placeholder="Contoh: 2023" min="1900" max="{{ date('Y') }}">
                                    @error('publication_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <hr class="my-3">
                                
                                {{-- Kode Awal --}}
                                <div class="mb-3">
                                    <label for="initial_code" class="form-label">Kode Awal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('initial_code') is-invalid @enderror" 
                                           id="initial_code" name="initial_code" value="{{ old('initial_code') }}" 
                                           placeholder="Contoh: IPA" maxlength="10" required>
                                    <small class="form-text text-muted">Contoh: 001-IPA-001</small>
                                    @error('initial_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- Stok --}}
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Jumlah Stok <span class="text-danger">*</span></label>
                                    <input type="number" class_control @error('stock') is-invalid @enderror" 
                                           id="stock" name="stock" value="{{ old('stock') }}" 
                                           placeholder="Contoh: 10" min="1" max="100" required>
                                    <small class="form-text text-muted">Jumlah eksemplar yang akan dibuat.</small>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.petugas.books.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Buku dan Buat Eksemplar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection