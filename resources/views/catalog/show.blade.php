<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $book->title }} - Katalog Perpustakaan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --brand-red: #c62828; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .cover-image {
            width: 100%;
            max-width: 300px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .book-synopsis {
            white-space: pre-wrap;
            line-height: 1.6;
            color: #495057;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('catalog.index') }}" style="color: var(--brand-red);">
                Perpustakaan Multicomp
            </a>
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
        
        <div class="card">
            <div class="card-body p-lg-5">
                <div class="row">
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        <img src="{{ $book->cover_image ? Storage::url($book->cover_image) : 'https://via.placeholder.com/300x400.png?text=No+Cover' }}" 
                             class="cover-image" alt="Sampul {{ $book->title }}">
                    </div>

                    <div class="col-md-8">
                        <h1 class="h2 fw-bold">{{ $book->title }}</h1>
                        <p class="text-muted">oleh {{ $book->author }}</p>
                        <span class="badge bg-danger mb-3">{{ $book->genre->name }}</span>
                        
                        {{-- ==================================================== --}}
                        {{-- ========== PASTIKAN BLOK KODE INI ADA ========== --}}
                        {{-- ==================================================== --}}
                        @if ($book->synopsis)
                            <p class="book-synopsis mt-3">{!! nl2br(e($book->synopsis)) !!}</p>
                        @endif
                        {{-- ==================================================== --}}

                    </div>
                </div>

                <hr class="my-4">

                @auth
                    {{-- Sisa kode untuk tabel peminjaman --}}
                    @include('catalog.partials.book-copies-table')
                @endauth

                @guest
                    <div class="alert alert-warning">
                        Anda harus <a href="{{ route('login') }}" class="alert-link">login</a> untuk dapat meminjam buku.
                    </div>
                @endguest
                
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>