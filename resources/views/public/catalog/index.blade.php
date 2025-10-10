<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Buku - Perpustakaan Multicomp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --brand-red: #c62828; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        
        /* Navbar Styling */
        .navbar-brand { font-size: 1.25rem; }
        
        /* Hero Slider */
        .hero-slider { margin-top: 0; }
        .hero-slide { 
            height: 500px; 
            background-size: cover; 
            background-position: center; 
            position: relative;
        }
        .hero-slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.6));
        }
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .hero-slide .btn {
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        @media (max-width: 768px) {
            .hero-slide { height: 350px; }
            .hero-content h1 { font-size: 1.75rem; }
            .hero-content p { font-size: 0.9rem; }
        }
        
        /* Book Card */
        .book-card { border: 1px solid #dee2e6; display: flex; flex-direction: column; transition: all .2s ease-in-out; border-radius: 8px; overflow: hidden; }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .book-cover { height: 300px; object-fit: cover; width: 100%; }
        .card-body { flex-grow: 1; display: flex; flex-direction: column; }
        .card-footer { border-top: 1px dashed #e9ecef !important; }

        /* Carousel overrides */
        .carousel-control-prev-icon, .carousel-control-next-icon { background-color: rgba(0, 0, 0, 0.4); padding: 10px; border-radius: 50%; }

        /* Genre Card */
        .subject-card { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 1rem; border-radius: 12px; background-color: #fff; border: 1px solid #e9ecef; text-decoration: none; color: #212529; transition: all 0.2s ease-in-out; }
        .subject-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); border-color: var(--brand-red); }
        .subject-code { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #fce4ec, #f8bbd0); color: var(--brand-red); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; }
        
        /* Top Borrowers */
        .top-borrowers-section { border-top: 1px solid #dee2e6; }
        .borrower-card { border: none; transition: all 0.3s ease; border-radius: 12px; }
        .borrower-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #fce4ec, #f8bbd0); color: #c62828; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; }

        /* Responsive adjustments for cover image height */
        @media (max-width: 768px) {
            .book-cover { height: 250px; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('catalog.index') }}" style="color: var(--brand-red);">
                <i class="bi bi-book-half me-2"></i> Perpustakaan Multicomp
            </a>
            <div class="d-flex align-items-center">
                {{-- Tombol Lihat Semua Buku di Navbar untuk layar kecil --}}
                <a href="{{ route('catalog.all') }}" class="btn btn-sm btn-outline-secondary d-lg-none me-3">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Semua
                </a>

                {{-- Form Pencarian Cepat di Navbar (Hanya untuk layar besar) --}}
                <form action="{{ route('catalog.all') }}" method="GET" class="d-none d-lg-block me-3">
                    <div class="input-group input-group-sm">
                        <input type="search" name="search" class="form-control" placeholder="Cari buku..." value="{{ request('search') }}" aria-label="Cari buku cepat">
                        <button class="btn btn-outline-danger" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-danger">
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
        {{-- =============================================== --}}
        {{-- SECTION 0: Hero Slider --}}
        {{-- =============================================== --}}
        @if(isset($sliders) && $sliders->isNotEmpty())
        <div class="hero-slider">
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-indicators">
                    @foreach($sliders as $index => $slider)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" 
                                aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                
                <div class="carousel-inner">
                    @foreach($sliders as $index => $slider)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="hero-slide d-flex align-items-center" 
                                 style="background-image: url('{{ $slider->image_path ? asset('storage/' . $slider->image_path) : 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=1920&h=500&fit=crop' }}');">
                                <div class="container">
                                    <div class="hero-content">
                                        @if($slider->title)
                                            <h1 class="display-4 fw-bold mb-3">{{ $slider->title }}</h1>
                                        @endif
                                        @if($slider->link_url)
                                            <a href="{{ $slider->link_url }}" class="btn btn-danger btn-lg">
                                                Lihat Selengkapnya <i class="bi bi-arrow-right ms-2"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($sliders->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        </div>
        @endif

        {{-- =============================================== --}}
        {{-- SECTION 1: Kategori/Genre --}}
        {{-- =============================================== --}}
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold display-6">Jelajahi Berdasarkan Kategori</h2>
                <p class="lead text-muted">Temukan koleksi buku favorit Anda berdasarkan subjek.</p>
            </div>
            
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                @forelse ($genres as $genre)
                    <div class="col">
                        <a href="{{ route('catalog.all', ['genre' => $genre->name]) }}" class="subject-card h-100">
                            {{-- Menggunakan kode genre 2 digit pertama sebagai pengganti ikon --}}
                            <div class="subject-code">{{ strtoupper(substr($genre->name, 0, 2)) }}</div>
                            <div class="subject-name small fw-bolder text-truncate">{{ $genre->name }}</div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">Genre belum ditambahkan ke sistem.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- =============================================== --}}
        {{-- SECTION 2: Buku Favorit (Carousel) --}}
        {{-- =============================================== --}}
        <div class="bg-white py-5 mb-5 shadow-sm">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0 text-danger"><i class="bi bi-heart-fill me-2"></i> 10 Buku Favorit</h3>
                </div>

                @if($favoriteBooks->isNotEmpty())
                    <div id="favoriteBooksCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach ($favoriteBooks->chunk(4) as $index => $chunk)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <div class="row row-cols-2 row-cols-md-4 g-4">
                                        @foreach ($chunk as $book)
                                            <div class="col">
                                                @include('public.catalog.partials._book_card', ['book' => $book])
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#favoriteBooksCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#favoriteBooksCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                @else
                    <div class="alert alert-info text-center">Belum ada data peminjaman untuk menentukan buku favorit.</div>
                @endif
            </div>
        </div>

        {{-- =============================================== --}}
        {{-- SECTION 3: Buku Terbaru (Grid) --}}
        {{-- =============================================== --}}
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0"><i class="bi bi-arrow-down-up me-2"></i> 10 Buku Terbaru</h3>
                <a href="{{ route('catalog.all', ['sort' => 'latest']) }}" class="btn btn-outline-danger btn-sm">
                    Lihat Semua Buku <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
                @forelse ($latestBooks as $book)
                    <div class="col">
                        @include('public.catalog.partials._book_card', ['book' => $book])
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <div class="alert alert-warning">Belum ada buku baru yang ditambahkan ke koleksi.</div>
                    </div>
                @endforelse
            </div>
            
            <div class="text-center mt-5">
                 <a href="{{ route('catalog.all') }}" class="btn btn-lg btn-danger shadow-lg">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i> Lihat Semua Buku di Katalog
                </a>
            </div>
        </div>

        {{-- =============================================== --}}
        {{-- SECTION 4: Peminjam Teratas --}}
        {{-- =============================================== --}}
        <div class="top-borrowers-section mt-5 py-5 bg-white shadow-lg">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold display-6">Peminjam Teratas Bulan Ini</h2>
                    <p class="lead text-muted">Apresiasi bagi para penikmat koleksi kami. Jadilah salah satunya!</p>
                </div>
                <div class="row g-4 justify-content-center">
                    @forelse ($topBorrowers as $borrower)
                        <div class="col-md-6 col-lg-4">
                            <div class="card text-center h-100 shadow-sm borrower-card">
                                <div class="card-body p-4">
                                    <div class="avatar mx-auto mb-3">{{ strtoupper(substr($borrower->user->name, 0, 2)) }}</div>
                                    <h5 class="card-title fw-bold text-danger">{{ $borrower->user->name }}</h5>
                                    <p class="text-muted mb-3 small">
                                        @if(isset($borrower->user->class) && isset($borrower->user->major))
                                            Siswa Kelas {{ $borrower->user->class }} {{ $borrower->user->major }}
                                        @else
                                            Anggota Perpustakaan
                                        @endif
                                    </p>
                                    <div class="stats d-flex justify-content-center gap-4 border-top pt-3">
                                        <div>
                                            <div class="fw-bolder fs-4 text-primary">{{ $borrower->loans_count }}</div>
                                            <div class="small text-muted">Buku Dipinjam</div>
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

    {{-- Footer Sederhana --}}
    <footer class="text-center py-4 text-muted small mt-5">
        <div class="container">
            &copy; {{ date('Y') }} Perpustakaan Multicomp. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>