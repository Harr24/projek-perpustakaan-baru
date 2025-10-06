<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Buku - Perpustakaan Multicomp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --brand-red: #c62828; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .book-card {
            border: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
            transition: box-shadow .2s ease-in-out, transform .2s ease-in-out;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .book-cover { height: 300px; object-fit: cover; }
        .card-body { flex-grow: 1; }
        .pagination .page-item.active .page-link {
            background-color: var(--brand-red);
            border-color: var(--brand-red);
        }
        .pagination .page-link { color: var(--brand-red); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('catalog.index') }}" style="color: var(--brand-red);">Perpustakaan Multicomp</a>
            <div>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-danger">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <div class="text-center mb-4">
            <h1 class="display-5 fw-bold">Katalog Buku</h1>
            <p class="lead text-muted">Temukan dan pinjam buku favorit Anda di sini.</p>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse ($books as $book)
                <div class="col">
                    <div class="card h-100 book-card">
                        
                        {{-- ========================================================== --}}
                        {{-- PERBAIKAN: Menggunakan Storage::url() untuk menampilkan gambar --}}
                        {{-- ========================================================== --}}
                        <img src="{{ $book->cover_image ? Storage::url($book->cover_image) : 'https://via.placeholder.com/300x400.png?text=No+Cover' }}" 
                             class="card-img-top book-cover" alt="Sampul {{ $book->title }}">
                        
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-truncate" title="{{ $book->title }}">{{ $book->title }}</h6>
                            <p class="card-text small text-muted mb-0">{{ $book->author }}</p>
                        </div>
                        <div class="card-footer bg-white border-top-0 p-3">
                            <div class="d-grid gap-2">
                                <a href="{{ route('catalog.show', $book) }}" class="btn btn-sm btn-outline-secondary">Lihat Detail</a>
                                @guest
                                    <a href="{{ route('login') }}" class="btn btn-sm btn-danger w-100">Login untuk Pinjam</a>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center">Belum ada buku di dalam katalog.</div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $books->links() }}
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>