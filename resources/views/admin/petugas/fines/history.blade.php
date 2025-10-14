<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Denda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Riwayat Denda</h1>
            <p class="text-muted mb-0">Daftar denda keterlambatan yang sudah lunas.</p>
        </div>
        <a href="{{ route('admin.petugas.fines.index') }}" class="btn btn-outline-danger">Kembali ke Denda Aktif</a>
    </div>

    {{-- Form Filter --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.petugas.fines.history') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">Cari Nama Peminjam</label>
                    <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Masukkan nama...">
                </div>

                <div class="col-md-3">
                    <label for="year" class="form-label">Filter per Tahun</label>
                    <select name="year" id="year" class="form-select">
                        <option value="">-- Semua Tahun --</option>
                        @foreach ($years as $year)
                            <option value="{{ $year->year }}" {{ request('year') == $year->year ? 'selected' : '' }}>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="month" class="form-label">Filter per Bulan</label>
                    <select name="month" id="month" class="form-select">
                        <option value="">-- Semua Bulan --</option>
                        @php
                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ========================================================== --}}
                {{-- (BARU) Grup tombol untuk Filter dan Export --}}
                {{-- ========================================================== --}}
                <div class="col-md-3">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-danger flex-grow-1 me-2">Filter</button>
                        <a href="{{ route('admin.petugas.fines.export', request()->query()) }}" class="btn btn-success" title="Export ke Excel">
                            <i class="bi bi-file-earmark-excel"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Riwayat Denda --}}
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
                            <th class="py-3 px-3">Kelas</th>
                            <th class="py-3 px-3">Kontak (WA)</th>
                            <th class="py-3 px-3">Judul Buku</th>
                            <th class="py-3 px-3">Jumlah Denda</th>
                            <th class="py-3 px-3">Tanggal Lunas</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paidFines as $fine)
                            <tr>
                                <td class="px-3">{{ $fine->user->name }}</td>
                                <td class="px-3">{{ $fine->user->class_name ?? 'N/A' }}</td>
                                <td class="px-3">
                                    @if($fine->user->phone_number)
                                        @php
                                            $cleanedPhone = preg_replace('/[^0-9]/', '', $fine->user->phone_number);
                                            $waNumber = (substr($cleanedPhone, 0, 1) === '0') ? '62' . substr($cleanedPhone, 1) : $cleanedPhone;
                                        @endphp
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="btn btn-sm btn-outline-success" title="Chat {{ $fine->user->name }} di WhatsApp">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td class="px-3">
                                    {{ $fine->bookCopy->book->title }}
                                    <small class="d-block text-muted">{{ $fine->bookCopy->book_code }}</small>
                                </td>
                                <td class="px-3">Rp {{ number_format($fine->fine_amount, 0, ',', '.') }}</td>
                                <td class="px-3">{{ $fine->updated_at->format('d M Y, H:i') }}</td>
                                <td class="px-3 text-center">
                                    <form action="{{ route('admin.petugas.fines.destroy', $fine->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus riwayat ini secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Riwayat">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Tidak ada data riwayat denda yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    {{-- ========================================================== --}}
                    {{-- (BARU) TFOOT untuk menampilkan total denda --}}
                    {{-- ========================================================== --}}
                    @if($paidFines->isNotEmpty())
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="px-3 py-3 text-end">Total Pemasukan Denda (sesuai filter):</td>
                            <td class="px-3 py-3" colspan="3">
                                Rp {{ number_format($totalFine, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>