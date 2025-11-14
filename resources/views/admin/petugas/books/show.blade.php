<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Buku: {{ $book->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .book-synopsis {
            white-space: pre-wrap; 
            line-height: 1.6;
            color: #495057;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container py-4">
        <div class="mb-3">
            <a href="{{ route('admin.petugas.books.index') }}" class="btn btn-sm btn-danger">
                &larr; Kembali ke Daftar Buku
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Detail Buku</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        @if($book->cover_image && Storage::disk('public')->exists($book->cover_image))
                            <img src="{{ Storage::url($book->cover_image) }}" 
                                 alt="Cover Buku" 
                                 class="img-fluid rounded shadow-sm"
                                 style="max-height: 300px; object-fit: cover;">
                        @else
                            <img src="https://placehold.co/200x300/eef0f2/6c757d?text=No+Cover" 
                                 alt="No Cover" 
                                 class="img-fluid rounded shadow-sm">
                        @endif
                    </div>

                    <div class="col-md-9">
                        <h3 class="fw-bold">{{ $book->title }}</h3>
                        <p class="mb-1"><strong>Penulis:</strong> {{ $book->author }}</p>
                        <p class="mb-1"><strong>Genre:</strong> {{ optional($book->genre)->name ?? 'N/A' }}</p>
                        
                        {{-- ========================================================== --}}
                        {{-- --- BARIS INI SAYA TAMBAHKAN --- --}}
                        <p class="mb-1"><strong>Lokasi Rak:</strong> {{ optional($book->shelf)->name ?? 'Belum Diatur' }}</p>
                        {{-- ========================================================== --}}

                        <p class="mb-1">
                            <strong>Tipe Buku:</strong>
                            @switch($book->book_type)
                                @case('reguler')
                                    <span class="badge bg-primary">Buku Reguler</span>
                                    @break
                                @case('paket')
                                    <span class="badge bg-info text-dark">Buku Paket</span>
                                    @break
                                @case('laporan')
                                    <span class="badge bg-secondary">Buku Laporan</span>
                                    @break
                                @default
                                    <span class="badge bg-dark">{{ ucfirst($book->book_type) }}</span>
                            @endswitch
                            </p>

                        {{-- 
                            CATATAN: 
                            Di screenshot Anda, ini adalah "Total Stok Awal: 10".
                            Di kode Anda, ini mengambil dari $book->stock.
                            Ini sudah benar dan sesuai.
                        --}}
                        <p><strong>Total Stok Awal:</strong> {{ $book->stock }}</p>
                    </div>
                </div>

                @if ($book->synopsis)
                    <hr class="my-4">
                    <div>
                        <h5 class="fw-bold">Sinopsis</h5>
                        <p class="book-synopsis">{!! nl2br(e($book->synopsis)) !!}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Daftar Salinan Buku</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-danger">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Kode Buku</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- INI ADALAH BLOK YANG DIPERBAIKI --}}
                            @forelse ($book->copies as $copy)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $copy->book_code }}</td>
                                    <td>
                                        @if($copy->status == 'tersedia')
                                            <span class="badge bg-success">Tersedia</span>
                                        @elseif ($copy->status == 'dipinjam')
                                            <span class="badge bg-warning text-dark">Dipinjam</span>
                                        @elseif ($copy->status == 'pending')
                                            <span class="badge bg-info text-dark">Pending</span>
                                        @elseif ($copy->status == 'overdue')
                                            <span class="badge bg-danger">Terlambat</span>
                                        @else
                                            <span class="badge bg-dark">{{ ucfirst($copy->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Belum ada data salinan untuk buku ini.
                                    </td>
                                </tr>
                            @endforelse 
                            {{-- ^ PASTIKAN INI @endforelse --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>