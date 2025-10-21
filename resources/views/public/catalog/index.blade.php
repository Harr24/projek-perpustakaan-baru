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
        
        /* ========================================================== */
        /* PERBAIKAN CSS SLIDER DENGAN !IMPORTANT */
        /* ========================================================== */
        .hero-slider .carousel-item {
            height: 60vh !important;
            max-height: 580px !important;
            min-height: 400px !important; 
            background-color: #212529;
        }
        .hero-slider .carousel-item img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            object-position: center; 
            filter: brightness(0.6); 
        }
        .hero-slider .carousel-caption { 
            top: 50%; 
            transform: translateY(-50%); 
            bottom: auto; 
        }
        /* Style untuk HP tidak diubah */
        @media (max-width: 768px) {
            .hero-slider .carousel-item { 
                height: 50vh !important; 
                min-height: 300px !important; 
            }
            .hero-slider .carousel-caption h1 { font-size: 1.75rem; }
            .hero-slider .carousel-caption .lead { display: none; }
        }
        
        /* Styling lain */
        .navbar-brand { font-size: 1.25rem; }
        .book-card, .material-card { border: 1px solid #dee2e6; display: flex; flex-direction: column; transition: all .2s ease-in-out; border-radius: 8px; overflow: hidden; }
        .book-card:hover, .material-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .book-cover { height: 300px; object-fit: cover; width: 100%; }
        .card-body { flex-grow: 1; display: flex; flex-direction: column; }
        .card-footer { border-top: 1px dashed #e9ecef !important; }
        .carousel-control-prev-icon, .carousel-control-next-icon { background-color: rgba(0, 0, 0, 0.4); padding: 10px; border-radius: 50%; }
        .subject-card { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 1rem; border-radius: 12px; background-color: #fff; border: 1px solid #e9ecef; text-decoration: none; color: #212529; transition: all 0.2s ease-in-out; }
        .subject-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); border-color: var(--brand-red); }
        .subject-code { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #fce4ec, #f8bbd0); color: var(--brand-red); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; }
        .top-borrowers-section { border-top: 1px solid #dee2e6; }
        .borrower-card { border: none; transition: all 0.3s ease; border-radius: 12px; }
        .borrower-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #fce4ec, #f8bbd0); color: #c62828; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; }
        @media (max-width: 768px) { .book-cover { height: 250px; } }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('catalog.index') }}" style="color: var(--brand-red);"><i class="bi bi-book-half me-2"></i> Perpustakaan Multicomp</a>
            <div class="d-flex align-items-center">
                <a href="{{ route('catalog.all') }}" class="btn btn-sm btn-outline-secondary d-lg-none me-3"><i class="bi bi-grid-3x3-gap-fill"></i> Semua</a>
                <form action="{{ route('catalog.all') }}" method="GET" class="d-none d-lg-block me-3">
                    <div class="input-group input-group-sm">
                        <input type="search" name="search" class="form-control" placeholder="Cari buku..." value="{{ request('search') }}" aria-label="Cari buku cepat">
                        <button class="btn btn-outline-danger" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-grid-fill"></i> Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        {{-- Hero Slider --}}
        @if(isset($heroSliders) && $heroSliders->isNotEmpty())
        <div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-indicators">
                @foreach($heroSliders as $index => $slider)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($heroSliders as $index => $slider)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . $slider->image_path) }}" class="d-block" alt="{{ $slider->title }}">
                        <div class="carousel-caption text-center">
                            @if($slider->title)<h1 class="display-4 fw-bold mb-3">{{ $slider->title }}</h1>@endif
                            @if($slider->description)<p class="lead d-none d-md-block">{{ $slider->description }}</p>@endif
                            @if($slider->button_link)
                                <a href="{{ $slider->button_link }}" class="btn btn-danger btn-lg mt-3" target="_blank" rel="noopener noreferrer">
                                    {{ $slider->button_text ?? 'Lihat Selengkapnya' }} <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($heroSliders->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button>
            @endif
        </div>
        @endif

        {{-- Section 1: Kategori/Genre --}}
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold display-6">Jelajahi Berdasarkan Kategori</h2>
                <p class="lead text-muted">Temukan koleksi buku favorit Anda berdasarkan subjek.</p>
            </div>
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
                @forelse ($genres as $genre)
                    <div class="col">
                        <a href="{{ route('catalog.all', ['genre' => $genre->name]) }}" class="subject-card h-100">
                            <div class="subject-code">{{ strtoupper(substr($genre->name, 0, 2)) }}</div>
                            <div class="subject-name small fw-bolder text-truncate">{{ $genre->name }}</div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center"><p class="text-muted">Genre belum ditambahkan.</p></div>
                @endforelse
            </div>
        </div>

        {{-- Section 2: Buku Favorit --}}
        <div class="bg-white py-5 shadow-sm">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0 text-danger"><i class="bi bi-heart-fill me-2"></i> 10 Buku Favorit</h3>
                </div>
                @if($favoriteBooks->isNotEmpty())
                    <div id="favoriteBooksCarousel" class="carousel slide">
                        <div class="carousel-inner">
                            @foreach ($favoriteBooks->chunk(4) as $index => $chunk)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <div class="row row-cols-2 row-cols-md-4 g-4">
                                        @foreach ($chunk as $book)
                                            <div class="col">@include('public.catalog.partials._book_card', ['book' => $book])</div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#favoriteBooksCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button>
                        <button class="carousel-control-next" type="button" data-bs-target="#favoriteBooksCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button>
                    </div>
                @else
                    <div class="alert alert-info text-center">Belum ada data untuk menentukan buku favorit.</div>
                @endif
            </div>
        </div>
        
        {{-- Buku Terbaru --}}
        <div class="bg-white py-5 mt-5 shadow-sm">
             <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0"><i class="bi bi-arrow-down-up me-2"></i> 10 Buku Terbaru</h3>
                    <a href="{{ route('catalog.all', ['sort' => 'latest']) }}" class="btn btn-outline-danger btn-sm">Lihat Semua Buku <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
                    @forelse ($latestBooks as $book)
                        <div class="col">
                            @include('public.catalog.partials._book_card', ['book' => $book])
                        </div>
                    @empty
                        <div class="col-12 text-center"><div class="alert alert-warning">Belum ada buku baru.</div></div>
                    @endforelse
                </div>
                <div class="text-center mt-5">
                     <a href="{{ route('catalog.all') }}" class="btn btn-lg btn-danger shadow-lg"><i class="bi bi-grid-3x3-gap-fill me-2"></i> Lihat Semua Buku di Katalog</a>
                </div>
            </div>
        </div>

        {{-- Materi Belajar (Sekarang Publik) --}}
        {{-- Blok ini dipindahkan dari atas dan @auth-nya dihapus --}}
        @if(isset($learningMaterials) && $learningMaterials->isNotEmpty())
        <div class="container py-5 mt-5"> {{-- Ditambahkan mt-5 untuk jarak --}}
            <div class="text-center mb-5">
                <h2 class="fw-bold display-6">Materi Belajar Terbaru</h2>
                <p class="lead text-muted">Akses materi tambahan yang dibagikan oleh para guru.</p>
            </div>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                @foreach($learningMaterials as $material)
                <div class="col">
                    <a href="{{ $material->link_url }}" target="_blank" rel="noopener noreferrer" class="card h-100 material-card text-decoration-none text-dark">
                        <div class="card-body d-flex align-items-center">
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
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('catalog.materials.all') }}" class="btn btn-outline-danger"><i class="bi bi-collection-fill me-2"></i> Lihat Semua Materi Belajar</a>
            </div>
        </div>
        @endif

        {{-- Peminjam Teratas --}}
        <div class="top-borrowers-section mt-5 py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold display-6">Peminjam Teratas Bulan Ini</h2>
                    <p class="lead text-muted">Apresiasi bagi para penikmat koleksi kami.</p>
                </div>
                <div class="row g-4 justify-content-center">
                    @forelse ($topBorrowers as $borrower)
                        <div class="col-md-6 col-lg-4">
                            <div class="card text-center h-100 shadow-sm borrower-card bg-white">
                                <div class="card-body p-4">
                                    @if($borrower->user->profile_photo)
                                        <img src="{{ asset('storage/' . $borrower->user->profile_photo) }}" alt="{{ $borrower->user->name }}" class="avatar mx-auto mb-3" style="object-fit: cover;">
                                    @else
                                        <div class="avatar mx-auto mb-3">{{ strtoupper(substr($borrower->user->name, 0, 2)) }}</div>
                                    @endif
                                    <h5 class="card-title fw-bold text-danger">{{ $borrower->user->name }}</h5>
                                    <p class="text-muted mb-3 small">
                                        @if($borrower->user->role === 'siswa' && $borrower->user->class_name)
                                            {{ $borrower->user->class_name }}
                                        @elseif($borrower->user->role === 'guru')
                                            Guru
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
                        <div class="col-12"><p class="text-center text-muted">Belum ada data peminjaman di bulan ini.</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @include('layouts.footer')
</body>
</html>