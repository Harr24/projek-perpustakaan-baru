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
        .border-dashed {
            border-style: dashed !important;
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
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body p-lg-5">
                <div class="row">
                    {{-- Kolom Kiri: Gambar Sampul --}}
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        {{-- Memastikan gambar menggunakan route() --}}
                        <img src="{{ $book->cover_image ? route('book.cover', $book) : 'https://via.placeholder.com/300x400.png?text=No+Cover' }}" 
                             class="cover-image" alt="Sampul {{ $book->title }}">
                    </div>

                    {{-- Kolom Kanan: Detail Buku --}}
                    <div class="col-md-8">
                        <h1 class="h2 fw-bold">{{ $book->title }}</h1>
                        <p class="text-muted">oleh {{ $book->author }}</p>
                        <div>
                            <span class="badge bg-danger mb-3">{{ $book->genre->name }}</span>
                            @if($book->is_textbook)
                                <span class="badge bg-info mb-3">Buku Paket</span>
                            @endif
                        </div>
                        @if($book->description)
                            <p>{{ $book->description }}</p>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                @auth
                    {{-- ========================================================== --}}
                    {{-- FITUR BARU: Form Pinjam Massal untuk Guru --}}
                    {{-- ========================================================== --}}
                    @if(Auth::user()->role == 'guru' && $book->is_textbook)
                        <div class="card bg-light border-2 border-dashed mb-4">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">Pinjam Massal (Khusus Guru)</h5>
                                <p class="card-text text-muted mb-2">Stok tersedia: <strong>{{ $availableCopiesCount }}</strong></p>
                                <form action="{{ route('borrow.store.bulk') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="quantity" placeholder="Jumlah" min="1" max="{{ $availableCopiesCount }}" required>
                                        <button class="btn btn-danger" type="submit" {{ $availableCopiesCount < 1 ? 'disabled' : '' }}>
                                            Ajukan Pinjaman Massal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <h3 class="h5 fw-bold mt-4">Daftar Salinan Buku (Pinjam Satuan)</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode Eksemplar</th>
                                    <th>Status</th>
                                    <th style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($book->copies as $copy)
                                    <tr>
                                        <td>{{ $copy->book_code }}</td>
                                        <td>
                                            @if($copy->status == 'tersedia')
                                                <span class="badge bg-success">Tersedia</span>
                                            @elseif($copy->status == 'pending')
                                                <span class="badge bg-warning text-dark">Sedang Diajukan</span>
                                            @else
                                                <span class="badge bg-secondary">Dipinjam</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($copy->status == 'tersedia')
                                                <a href="{{ route('borrow.create', $copy) }}" class="btn btn-danger btn-sm">Ajukan Pinjaman</a>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>Tidak Tersedia</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada salinan buku ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endauth

                @guest
                    <div class="alert alert-warning">
                        Anda harus <a href="{{ route('login') }}" class="alert-link">login</a> atau <a href="{{ route('register') }}" class="alert-link">mendaftar</a> untuk dapat meminjam buku.
                    </div>
                @endguest
                
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>