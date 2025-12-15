<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Genre - {{ $genre->name }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    {{-- Script SweetAlert2 untuk Pop-up Hapus --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root{
            --brand-red: #c62828;
            --brand-red-dark: #a21f1f;
        }
        body{ 
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, Arial; 
            background:#f8f9fa; 
        }
        
        /* HEADER STYLE */
        .topbar{ 
            background: var(--brand-red); 
            color:#fff; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .initials-avatar {
            width: 44px;
            height: 44px;
            background-color: rgba(255,255,255,0.2); 
            color: #fff;
            font-weight: 700;
        }
        .btn-logout {
            background: none;
            border: 1px solid rgba(255,255,255,0.5);
            color: rgba(255,255,255,0.8);
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }
        .btn-logout:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-color: #fff;
        }

        label.required::after{ content: " *"; color:#d11; }
        .help-text{ font-size:0.9rem; color:#6c757d; }
    </style>
</head>
<body>

    <header class="topbar py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center initials-avatar">
                    {{ Str::upper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="small text-white-50">Selamat Datang,</div>
                    <div class="h6 mb-0 text-white fw-bold">{{ Auth::user()->name }}</div>
                </div>
            </div>
            <div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="bi bi-box-arrow-right me-1"></i> LOGOUT
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="container py-5">
        <div class="row g-4 g-lg-5">
            
            {{-- Kolom Form Utama (Kiri) --}}
            <div class="col-lg-8">
                
                <div class="mb-4">
                    <h1 class="h3 fw-bold text-gray-800">Edit Genre: {{ $genre->name }}</h1>
                    <p class="text-muted mb-0">Perbarui nama, kode, atau ikon genre buku.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <h6 class="alert-heading fw-bold">Oops! Ada kesalahan:</h6>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        {{-- PENTING: Tambahkan enctype="multipart/form-data" --}}
                        <form action="{{ route('admin.petugas.genres.update', $genre->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="genre_code" class="form-label required">Kode Genre (DDC)</label>
                                <input type="text" id="genre_code" name="genre_code" 
                                       value="{{ old('genre_code', $genre->genre_code) }}" required
                                       class="form-control form-control-lg @error('genre_code') is-invalid @enderror" 
                                       placeholder="Contoh: 000, 100, 200">
                                @error('genre_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="name" class="form-label required">Nama Genre</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $genre->name) }}" required
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="Contoh: Fiksi, Sejarah">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- TAMBAHAN: Input Edit Icon --}}
                            <div class="mb-4">
                                <label for="icon" class="form-label">Ikon Kategori (Opsional)</label>
                                
                                {{-- Preview Icon Lama --}}
                                @if($genre->icon)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $genre->icon) }}" alt="Current Icon" class="img-thumbnail" style="height: 80px; width: 80px; object-fit: cover;">
                                        <div class="small text-muted mt-1">Icon saat ini</div>
                                    </div>
                                @endif

                                <input type="file" id="icon" name="icon" class="form-control @error('icon') is-invalid @enderror" accept="image/*">
                                <div class="form-text help-text">Biarkan kosong jika tidak ingin mengubah icon. (Format: JPG, PNG, SVG. Max 2MB)</div>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="bi bi-save me-1"></i> Update
                                </button>
                                <a href="{{ route('admin.petugas.genres.index') }}" class="btn btn-outline-secondary btn-lg">
                                    Kembali
                                </a>
                            </div>
                        </form>
                        
                        {{-- Form Hapus --}}
                        <form action="{{ route('admin.petugas.genres.destroy', $genre->id) }}" method="POST" id="delete-form" class="mt-4 border-top pt-4">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i> Hapus Genre Ini
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Kolom Sidebar (Kanan) --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-start border-danger border-4 mb-4">
                    <div class="card-body">
                        <h6 class="mb-2 fw-bold text-danger">Ringkasan Cepat</h6>
                        <p class="mb-1"><strong>Total Genre:</strong> {{ \App\Models\Genre::count() }}</p>
                        <p class="mb-0 text-muted small">Icon akan muncul di halaman katalog pengunjung.</p>
                    </div>
                </div>

                <div class="card shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="mb-3 fw-bold text-primary">Petunjuk</h6>
                        <ul class="list-unstyled mb-0 small text-muted">
                            <li class="mb-2 d-flex">
                                <i class="bi bi-check-circle-fill text-primary me-2 mt-1"></i>
                                <span>Nama genre wajib diisi dan unik.</span>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="bi bi-image text-primary me-2 mt-1"></i>
                                <span>Gunakan gambar transparan (PNG/SVG) agar lebih rapi.</span>
                            </li>
                            <li class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2 mt-1"></i>
                                <span>Gunakan tombol Hapus hanya jika tidak ada buku yang terhubung.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
          'use strict'
          var forms = document.querySelectorAll('.needs-validation')
          Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
              if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
              }
              form.classList.add('was-validated')
            }, false)
          })
        })();

        const deleteForm = document.getElementById('delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function (event) {
                event.preventDefault(); 
                
                Swal.fire({
                    title: 'Hapus Genre Ini?',
                    text: "Apakah Anda yakin ingin menghapus genre '{{ $genre->name }}'? Tindakan ini tidak dapat dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#c62828', 
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.submit(); 
                    }
                });
            });
        }
    </script>
</body>
</html>