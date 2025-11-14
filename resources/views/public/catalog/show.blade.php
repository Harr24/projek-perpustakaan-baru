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
                 <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                     <i class="bi bi-house-door-fill"></i> Kembali ke Beranda
                 </a>
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

        <div class="card border-0 shadow-sm">
            <div class="card-body p-lg-5">
                <div class="row">
                    {{-- Kolom Kiri: Gambar Sampul --}}
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : 'https://placehold.co/300x450/E91E63/FFFFFF?text=No+Cover' }}" 
                             class="cover-image" alt="Sampul {{ $book->title }}">
                    </div>

                    {{-- Kolom Kanan: Detail Buku --}}
                    <div class="col-md-8">
                        <h1 class="h2 fw-bold">{{ $book->title }}</h1>
                        <p class="text-muted">oleh {{ $book->author }}</p>
                        <div>
                            <span class="badge bg-danger mb-3">{{ $book->genre->name }}</span>
                            
                            @switch($book->book_type)
                                @case('paket')
                                    <span class="badge bg-info text-dark mb-3">Buku Paket</span>
                                    @break
                                @case('laporan')
                                    <span class="badge bg-secondary mb-3">Buku Laporan</span>
                                    @break
                                @case('reguler')
                                    <span class="badge bg-primary mb-3">Buku Reguler</span>
                                    @break
                                @default
                                    {{-- Tidak perlu badge untuk 'reguler' jika tidak mau --}}
                            @endswitch
                            </div>

                        {{-- ========================================================== --}}
                        {{-- --- TAMBAHAN BARU UNTUK LOKASI RAK --- --}}
                        <p class="mt-3">
                            <strong>Lokasi Rak:</strong> {{ optional($book->shelf)->name ?? 'Belum Diatur' }}
                        </p>
                        {{-- ========================================================== --}}
                        
                        {{-- Menampilkan Sinopsis --}}
                        @if ($book->synopsis)
                            <hr>
                            <h5 class="fw-bold">Sinopsis</h5>
                            <p class="book-synopsis">{!! nl2br(e($book->synopsis)) !!}</p>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                @auth
                    {{-- ========================================================== --}}
                    @php
                        // Cek HANYA apakah buku ini adalah 'paket'
                        $isBookPackage = ($book->book_type == 'paket');
                    @endphp

                    {{-- Form Pinjam Buku Paket untuk Guru --}}
                    @if(Auth::user()->role == 'guru' && $isBookPackage)
                        <div class="card bg-light border-2 border-danger border-opacity-25 mb-4">
                            <div class="card-body">
                                <h3 class="h5 fw-bold text-danger"><i class="bi bi-person-workspace me-2"></i> Pinjam Buku Paket (Khusus Guru)</h3>
                                <p class="small text-muted">Anda dapat meminjam beberapa eksemplar buku ini sekaligus untuk kebutuhan kelas.</p>
                                <form action="{{ route('borrow.store.bulk') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <div class="row align-items-end">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="quantity" class="form-label fw-semibold">Jumlah yang ingin dipinjam:</label>
                                            <input type="number" name="quantity" id="quantity" class="form-control" 
                                                   min="1" max="{{ $book->available_copies_count }}" 
                                                   placeholder="Maks: {{ $book->available_copies_count }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-danger w-100 fw-bold">
                                                <i class="bi bi-box-arrow-down me-2"></i> Ajukan Pinjaman Massal
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        Saat ini tersedia <strong>{{ $book->available_copies_count }}</strong> eksemplar untuk dipinjam.
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    {{-- Tabel Pinjam Satuan --}}
                    <h3 class="h5 fw-bold mt-4">Daftar Salinan Buku (Pinjam Satuan)</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
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
                                                <form action="{{ route('borrow.store', $copy) }}" method="POST" onsubmit="return confirm('Anda yakin ingin meminjam buku ini?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">Ajukan Pinjaman</button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>Tidak Tersedia</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada salinan buku yang tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endauth

                @guest
                    <div class="alert alert-warning mt-4">
                        Anda harus <a href="{{ route('login') }}" class="alert-link">login</a> atau <a href="{{ route('register') }}" class="alert-link">mendaftar</a> untuk dapat meminjam buku.
                    </div>
                @endguest
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>