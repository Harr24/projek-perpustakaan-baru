<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Buku: {{ $book->title }}</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container py-4">
        <!-- Tombol Kembali -->
        <div class="mb-3">
            <a href="{{ route('admin.petugas.books.index') }}" class="btn btn-sm btn-danger">
                &larr; Kembali ke Daftar Buku
            </a>
        </div>

        <!-- Card Detail Buku -->
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Detail Buku</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Cover -->
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        @if($book->cover_image)
                            <img src="{{ Storage::url($book->cover_image) }}" 
                                 alt="Cover Buku" 
                                 class="img-fluid rounded shadow-sm">
                        @else
                            <div class="bg-secondary text-white p-4 rounded">No Cover</div>
                        @endif
                    </div>

                    <!-- Info Buku -->
                    <div class="col-md-9">
                        <h3 class="fw-bold">{{ $book->title }}</h3>
                        <p><strong>Penulis:</strong> {{ $book->author }}</p>
                        <p><strong>Genre:</strong> {{ optional($book->genre)->name ?? 'N/A' }}</p>
                        <p><strong>Total Stok Awal:</strong> {{ $book->stock }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Salinan -->
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
                            @forelse ($book->copies as $copy)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $copy->book_code }}</td>
                                    <td>
                                        @if($copy->status == 'available')
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
