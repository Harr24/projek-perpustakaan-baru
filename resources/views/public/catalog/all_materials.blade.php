<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Materi Belajar - Perpustakaan Multicomp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --brand-red: #c62828; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .material-card {
            border: 1px solid #dee2e6;
            transition: all .2s ease-in-out;
            border-radius: 8px;
            overflow: hidden;
            text-decoration: none;
            color: #212529;
            background-color: #fff; /* Tambahkan background putih */
        }
        .material-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        /* Style untuk pagination agar sesuai tema */
        .pagination .page-item.active .page-link {
            background-color: var(--brand-red);
            border-color: var(--brand-red);
            color: white; /* Warna teks putih */
        }
        .pagination .page-link {
            color: var(--brand-red);
             box-shadow: none !important; /* Hilangkan shadow focus bootstrap */
        }
         .pagination .page-link:hover {
             background-color: #f8d7da; /* Warna hover lebih lembut */
             border-color: #f5c2c7;
         }
         .pagination .page-item.disabled .page-link {
             color: #6c757d;
             background-color: #e9ecef;
             border-color: #dee2e6;
         }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold me-auto" href="{{ route('catalog.index') }}" style="color: var(--brand-red);">
                <i class="bi bi-book-half me-2"></i> Perpustakaan Multicomp
            </a>

            <div class="d-flex align-items-center">
                <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-outline-secondary d-none d-md-flex align-items-center me-2">
                    <i class="bi bi-house-door-fill me-1"></i>
                    <span class="d-none d-lg-inline">Beranda</span>
                </a>
                 <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-outline-secondary d-md-none me-2" aria-label="Kembali ke Beranda">
                    <i class="bi bi-house-door-fill"></i>
                </a>
                {{-- Login/Dashboard Button --}}
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-danger" title="Dashboard">
                         <i class="bi bi-grid-fill d-inline d-sm-none"></i>
                         <span class="d-none d-sm-inline">Dashboard</span>
                    </a>
                @else
                     <a href="{{ route('login') }}" class="btn btn-sm btn-danger" title="Login">
                         <i class="bi bi-box-arrow-in-right d-inline d-sm-none"></i>
                         <span class="d-none d-sm-inline">Login</span>
                     </a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold display-5">Materi Belajar</h1>
            <p class="lead text-muted">Akses semua materi tambahan yang dibagikan oleh para guru.</p>
        </div>

        {{-- FORM PENCARIAN DAN FILTER --}}
        <div class="card card-body mb-5 shadow-sm border-0">
            <form action="{{ route('catalog.materials.all') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Cari Judul Materi</label>
                    <input type="search" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Contoh: Sejarah Kemerdekaan">
                </div>
                <div class="col-md-4">
                    <label for="teacher" class="form-label fw-semibold">Filter Berdasarkan Guru</label>
                    <select name="teacher" id="teacher" class="form-select">
                        <option value="">Semua Guru</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @forelse($materials as $material)
                <div class="col">
                    <a href="{{ $material->link_url }}" target="_blank" rel="noopener noreferrer" class="card h-100 material-card">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="pe-3">
                                <div class="d-flex align-items-center justify-content-center bg-danger text-white rounded-circle" style="width: 50px; height: 50px;">
                                    <i class="bi bi-link-45deg fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title fw-bold mb-1">{{ $material->title }}</h5>
                                <p class="card-text text-muted small mb-2">{{ Str::limit($material->description, 100) }}</p>
                                <p class="card-text mb-0"><small class="text-muted">Oleh: {{ $material->user->name }}</small></p>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <h5 class="alert-heading">Materi Tidak Ditemukan</h5>
                        <p class="mb-0">Tidak ada materi yang cocok dengan kriteria pencarian Anda. Coba kata kunci atau filter yang berbeda.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- ========================================================== --}}
        {{-- PENAMBAHAN: Tampilkan Link Paginasi --}}
        {{-- ========================================================== --}}
        @if($materials->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{-- Ini akan merender link pagination Bootstrap secara otomatis --}}
            {{ $materials->links() }}
        </div>
        @endif
        {{-- ========================================================== --}}

    </main>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>