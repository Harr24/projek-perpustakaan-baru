<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Buku - {{ $book->title }}</title>

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
    .img-preview{ width:100%; height:auto; max-width:160px; max-height:240px; object-fit:cover; border:1px solid #e9ecef; border-radius:6px; background:#fff; display:block; }
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
            <h1 class="h4 mb-1">Edit Buku: <strong>{{ $book->title }}</strong></h1>
            <p class="text-muted mb-0">Perbarui data buku. Kosongkan field sampul jika tidak ingin mengubah gambar.</p>
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
            <form action="{{ route('admin.petugas.books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
              @csrf
              @method('PUT')

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

              <div class="mb-3">
                <label for="author" class="form-label required">Penulis</label>
                <input type="text" id="author" name="author" value="{{ old('author', $book->author) }}" required
                       class="form-control @error('author') is-invalid @enderror" placeholder="Nama penulis">
                @error('author')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                  <label for="synopsis" class="form-label">Sinopsis</label>
                  <textarea id="synopsis" name="synopsis" rows="4" class="form-control @error('synopsis') is-invalid @enderror">{{ old('synopsis', $book->synopsis) }}</textarea>
                  <div class="form-text help-text">Deskripsi singkat atau ringkasan cerita dari buku. (Opsional)</div>
                  @error('synopsis')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label for="genre_id" class="form-label required">Genre</label>
                <select id="genre_id" name="genre_id" required class="form-select @error('genre_id') is-invalid @enderror">
                  @foreach ($genres as $genre)
                    <option value="{{ $genre->id }}" {{ (old('genre_id', $book->genre_id) == $genre->id) ? 'selected' : '' }}>
                      {{ $genre->name }}
                    </option>
                  @endforeach
                </select>
                @error('genre_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-md-6">
                  <label class="form-label">Sampul Buku Saat Ini</label>
                  @if($book->cover_image)
                    <img id="currentCover" src="{{ Storage::url($book->cover_image) }}" alt="Cover" class="img-preview mb-2">
                  @else
                    <div class="mb-2 text-muted">Tidak ada gambar</div>
                    <img id="currentCover" src="{{ asset('images/placeholder-cover.png') }}" alt="Cover" class="img-preview mb-2">
                  @endif
                  <div class="form-text help-text">Ganti sampul hanya jika perlu.</div>
                </div>

                <div class="col-12 col-md-6">
                  <label for="cover_image" class="form-label">Ganti Sampul Buku (Kosongkan jika tidak diubah)</label>
                  <input class="form-control @error('cover_image') is-invalid @enderror" type="file" id="cover_image" name="cover_image" accept="image/*">
                  @error('cover_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror

                  <div class="mt-3 preview-col text-center">
                    <label class="form-label d-block">Preview Gambar Baru</label>
                    <img id="newCoverPreview" class="img-preview" src="{{ asset('images/placeholder-cover.png') }}" alt="Preview baru">
                  </div>
                </div>
              </div>

              <div class="d-flex flex-column flex-sm-row gap-2 actions-row">
                <button type="submit" class="btn btn-danger">
                  <i class="bi bi-save"></i> Update Buku
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
        
        {{-- ... Kode Modal dan sidebar Anda ... --}}

      </div>
    </div>
  </main>
  
  {{-- ... Kode Javascript Anda ... --}}

</body>
</html>