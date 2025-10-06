<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Peminjaman Saya - Perpustakaan Multicomp</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root { --brand-red: #c62828; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('catalog.index') }}" style="color: var(--brand-red);">
                Perpustakaan Multicomp
            </a>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">Kembali ke Dashboard</a>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <div class="mb-4">
            <h1 class="h3 fw-bold">Riwayat Peminjaman Saya</h1>
            <p class="text-muted">Daftar buku yang sedang dan pernah Anda pinjam.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Judul Buku</th>
                                <th>Kode Eksemplar</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($borrowings as $index => $borrow)
                                @php
                                    $isOverdue = $borrow->status == 'borrowed' && $borrow->due_at < now();
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $borrow->bookCopy->book->title }}</td>
                                    <td>{{ $borrow->bookCopy->book_code }}</td>
                                    <td>{{ \Carbon\Carbon::parse($borrow->borrowed_at)->format('d M Y') }}</td>
                                    <td>{{ $borrow->due_at ? \Carbon\Carbon::parse($borrow->due_at)->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($borrow->returned_at)
                                            {{ \Carbon\Carbon::parse($borrow->returned_at)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{-- =============================================== --}}
                                        {{-- PERUBAHAN DI SINI: Logika status baru --}}
                                        {{-- =============================================== --}}
                                        @switch($borrow->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                                                @break
                                            @case('borrowed')
                                                @if($isOverdue)
                                                    <span class="badge bg-danger">Terlambat</span>
                                                @else
                                                    <span class="badge bg-primary">Dipinjam</span>
                                                @endif
                                                @break
                                            @case('returned')
                                                <span class="badge bg-success">Dikembalikan</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Anda belum memiliki riwayat peminjaman.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>