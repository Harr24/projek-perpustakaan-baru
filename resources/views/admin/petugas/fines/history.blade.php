<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Denda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Riwayat Denda</h1>
            <p class="text-muted mb-0">Daftar denda keterlambatan yang sudah lunas.</p>
        </div>
        <a href="{{ route('admin.petugas.fines.index') }}" class="btn btn-outline-danger">Kembali ke Denda Aktif</a>
    </div>

    {{-- ========================================================== --}}
    {{-- BAGIAN BARU: Form Filter --}}
    {{-- ========================================================== --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.petugas.fines.history') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="search" class="form-label">Cari Nama Peminjam</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Masukkan nama...">
                </div>
                <div class="col-md-5">
                    <label for="month_year" class="form-label">Filter per Bulan</label>
                    <select name="month_year" id="month_year" class="form-select">
                        <option value="">-- Semua Bulan --</option>
                        @foreach ($months as $month)
                            <option value="{{ $month->year }}-{{ $month->month }}" {{ request('month_year') == ($month->year . '-' . $month->month) ? 'selected' : '' }}>
                                {{ $month->month_name }} {{ $month->year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-danger">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Denda Lunas</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3">Nama Peminjam</th>
                            <th class="py-3 px-3">Judul Buku</th>
                            <th class="py-3 px-3">Jumlah Denda</th>
                            <th class="py-3 px-3">Tanggal Lunas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paidFines as $fine)
                            <tr>
                                <td class="px-3">{{ $fine->user->name }}</td>
                                <td class="px-3">
                                    {{ $fine->bookCopy->book->title }}
                                    <small class="d-block text-muted">{{ $fine->bookCopy->book_code }}</small>
                                </td>
                                <td class="px-3">Rp {{ number_format($fine->fine_amount, 0, ',', '.') }}</td>
                                <td class="px-3">{{ $fine->updated_at->format('d M Y, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Tidak ada data riwayat denda yang cocok dengan filter.
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