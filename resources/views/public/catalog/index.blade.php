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
        .book-card { border: 1px solid #dee2e6; display: flex; flex-direction: column; transition: all .2s ease-in-out; }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .book-cover { height: 300px; object-fit: cover; }
        .card-body { flex-grow: 1; }
        .pagination .page-item.active .page-link { background-color: var(--brand-red); border-color: var(--brand-red); }
        .pagination .page-link { color: var(--brand-red); }
        .subject-card { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 1rem; border-radius: 12px; background-color: #fff; border: 1px solid #e9ecef; text-decoration: none; color: #212529; transition: all 0.2s ease-in-out; }
        .subject-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); border-color: var(--brand-red); }
        .subject-code { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #fce4ec, #f8bbd0); color: var(--brand-red); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; }
        .subject-name { font-weight: 600; text-align: center; }
        .top-borrowers-section { border-top: 1px solid #dee2e6; }
        .borrower-card { border: none; transition: all 0.3s ease; }
        .borrower-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #fce4ec, #f8bbd0); color: #c62828; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; }
    </style>
</head>
<body>
    {{-- =============================================== --}}
    {{-- BAGIAN HEADER YANG DIKEMBALIKAN                --}}
    {{-- =============================================== --}}
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

    <main>
        {{-- Bagian Genre --}}
        <div class="container my-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Pilih Subjek yang Menarik</h2>
                <p class="text-muted">Jelajahi koleksi kami berdasarkan kategori.</p>
            </div>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
                @forelse ($genres as $genre)
                    <div class="col">
                        <a href="{{ route('catalog.index', ['genre' => $genre->name]) }}" class="subject-card h-100">
                            <div class="subject-code">{{ $genre->genre_code }}</div>
                            <div class="subject-name">{{ $genre->name }}</div>
                        </a>
                    </div>
                @empty
                    <p class="text-center text-muted">Genre belum ditambahkan.</p>
                @endforelse
            </div>
        </div>

        {{-- Bagian Katalog Buku --}}
        <div class="container my-4">
            <div class="text-center mb-4">
                <h2 class="display-5 fw-bold">Katalog Buku</h2>
                <p class="lead text-muted">Temukan dan pinjam buku favorit Anda di sini.</p>
            </div>

            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <form action="{{ route('catalog.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul atau penulis..." value="{{ request('search') }}">
                            <button class="btn btn-danger" type="submit">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            @if(request('genre'))
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <span>Menampilkan buku untuk genre: <strong>{{ request('genre') }}</strong></span>
                    <a href="{{ route('catalog.index') }}" class="btn-close" aria-label="Close"></a>
                </div>
            @endif

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                @forelse ($books as $book)
                    <div class="col">
                        <div class="card h-100 book-card">
                            <img src="{{ $book->cover_image ? route('book.cover', $book) : 'https://via.placeholder.com/300x400.png?text=No+Cover' }}" 
                                 class="card-img-top book-cover" alt="Sampul {{ $book->title }}">
                            <div class="card-body">
                                <h6 class="card-title fw-bold text-truncate" title="{{ $book->title }}">{{ $book->title }}</h6>
                                <p class="card-text small text-muted mb-0">{{ $book->author }}</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 p-3">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('catalog.show', $book) }}" class="btn btn-sm btn-outline-secondary">Lihat Detail</a>
                                    @auth
                                        @if($book->copies->isNotEmpty())
                                            <a href="{{ route('borrow.create', $book->copies->first()) }}" class="btn btn-sm btn-danger w-100">Ajukan Pinjaman</a>
                                        @else
                                            <button class="btn btn-sm btn-secondary w-100" disabled>Stok Habis</button>
                                        @endif
                                    @endauth
                                    @guest
                                        <a href="{{ route('login') }}" class="btn btn-sm btn-danger w-100">Login untuk Pinjam</a>
                                    @endguest
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            @if(request('search'))
                                Buku dengan kata kunci "{{ request('search') }}" tidak ditemukan.
                            @elseif(request('genre'))
                                Tidak ada buku untuk genre "{{ request('genre') }}".
                            @else
                                Belum ada buku di dalam katalog.
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $books->appends(request()->query())->links() }}
            </div>
        </div>

        {{-- Bagian Peminjam Teratas --}}
        <div class="top-borrowers-section mt-5 py-5 bg-white">
            <div class="container">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Peminjam Teratas Bulan Ini</h2>
                    <p class="lead text-muted">Apresiasi bagi para penikmat koleksi kami. Jadilah salah satunya!</p>
                </div>
                <div class="row g-4 justify-content-center">
                    @forelse ($topBorrowers as $borrower)
                        <div class="col-md-6 col-lg-4">
                            <div class="card text-center h-100 shadow-sm borrower-card">
                                <div class="card-body">
                                    <div class="avatar mx-auto mb-3">{{ strtoupper(substr($borrower->user->name, 0, 2)) }}</div>
                                    <h5 class="card-title fw-bold">{{ $borrower->user->name }}</h5>
                                    <p class="text-muted mb-3">
                                        @if($borrower->user->class && $borrower->user->major)
                                            Siswa Kelas {{ $borrower->user->class }} {{ $borrower->user->major }}
                                        @else
                                            Anggota Perpustakaan
                                        @endif
                                    </p>
                                    <div class="stats d-flex justify-content-center gap-4">
                                        <div>
                                            <div class="fw-bold fs-5">{{ $borrower->loans_count }}</div>
                                            <div class="small text-muted">Peminjaman</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted">Belum ada data peminjaman di bulan ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>