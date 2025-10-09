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
        .table-danger { 
            --bs-table-bg: #f8d7da;
            --bs-table-hover-bg: #f5c6cb;
        }
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
            {{-- Pastikan route ini benar --}}
            <a href="{{ route('admin.petugas.fines.index') }}" class="btn btn-warning"><i class="bi bi-cash-coin"></i> Lihat Daftar Denda</a>
            {{-- Pastikan route ini benar --}}
            <a href="{{ route('dashboard') }}" class="btn btn-outline-danger">Kembali ke Dashboard</a>
        </div>
    </div>

    {{-- Notifikasi Session --}}
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

    {{-- Form untuk Pengembalian Massal --}}
    <form action="{{ route('admin.petugas.returns.storeMultiple') }}" method="POST">
        @csrf
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Buku Sedang Dipinjam</h5>
                {{-- Tombol submit untuk form massal --}}
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
                                    // Hitung hari kerja terlambat untuk ditampilkan
                                    $lateWeekdays = $isOverdue ? $dueDate->diffInDaysFiltered(fn($date) => !$date->isSaturday() && !$date->isSunday(), now()) : 0;
                                @endphp
                                <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                    <td class="px-3">
                                        {{-- Checkbox untuk pengembalian massal --}}
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
                                            <span class="badge bg-danger">Terlambat {{ $lateWeekdays }} hari kerja</span>
                                        @else
                                            <span class="badge bg-primary">Dipinjam</span>
                                        @endif
                                    </td>
                                    <td class="px-3">
                                        {{-- Tombol untuk modal, membawa URL aksi dan status terlambat --}}
                                        <button 
                                            type="button" 
                                            class="btn btn-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#returnConfirmModal" 
                                            data-form-action="{{ route('admin.petugas.returns.store', $borrow) }}"
                                            data-is-overdue="{{ $isOverdue ? '1' : '0' }}"
                                        >
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

{{-- ========================================================== --}}
{{-- MODAL KONFIRMASI (KODE LENGKAP) --}}
{{-- ========================================================== --}}
<div class="modal fade" id="returnConfirmModal" tabindex="-1" aria-labelledby="returnConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{-- Form ini akan diisi action-nya oleh JavaScript --}}
            <form id="modalReturnForm" method="POST" action="">
                @csrf
                {{-- WAJIB: Menggunakan PUT untuk operasi update/pengembalian --}}
                @method('PUT') 

                <div class="modal-header">
                    <h5 class="modal-title" id="returnConfirmModalLabel">Konfirmasi Pengembalian Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyelesaikan proses pengembalian buku ini?</p>
                    {{-- Alert ini akan muncul jika buku terlambat --}}
                    <div id="modal-overdue-alert" class="alert alert-warning" role="alert" style="display:none;">
                        Buku **terlambat**. Pastikan denda sudah dicatat jika diperlukan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ==========================================================
    // SKRIP JAVASCRIPT
    // ==========================================================
    
    // 1. Skrip "Pilih Semua" (Select All)
    document.getElementById('selectAll').addEventListener('click', function(event) {
        const checkboxes = document.querySelectorAll('input[name="borrowing_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = event.target.checked;
        });
    });

    // 2. Skrip untuk Menghubungkan Tombol "Kembalikan" ke Modal (Modal Dynamic Action)
    const returnConfirmModal = document.getElementById('returnConfirmModal');

    if (returnConfirmModal) {
        returnConfirmModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Tombol yang memicu modal
            
            // Ambil URL aksi dan status terlambat dari tombol
            const actionUrl = button.getAttribute('data-form-action'); 
            const isOverdue = button.getAttribute('data-is-overdue') === '1';

            // Set action URL pada form modal
            const modalForm = returnConfirmModal.querySelector('#modalReturnForm');
            if (modalForm) {
                modalForm.setAttribute('action', actionUrl);
            }

            // Tampilkan/sembunyikan alert denda
            const overdueAlert = document.getElementById('modal-overdue-alert');
            if (overdueAlert) {
                if (isOverdue) {
                    overdueAlert.style.display = 'block';
                } else {
                    overdueAlert.style.display = 'none';
                }
            }
        });
    }
</script>

</body>
</html>
