<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Peminjaman</title>
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
            <h1 class="h3 fw-bold mb-0">Manajemen Peminjaman</h1>
            <p class="text-muted mb-0">Daftar buku yang sedang dipinjam & terlambat.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.petugas.fines.index') }}" class="btn btn-warning"><i class="bi bi-cash-coin"></i> Lihat Daftar Denda</a>
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

    {{-- ========================================================== --}}
    {{-- PERUBAHAN UTAMA: Membungkus tabel dengan <form>          --}}
    {{-- ========================================================== --}}
    <form action="{{ route('admin.petugas.returns.storeMultiple') }}" method="POST">
        @csrf
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Buku Sedang Dipinjam</h5>
                <button type="submit" class="btn btn-light btn-sm" onclick="return confirm('Kembalikan semua buku yang dipilih?');">
                    <i class="bi bi-check2-all"></i> Kembalikan yang Dipilih
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 px-3" style="width: 5%;"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                                <th class="py-3 px-3">Judul Buku</th>
                                <th class="py-3 px-3">Peminjam</th>
                                <th class="py-3 px-3">Jatuh Tempo</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3">Aksi Individual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activeBorrowings as $borrow)
                                @php
                                    $dueDate = \Carbon\Carbon::parse($borrow->due_at);
                                    $isOverdue = $borrow->status == 'borrowed' && now()->isAfter($dueDate);
                                @endphp
                                <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                    <td class="px-3">
                                        <input class="form-check-input" type="checkbox" name="borrowing_ids[]" value="{{ $borrow->id }}">
                                    </td>
                                    <td class="px-3">
                                        {{ $borrow->bookCopy->book->title }}
                                        <small class="d-block text-muted">{{ $borrow->bookCopy->book_code }}</small>
                                    </td>
                                    <td class="px-3">{{ $borrow->user->name }}</td>
                                    <td class="px-3 fw-bold">{{ $dueDate->format('d M Y') }}</td>
                                    <td class="px-3">
                                        @if($isOverdue)
                                            @php
                                                $lateWeekdays = $dueDate->diffInDaysFiltered(fn($date) => !$date->isSaturday() && !$date->isSunday(), now());
                                            @endphp
                                            <span class="badge bg-danger">Terlambat {{ $lateWeekdays }} hari kerja</span>
                                        @else
                                            <span class="badge bg-primary">Dipinjam</span>
                                        @endif
                                    </td>
                                    <td class="px-3">
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#returnConfirmModal" data-form-action="{{ route('admin.petugas.returns.store', $borrow) }}">
                                            Kembalikan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada buku yang sedang dipinjam.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Modal Konfirmasi Pengembalian --}}
<div class="modal fade" id="returnConfirmModal" tabindex="-1" aria-labelledby="returnConfirmModalLabel" aria-hidden="true">
    {{-- ... Kode modal Anda tetap sama ... --}}
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    {{-- ... Kode JavaScript modal Anda tetap sama ... --}}

    // Script sederhana untuk fungsi "Pilih Semua"
    document.getElementById('selectAll').addEventListener('click', function(event) {
        const checkboxes = document.querySelectorAll('input[name="borrowing_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = event.target.checked;
        });
    });
</script>

</body>
</html>