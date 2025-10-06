<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Buku Baru</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

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
        .img-preview{ width:100%; height:auto; max-width:160px; max-height:220px; object-fit:cover; border:1px solid #e9ecef; border-radius:6px; background:#fff; display:block; }
        @media (max-width:575.98px){
            .actions-row .btn { width:100%; }
            .actions-row .btn + .btn { margin-top:10px; }
            .preview-col { display:flex; justify-content:center; }
        }
        @media (min-width:576px){
            .actions-row .btn { min-width:140px; }
        }
    </style>
</head>
<body>

    <header class="topbar py-3">
        {{-- ... Kode header Anda ... --}}
    </header>

    <main class="container py-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-hero mb-3">
                    <div class="card-body">
                        <h1 class="h4 mb-1">Tambah Buku Baru</h1>
                        <p class="text-muted mb-0">Masukkan informasi buku untuk ditambahkan ke katalog perpustakaan.</p>
                    </div>
                </div>

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
                        <form action="{{ route('admin.petugas.books.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf

                            {{-- Judul, Penulis, Genre tetap sama --}}
                            <div class="mb-3">
                                <label for="title" class="form-label required">Judul Buku</label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required class="form-control @error('title') is-invalid @enderror" placeholder="Masukkan judul buku">
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="author" class="form-label required">Penulis</label>
                                <input type="text" id="author" name="author" value="{{ old('author') }}" required class="form-control @error('author') is-invalid @enderror" placeholder="Nama penulis">
                                @error('author')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="genre_id" class="form-label required">Genre</label>
                                <select id="genre_id" name="genre_id" required class="form-select @error('genre_id') is-invalid @enderror">
                                    <option value="">-- Pilih Genre --</option>
                                    @foreach ($genres as $genre)
                                        <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>
                                            {{ $genre->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('genre_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Kode Awal & Stok --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    {{-- =============================================== --}}
                                    {{-- TAMBAHAN: Input untuk Kode Awal / Alias Buku  --}}
                                    {{-- =============================================== --}}
                                    <label for="initial_code" class="form-label required">Kode Awal Buku</label>
                                    <input type="text" id="initial_code" name="initial_code" value="{{ old('initial_code') }}" required
                                           class="form-control @error('initial_code') is-invalid @enderror" placeholder="Contoh: AGAMA, FIKSI">
                                    <div class="form-text help-text">Kode singkat untuk buku, misal: AGAMA, FIKSI, MTK. Maksimal 10 karakter.</div>
                                    @error('initial_code')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="stock" class="form-label required">Jumlah Stok</label>
                                    <input type="number" id="stock" name="stock" min="1" value="{{ old('stock', 1) }}" required
                                           class="form-control @error('stock') is-invalid @enderror">
                                    <div class="form-text help-text">Jumlah eksemplar fisik buku yang ditambahkan.</div>
                                    @error('stock')
                                      <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Sampul & Preview --}}
                            <div class="row g-3 align-items-center mb-3">
                                <div class="col-sm-8">
                                    <label for="cover_image" class="form-label">Sampul Buku (Cover)</label>
                                    <input class="form-control @error('cover_image') is-invalid @enderror" type="file" id="cover_image" name="cover_image" accept="image/*">
                                    <div class="form-text help-text">Format JPG/PNG, maksimal 2MB.</div>
                                    @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-4 preview-col text-center">
                                    <label class="form-label d-block">Preview Sampul</label>
                                    <img id="coverPreview" class="img-preview" src="{{ asset('images/placeholder-cover.png') }}" alt="Preview Sampul">
                                </div>
                            </div>
                            
                            {{-- Tombol Aksi --}}
                            <div class="d-flex flex-column flex-sm-row gap-2 actions-row mt-4">
                                <button type="submit" class="btn btn-danger"><i class="bi bi-save"></i> Simpan Buku</button>
                                <a href="{{ route('admin.petugas.books.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            {{-- Sidebar Anda tetap sama --}}
            <div class="col-lg-4">
                {{-- ... Kode sidebar Anda ... --}}
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        {{-- ... Kode JavaScript Anda ... --}}
    </script>
</body>
</html>