<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konfirmasi Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --brand-red: #c62828; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h3 class="mb-0">Konfirmasi Pengajuan Pinjaman</h3>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-3">Anda akan mengajukan peminjaman untuk buku:</p>
                        <div class="mb-3">
                            <h5 class="card-title">{{ $book_copy->book->title }}</h5>
                            <p class="card-text text-muted">Kode Eksemplar: <strong>{{ $book_copy->book_code }}</strong></p>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6"><strong>Tanggal Pinjam:</strong></div>
                            <div class="col-6 text-end">{{ $borrowDate->format('d F Y') }}</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6"><strong>Batas Pengembalian:</strong></div>
                            <div class="col-6 text-end fw-bold">{{ $dueDate->format('d F Y') }}</div>
                        </div>
                        <small class="text-muted d-block mt-3">Durasi peminjaman adalah 7 hari di luar hari Sabtu & Minggu.</small>

                        <form action="{{ route('borrow.store') }}" method="POST" class="mt-4">
                            @csrf
                            <input type="hidden" name="book_copy_id" value="{{ $book_copy->id }}">
                            <input type="hidden" name="due_at" value="{{ $dueDate }}">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-danger">Ya, Ajukan Pinjaman</button>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>