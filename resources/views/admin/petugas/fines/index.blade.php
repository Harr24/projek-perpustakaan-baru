<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Denda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Manajemen Denda</h1>
            <p class="text-muted mb-0">Daftar denda keterlambatan yang belum lunas.</p>
        </div>
        {{-- =============================================== --}}
        {{-- PERUBAHAN DI SINI: Menambahkan tombol Riwayat  --}}
        {{-- =============================================== --}}
        <div class="d-flex gap-2">
            <a href="{{ route('admin.petugas.fines.history') }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history"></i> Lihat Riwayat Denda
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-danger">Kembali ke Dashboard</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Denda Belum Lunas</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3">Nama Peminjam</th>
                            <th class="py-3 px-3">Judul Buku</th>
                            <th class="py-3 px-3">Jumlah Denda</th>
                            <th class="py-3 px-3">Telat (Hari Kerja)</th>
                            <th class="py-3 px-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unpaidFines as $fine)
                            <tr>
                                <td class="px-3">{{ $fine->user->name }}</td>
                                <td class="px-3">
                                    {{ $fine->bookCopy->book->title }}
                                    <small class="d-block text-muted">{{ $fine->bookCopy->book_code }}</small>
                                </td>
                                <td class="px-3 fw-bold">Rp {{ number_format($fine->fine_amount, 0, ',', '.') }}</td>
                                <td class="px-3">{{ $fine->late_days }} hari</td>
                                <td class="px-3">
                                    <form action="{{ route('admin.petugas.fines.pay', $fine) }}" method="POST" onsubmit="return confirm('Konfirmasi pembayaran denda untuk {{ $fine->user->name }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Tandai Lunas</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Tidak ada denda yang belum lunas.
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