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

        .genre-filter-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            border-radius: 12px;
            background-color: #fff;
            border: 1px solid #e9ecef;
            text-decoration: none;
            color: #212529;
            transition: all 0.2s ease-in-out;
            text-align: center;
        }
        .genre-filter-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }
        .genre-filter-item.active {
            border-color: var(--brand-red);
            background-color: #fef2f2;
            box-shadow: 0 0 0 3px rgba(198, 40, 40, 0.1);
        }
        
        /* LOGIKA TAMPILAN ICON DI FILTER */
        .genre-code {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            color: var(--brand-red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            overflow: hidden; /* Tambahan agar gambar bulat */
            position: relative;
        }
        
        /* Tambahan style untuk gambar di dalam lingkaran filter */
        .genre-code img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('catalog.index') }}" style="color: var(--brand-red);">
                <i class="bi bi-book-half me-2"></i> Perpustakaan Multicomp
            </a>

            <div class="d-flex align-items-center ms-auto"> 
                {{-- Quick Search (Desktop) --}}
                <form action="{{ route('catalog.all') }}" method="GET" class="d-none d-lg-block me-3">
                    <div class="input-group input-group-sm">
                        <input type="search" name="search" class="form-control" placeholder="Cari buku..." value="{{ request('search') }}" aria-label="Cari buku cepat">
                        <button class="btn btn-outline-danger" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                {{-- Mobile Search Button --}}
                <a href="{{ route('catalog.all') }}" class="btn btn-sm btn-outline-secondary d-lg-none me-2" aria-label="Cari Buku">
                    <i class="bi bi-search"></i>
                </a>

                {{-- Tombol Kembali --}}
                <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center me-2" title="Kembali ke Beranda">
                    <i class="bi bi-house-door-fill me-1 d-none d-sm-inline"></i> 
                    <span class="d-none d-sm-inline">Beranda</span>
                    <i class="bi bi-house-door-fill d-inline d-sm-none"></i>
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

    <main>
        <div class="container header-section mb-4">
            <h1 class="display-5 fw-bold text-center">Seluruh Katalog Buku</h1>
            <p class="lead text-muted text-center">Jelajahi, cari, dan saring semua koleksi buku kami.</p>
        </div>

        <div class="container my-4">

            {{-- Filter Berdasarkan Genre --}}
            <div class="mb-5">
                <h5 class="fw-bold mb-3 text-center">Saring Berdasarkan Kategori</h5>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('catalog.all', ['search' => request('search')]) }}"
                       class="genre-filter-item {{ !request('genre') ? 'active' : '' }}"
                       style="min-width: 120px;">
                        <div class="genre-code" style="background: #e9ecef; color: #495057;">All</div>
                        <div class="small fw-bold">Semua</div>
                    </a>
                    
                    {{-- LOOPING GENRE DENGAN LOGIKA ICON --}}
                    @foreach ($genres as $genre)
                        <a href="{{ route('catalog.all', ['genre' => $genre->name, 'search' => request('search')]) }}"
                           class="genre-filter-item {{ request('genre') == $genre->name ? 'active' : '' }}"
                           style="min-width: 120px;">
                            
                            <div class="genre-code">
                                @if($genre->icon)
                                    {{-- Jika ada icon, tampilkan gambarnya --}}
                                    <img src="{{ asset('storage/' . $genre->icon) }}" alt="{{ $genre->name }}">
                                @else
                                    {{-- Fallback: Tampilkan kode genre --}}
                                    {{ $genre->genre_code }}
                                @endif
                            </div>
                            
                            <div class="small fw-bold text-truncate">{{ $genre->name }}</div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Form Pencarian --}}
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <form action="{{ route('catalog.all') }}" method="GET">
                        @if(request('genre'))
                            <input type="hidden" name="genre" value="{{ request('genre') }}">
                        @endif
                        <div class="input-group input-group-lg shadow-sm">
                            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul, penulis..." value="{{ request('search') }}" aria-label="Kolom pencarian katalog">
                            <button class="btn btn-danger" type="submit"><i class="bi bi-search"></i> Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Notifikasi Filter Aktif --}}
            @if(request('search') || request('genre'))
                <div class="alert alert-info d-flex justify-content-between align-items-center rounded-3 shadow-sm my-4 p-3">
                    <span class="fw-medium">
                        <i class="bi bi-funnel-fill me-2"></i> Filter Aktif:
                        @if(request('search'))
                            Pencarian: <span class="badge bg-danger">"{{ request('search') }}"</span>
                        @endif
                        @if(request('genre'))
                            Genre: <span class="badge bg-danger">{{ request('genre') }}</span>
                        @endif
                    </span>
                    <a href="{{ route('catalog.all') }}" class="btn btn-outline-danger btn-sm">
                        Hapus Filter &times;
                    </a>
                </div>
            @endif

            {{-- Daftar Buku --}}
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                @forelse ($books as $book)
                    <div class="col">
                        @include('public.catalog.partials._book_card', ['book' => $book])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center mt-3 p-4 shadow-sm">
                            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Maaf, Buku Tidak Ditemukan.</h4>
                            <p class="mb-0">Tidak ada buku yang cocok dengan kriteria filter Anda. Coba kata kunci atau filter lain.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Paginasi --}}
            <div class="d-flex justify-content-center mt-5">
                {{ $books->appends(request()->query())->links() }}
            </div>
        </div>
    </main>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>