<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Buku - Perpustakaan Multicomp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root { 
            --brand-red: #c62828; 
            --brand-red-hover: #b71c1c;
        }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8f9fa; 
            overflow-x: hidden;
        }
        
        /* ========================================================== */
        /* NAVBAR DENGAN ANIMASI SMOOTH */
        /* ========================================================== */
        .navbar {
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-size: 1.25rem;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .navbar-brand img {
            height: 50px !important; /* Diperbesar dari 35px */
            transition: all 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .navbar-brand img {
                height: 40px !important;
            }
        }
        
        /* Button animations */
        .btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* ========================================================== */
        /* HERO SLIDER (VERSI BARU - Teks di Kiri) */
        /* ========================================================== */
        .hero-slider .carousel-item {
            height: 60vh !important;
            max-height: 580px !important;
            min-height: 400px !important; 
            position: relative;
            /* Menggunakan background properties, bukan <img> */
            background-size: cover;
            background-position: center;
            animation: kenBurns 20s ease infinite;
        }

        /* Hapus animasi Ken Burns dari <img> karena <img> akan dihapus */
        .hero-slider .carousel-item img { 
           display: none; /* Sembunyikan img tag lama */
        }
        
        @keyframes kenBurns {
            0% { background-position: center 45%; }
            50% { background-position: center 55%; } /* Efek 'pan' pelan */
            100% { background-position: center 45%; }
        }

        .hero-slider .carousel-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* Gradient dari kiri ke kanan agar teks terbaca */
            background: linear-gradient(to right, rgba(0,0,0,0.65) 30%, rgba(0,0,0,0.1) 70%);
            z-index: 1;
        }

        /* Mengubah posisi caption ke kiri */
        .hero-slider .carousel-caption { 
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            bottom: auto;
            left: 10%; /* Posisi dari kiri */
            right: auto; /* Matikan posisi kanan */
            width: 50%; /* Batasi lebar teks */
            text-align: left; /* Teks rata kiri */
            z-index: 2;
            padding-top: 0;
            padding-bottom: 0;
        }

        .hero-slider .carousel-caption h1 {
            animation: fadeInUp 1s ease;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
            font-size: 2.75rem; /* Sedikit lebih besar */
            font-weight: 700;
        }

        .hero-slider .carousel-caption p {
            animation: fadeInUp 1.2s ease;
            font-size: 1.1rem;
        }

        .hero-slider .carousel-caption .btn {
            animation: fadeInUp 1.4s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) { 
            .hero-slider .carousel-item { 
                height: 50vh !important; 
                min-height: 300px !important; 
            }
            .hero-slider .carousel-caption {
                left: 8%;
                width: 80%;
            }
            .hero-slider .carousel-caption h1 { font-size: 1.75rem; }
            .hero-slider .carousel-caption p { display: none; } /* Sembunyikan deskripsi di HP */
        }
        
        .carousel-control-prev-icon, .carousel-control-next-icon { 
            background-color: rgba(198, 40, 40, 0.8);
            padding: 15px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .carousel-control-prev-icon:hover, .carousel-control-next-icon:hover {
            background-color: rgba(198, 40, 40, 1);
            transform: scale(1.1);
        }
        
        /* ========================================================== */
        /* SEARCH BAR DENGAN ANIMASI */
        /* ========================================================== */
        .search-container {
            position: relative;
        }
        
        .search-container .input-group {
            transition: all 0.3s ease;
        }
        
        .search-container .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(198, 40, 40, 0.25);
            border-color: var(--brand-red);
        }
        
        /* ========================================================== */
        /* SUBJECT CARDS DENGAN ANIMASI LEBIH MENARIK */
        /* ========================================================== */
        .subject-card { 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            padding: 1.5rem 1rem; 
            border-radius: 12px; 
            background-color: #fff; 
            border: 2px solid #e9ecef; 
            text-decoration: none; 
            color: #212529; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        .subject-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(198, 40, 40, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .subject-card:hover::before {
            left: 100%;
        }
        
        .subject-card:hover { 
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 12px 30px rgba(198, 40, 40, 0.2);
            border-color: var(--brand-red);
        }
        
        .subject-code { 
            width: 60px; 
            height: 60px; 
            border-radius: 50%; 
            background: linear-gradient(135deg, #fce4ec, #f8bbd0); 
            color: var(--brand-red); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem; 
            font-weight: 700; 
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(198, 40, 40, 0.2);
        }
        
        .subject-card:hover .subject-code {
            transform: rotate(360deg) scale(1.1);
            background: linear-gradient(135deg, var(--brand-red), #e53935);
            color: white;
        }
        
        .subject-name {
            transition: all 0.3s ease;
        }
        
        .subject-card:hover .subject-name {
            color: var(--brand-red);
            transform: scale(1.05);
        }
        
        /* ========================================================== */
        /* BOOK CARDS DENGAN ANIMASI */
        /* ========================================================== */
        .book-card, .material-card { 
            border: 1px solid #dee2e6; 
            display: flex; 
            flex-direction: column; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 12px; 
            overflow: hidden;
            position: relative;
        }
        
        .book-card::before, .material-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(198, 40, 40, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        
        .book-card:hover::before, .material-card:hover::before {
            opacity: 1;
        }
        
        .book-card:hover, .material-card:hover { 
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            border-color: var(--brand-red);
        }
        
        .book-cover { 
            height: 300px; 
            object-fit: cover; 
            width: 100%;
            transition: transform 0.5s ease;
        }
        
        .book-card:hover .book-cover {
            transform: scale(1.1);
        }
        
        .card-body { 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column;
            position: relative;
            z-index: 1;
        }
        
        .card-footer { 
            border-top: 1px dashed #e9ecef !important;
            transition: background-color 0.3s ease;
        }
        
        .book-card:hover .card-footer {
            background-color: #fff5f5;
        }
        
        /* ========================================================== */
        /* BORROWER CARDS DENGAN ANIMASI */
        /* ========================================================== */
        .borrower-card { 
            border: none; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 16px;
            position: relative;
            overflow: hidden;
        }
        
        .borrower-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--brand-red), #e53935);
            transform: scaleX(0);
            transition: transform 0.5s ease;
        }
        
        .borrower-card:hover::before {
            transform: scaleX(1);
        }
        
        .borrower-card:hover { 
            transform: translateY(-10px) rotate(-1deg);
            box-shadow: 0 20px 40px rgba(198, 40, 40, 0.2) !important;
        }
        
        .avatar { 
            width: 80px; 
            height: 80px; 
            border-radius: 50%; 
            background: linear-gradient(135deg, #fce4ec, #f8bbd0); 
            color: #c62828; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 2rem; 
            font-weight: 600;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(198, 40, 40, 0.3);
        }
        
        .borrower-card:hover .avatar {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 20px rgba(198, 40, 40, 0.4);
        }
        
        /* ========================================================== */
        /* SECTION HEADERS DENGAN ANIMASI */
        /* ========================================================== */
        .display-6 {
            position: relative;
            display: inline-block;
        }
        
        .display-6::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--brand-red), #e53935);
            transition: width 0.5s ease;
        }
        
        [data-aos].aos-animate .display-6::after {
            width: 100px;
        }
        
        /* ========================================================== */
        /* MATERIAL CARDS */
        /* ========================================================== */
        .material-card .card-body {
            transition: all 0.3s ease;
        }
        
        .material-card:hover .card-body {
            background-color: #fff8f8;
        }
        
        .material-card i {
            transition: transform 0.3s ease;
        }
        
        .material-card:hover i {
            transform: rotate(-10deg) scale(1.1);
        }
        
        /* ========================================================== */
        /* RESPONSIVE */
        /* ========================================================== */
        @media (max-width: 768px) { 
            .book-cover { height: 250px; }
            .navbar-brand img { height: 40px !important; }
        }
        
        /* ========================================================== */
        /* FLOATING ANIMATION */
        /* ========================================================== */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .btn-lg {
            animation: float 3s ease-in-out infinite;
        }
        
        /* ========================================================== */
        /* SMOOTH SCROLL */
        /* ========================================================== */
        html {
            scroll-behavior: smooth;
        }

        /* ========================================================== */
        /* CSS BARU UNTUK INFO KONTAK (DARI INSPIRASI) */
        /* ========================================================== */
        .info-block {
            display: flex;
            align-items: flex-start; /* Ikon di atas */
            gap: 1.25rem; /* Jarak antara ikon dan teks */
        }
        
        .info-icon {
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            border-radius: 12px; /* Rounded square, looks modern */
            background-color: #fff5f5; /* Warna merah muda/pink */
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-red);
            font-size: 1.75rem; /* Ukuran ikon */
            box-shadow: 0 4px 10px rgba(198, 40, 40, 0.1);
            transition: all 0.3s ease;
        }

        .info-block:hover .info-icon {
            transform: scale(1.1);
            background-color: var(--brand-red);
            color: #fff;
        }
        
        .info-text {
            flex-grow: 1;
        }
        
        .info-title {
            font-size: 1.25rem; /* Ukuran heading (Alamat, Email, etc.) */
            font-weight: 700;
            color: var(--brand-red);
            margin-top: 0;
            margin-bottom: 0.25rem;
        }
        
        .info-text p {
            font-size: 1rem; /* Ukuran teks info */
            line-height: 1.6;
            color: #333; /* Warna teks lebih gelap agar mudah dibaca */
            margin-bottom: 0;
        }
        /* ========================================================== */
        /* AKHIR CSS BARU */
        /* ========================================================== */
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('catalog.index') }}">
                <img src="{{ asset('images/MCP.jpg') }}" alt="Logo Perpustakaan Multicomp">
            </a>

            <div class="d-flex align-items-center">
                <a href="{{ route('catalog.all') }}" class="btn btn-sm btn-outline-secondary d-lg-none me-3"><i class="bi bi-grid-3x3-gap-fill"></i> Semua</a>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-grid-fill"></i> Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-sm btn-outline-danger me-2"><i class="bi bi-person-plus-fill"></i> Mendaftar</a>
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
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" 
                         style="background-image: url('{{ asset('storage/' . $slider->image_path) }}');">
                        
                        {{-- Konten Teks di Kiri --}}
                        <div class="carousel-caption"> {{-- Hapus class text-center --}}
                            
                            @if($slider->title)
                                <h1 class="display-4 fw-bold mb-3">{{ $slider->title }}</h1>
                            @endif

                            @if($slider->description)
                                <p class="lead d-none d-md-block">{{ $slider->description }}</p>
                            @endif

                            {{-- Link tombol diperbaiki --}}
                            @if($slider->link_url)
                                <a href="{{ $slider->link_url }}" class="btn btn-danger btn-lg mt-3" target="_blank" rel="noopener noreferrer">
                                    BACA SELENGKAPNYA <i class="bi bi-arrow-right ms-2"></i>
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

        {{-- Section Baru: Tentang Kami (Intro) --}}
        <div class="bg-white py-5 shadow-sm">
            <div class="container">
                <div class="row g-5 align-items-center">
        
                    <div class="col-lg-5" data-aos="fade-right">
                        <img src="{{ asset('images/orangsekolah.png') }}" 
                             alt="Ilustrasi siswi membaca buku" 
                             class="img-fluid rounded-3 shadow-lg" 
                             style="width: 100%; height: auto; max-height: 450px; object-fit: cover;">
                    </div>
        
                    <div class="col-lg-7" data-aos="fade-left" data-aos-delay="100">
                        <h2 class="fw-bold display-6" style="color: var(--brand-red);">
                            Apa itu MyMulticompLibrary?
                        </h2>
                        <p class="lead text-muted mt-3">
                            Ini adalah web perpustakaan digital resmi SMK Multicomp Depok.
                        </p>
                        <p style="font-size: 1.1rem; line-height: 1.7;">
                            Kami hadir untuk membawa perpustakaan ke dalam genggaman Anda. Di era digital ini, <strong>pentingnya membaca</strong> menjadi semakin krusial untuk membuka wawasan. MyMulticompLibrary menyediakan akses mudah ke ribuan koleksi buku, materi pelajaran, dan sumber ilmu pengetahuan lainnya.
                        </p>
                        <a href="#search-section" class="btn btn-danger btn-lg mt-3">
                            <i class="bi bi-search me-2"></i> Mulai Cari Buku
                        </a>
                    </div>
        
                </div>
            </div>
        </div>
        {{-- Section 1: Kategori/Genre --}}
        <div class="container py-5" id="search-section"> <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold display-6">Jelajahi Berdasarkan Kategori</h2>
                <p class="lead text-muted">Temukan koleksi buku favorit Anda berdasarkan subjek.</p>
            </div>

            <div class="row justify-content-center mb-5" data-aos="fade-up" data-aos-delay="100">
                <div class="col-md-8">
                    <div class="search-container">
                        <form action="{{ route('catalog.all') }}" method="GET">
                            <div class="input-group input-group-lg shadow-sm">
                                <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul, penulis..." value="{{ request('search') }}" aria-label="Kolom pencarian katalog">
                                <button class="btn btn-danger" type="submit"><i class="bi bi-search"></i> Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3" data-aos="fade-up" data-aos-delay="200">
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
        <div class="bg-white py-5 shadow-sm" data-aos="fade-up">
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
        <div class="bg-white py-5 mt-5 shadow-sm" data-aos="fade-up">
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
        @if(isset($learningMaterials) && $learningMaterials->isNotEmpty())
        <div class="container py-5 mt-5" data-aos="fade-up">
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
        <div class="top-borrowers-section mt-5 py-5" data-aos="fade-up">
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

        {{-- Section 4: Tentang Kami (Kontak & Peta) --}}
        <div class="bg-white py-5 mt-5 shadow-sm">
            <div class="container">
                
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="fw-bold display-6">TENTANG KAMI</h2>
                </div>

                <div class="row" data-aos="fade-up" data-aos-delay="100">

                    <div class="col-lg-6 mb-4 mb-lg-0">
                        
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.691893162832!2d106.81866667499214!3d-6.433608393557545!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ea2635732523%3A0x20126f5d1f77c9b0!2sSMK%20Multicomp%20Depok!5e0!3m2!1sid!2sid!4v1762216376908!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" 
                            style="border:0; width: 100%; height: 100%; min-height: 450px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                        
                    </div>

                    <div class="col-lg-6">
                        
                        <div class="info-block d-flex align-items-start mb-4">
                            <div class="info-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="info-text">
                                <h4 class="info-title">Alamat</h4>
                                <p>Jl. Raya Kalimulya, Kp. Kebun Duren, Pd. Rajeg, No.7, Kel. Kalimulya, Kec. Cilodong, Depok, Jawa Barat, Indonesia.
                                <br>Kode Pos: 16413</p>
                            </div>
                        </div>

                        <div class="info-block d-flex align-items-start mb-4">
                            <div class="info-icon">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div class="info-text">
                                <h4 class="info-title">Email</h4>
                                <p>-</p>
                            </div>
                        </div>

                        <div class="info-block d-flex align-items-start mb-4">
                            <div class="info-icon">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div class="info-text">
                                <h4 class="info-title">Telepon</h4>
                                <p>-</p>
                            </div>
                        </div>
                        
                        <div class="info-block d-flex align-items-start mb-4">
                            <div class="info-icon">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <div class="info-text">
                                <h4 class="info-title">Jam Buka Perpustakaan</h4>
                                <p>Senin - Jumat = 08:00-14:00 WIB
                            </div>
                        </div>

                    </div>
                </div>

                <h4 class="text-center fw-bold mt-5 text-muted" data-aos="fade-up" data-aos-delay="200" style="font-size: 1.1rem; letter-spacing: 1px;">
                    UPT PERPUSTAKAAN MULTICOMP
                </h4>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>

    @include('layouts.footer')
</body>
</html>