<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Semua Katalog Buku - Perpustakaan Multicomp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --brand-red: #c62828; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .book-card { border: 1px solid #dee2e6; display: flex; flex-direction: column; transition: all .2s ease-in-out; border-radius: 8px; overflow: hidden; }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .book-cover { height: 300px; object-fit: cover; width: 100%; }
        .card-body { flex-grow: 1; display: flex; flex-direction: column; }
        .pagination .page-item.active .page-link { background-color: var(--brand-red); border-color: var(--brand-red); }
        .pagination .page-link { color: var(--brand-red); }
        .header-section { background-color: #fff; padding: 2rem 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('catalog.index') }}" style="color: var(--brand-red);">
                <i class="bi bi-book-half me-2"></i> Perpustakaan Multicomp
            </a>
            <div class="d-flex align-items-center">
                <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="bi bi-house-door-fill"></i> Kembali ke Beranda
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-danger">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-danger">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        <div class="container header-section mb-5">
            <h1 class="display-5 fw-bold text-center">Seluruh Katalog Buku</h1>
            <p class="lead text-muted text-center">Jelajahi, cari, dan saring semua koleksi buku kami.</p>
        </div>

        <div class="container my-4">
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <form action="{{ route('catalog.all') }}" method="GET">
                        <div class="input-group input-group-lg shadow-sm">
                            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul, penulis..." value="{{ request('search') }}" aria-label="Kolom pencarian katalog">
                            <button class="btn btn-danger" type="submit"><i class="bi bi-search"></i> Cari</button>
                        </div>
                    </form>
                </div>
            </div>
            
            @if(request('search'))
                <div class="alert alert-info d-flex justify-content-between align-items-center rounded-3 shadow-sm my-4 p-3">
                    <span class="fw-medium">
                        <i class="bi bi-funnel-fill me-2"></i> Filter Aktif: 
                        Pencarian: <span class="badge bg-danger">"{{ request('search') }}"</span>
                    </span>
                    <a href="{{ route('catalog.all') }}" class="btn btn-outline-danger btn-sm">
                        Hapus Filter &times;
                    </a>
                </div>
            @endif

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                {{-- ========================================================== --}}
                {{-- BAGIAN INI DITULIS ULANG UNTUK MEMASTIKAN SINTAKS BENAR --}}
                {{-- ========================================================== --}}
                @forelse ($books as $book)
                    <div class="col">
                        @include('public.catalog.partials._book_card', ['book' => $book])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center mt-3 p-4 shadow-sm">
                            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Maaf, Buku Tidak Ditemukan.</h4>
                            <p class="mb-0">
                                Buku dengan kata kunci **"{{ request('search') }}"** tidak ditemukan. Coba kata kunci lain.
                            </p>
                        </div>
                    </div>
                @endforelse
                {{-- ========================================================== --}}
                {{-- AKHIR DARI BAGIAN YANG DIPERBAIKI --}}
                {{-- ========================================================== --}}
            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $books->appends(request()->query())->links() }} 
            </div>
        </div>
    </main>
    
    <footer class="text-center py-4 text-muted small mt-5">
        <div class="container">
            &copy; {{ date('Y') }} Perpustakaan Multicomp. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





