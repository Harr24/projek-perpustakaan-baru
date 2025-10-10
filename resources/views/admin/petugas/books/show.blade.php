<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Buku: {{ $book->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tambahkan sedikit style agar paragraf sinopsis lebih rapi */
        .book-synopsis {
            white-space: pre-wrap; /* Jaga format spasi dan baris baru */
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
                        @if($book->cover_image)
                            <img src="{{ Storage::url($book->cover_image) }}" 
                                 alt="Cover Buku" 
                                 class="img-fluid rounded shadow-sm">
                        @else
                            <div class="bg-secondary text-white p-4 rounded d-flex align-items-center justify-content-center" style="min-height: 200px;">No Cover</div>
                        @endif
                    </div>

                    <div class="col-md-9">
                        <h3 class="fw-bold">{{ $book->title }}</h3>
                        <p class="mb-1"><strong>Penulis:</strong> {{ $book->author }}</p>
                        <p class="mb-1"><strong>Genre:</strong> {{ optional($book->genre)->name ?? 'N/A' }}</p>
                        <p><strong>Total Stok Awal:</strong> {{ $book->stock }}</p>
                    </div>
                </div>

                @if ($book->synopsis)
                    <hr class="my-4">
                    <div>
                        <h5 class="fw-bold">Sinopsis</h5>
                        {{-- Menggunakan nl2br(e($text)) agar aman dari XSS tapi tetap menampilkan baris baru --}}
                        <p class="book-synopsis">{!! nl2br(e($book->synopsis)) !!}</p>
                    </div>
                @endif
                </div>
        </div>

        <div class="card shadow-sm mt-4">
            {{-- ... Sisa kode untuk daftar salinan tidak perlu diubah ... --}}
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
                            @forelse ($book->copies as $copy)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $copy->book_code }}</td>
                                    <td>
                                        @if($copy->status == 'tersedia') {{-- Saya perbaiki dari 'available' ke 'tersedia' --}}
                                            <span class="badge bg-success">Tersedia</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($copy->status) }}</span>
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>