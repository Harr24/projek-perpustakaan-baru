<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perpustakaan Multicomp')</title>

    {{-- CSS Links --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    {{-- Style dasar & untuk sticky footer --}}
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex-grow: 1;
        }
    </style>
    @stack('styles') {{-- Untuk style tambahan per halaman --}}
</head>
<body>
    <div id="public-layout">
        {{-- Navigasi Publik Sederhana --}}
        <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ route('catalog.index') }}" style="color: #c62828;"><i class="bi bi-book-half me-2"></i> Perpustakaan Multicomp</a>
                <div class="d-flex align-items-center">
                    <form action="{{ route('catalog.all') }}" method="GET" class="d-none d-lg-block me-3">
                        <div class="input-group input-group-sm">
                            <input type="search" name="search" class="form-control" placeholder="Cari buku..." value="{{ request('search') }}">
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

        {{-- Main Content (konten unik setiap halaman akan muncul di sini) --}}
        <main>
            @yield('content')
        </main>

        {{-- Footer Gelap --}}
        <footer class="bg-dark text-white pt-5 pb-4 mt-auto">
            <div class="container">
                <div class="row">
                    {{-- Kolom 1 --}}
                    <div class="col-md-4 col-lg-4 mb-4">
                         <h5 class="fw-bold mb-3 d-flex align-items-center">
                            <i class="bi bi-book-half me-2"></i> Perpustakaan Multicomp
                        </h5>
                        <p class="small text-white-50">Sistem informasi ini dirancang untuk memudahkan siswa dan guru dalam mengakses dan meminjam koleksi buku.</p>
                    </div>
                    {{-- Kolom 2 --}}
                    <div class="col-md-4 col-lg-4 mb-4">
                        <h5 class="fw-bold mb-3">Tautan Cepat</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="{{ route('catalog.librarians') }}" class="text-white-50 text-decoration-none">Pustakawan</a></li>
                            <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none">Layanan</a></li>
                        </ul>
                    </div>
                    {{-- Kolom 3 --}}
                    <div class="col-md-4 col-lg-4 mb-4">
                        <h5 class="fw-bold mb-3">Cari Koleksi</h5>
                        <form action="{{ route('catalog.all') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Masukkan kata kunci...">
                                <button class="btn btn-danger" type="submit">Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
                <hr style="color: #6c757d;">
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="small text-white-50 mb-0">&copy; {{ date('Y') }} Perpustakaan Multicomp. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts') {{-- Untuk script tambahan per halaman --}}
</body>
</html>