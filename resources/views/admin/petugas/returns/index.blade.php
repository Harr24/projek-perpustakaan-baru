@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2" style="color: #d9534f;">Manajemen Peminjaman</h1>
            <p class="text-muted mb-0 small">Daftar buku yang sedang dipinjam & terlambat.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.petugas.fines.index') }}" class="btn btn-warning btn-sm"><i class="bi bi-cash-coin"></i> Lihat Daftar Denda</a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- ========================================================== --}}
    {{-- PERUBAHAN STRUKTUR UTAMA --}}
    {{-- ========================================================== --}}

    <!-- 1. Form untuk Aksi Massal sekarang KOSONG dan TERPISAH -->
    <form action="{{ route('admin.petugas.returns.storeMultiple') }}" method="POST" id="bulk-return-form" onsubmit="return confirm('Anda yakin ingin mengembalikan semua buku yang dipilih?');">
        @csrf
        @method('PUT')
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-journal-arrow-up me-2"></i> Buku Sedang Dipinjam</h5>
            <!-- 2. Tombol ini sekarang secara LOGIS terhubung ke form di atas menggunakan atribut 'form' -->
            <button type="submit" form="bulk-return-form" class="btn btn-light btn-sm fw-bold">
                <i class="bi bi-check2-all"></i> Kembalikan yang Dipilih
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <!-- 3. Tabel sekarang TIDAK LAGI dibungkus oleh form massal -->
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3" style="width: 5%;"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                            <th class="py-3 px-3">Judul Buku</th>
                            <th class="py-3 px-3">Peminjam</th>
                            <th class="py-3 px-3">Jatuh Tempo</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-end">Aksi Individual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activeBorrowings as $borrow)
                            @php
                                $dueDate = \Carbon\Carbon::parse($borrow->due_date);
                                $isOverdue = $borrow->status == 'approved' && now()->isAfter($dueDate);
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                <td class="px-3">
                                    <!-- 4. Checkbox juga terhubung ke form massal menggunakan atribut 'form' -->
                                    <input class="form-check-input" type="checkbox" name="borrowing_ids[]" value="{{ $borrow->id }}" form="bulk-return-form">
                                </td>
                                <td class="px-3">
                                    {{ $borrow->bookCopy->book->title }}
                                    <small class="d-block text-muted">{{ $borrow->bookCopy->book_code }}</small>
                                </td>
                                <td class="px-3">{{ $borrow->user->name }}</td>
                                <td class="px-3 fw-bold">{{ $dueDate->format('d M Y') }}</td>
                                <td class="px-3">
                                    @if($isOverdue)
                                        <span class="badge bg-danger">Terlambat</span>
                                    @else
                                        <span class="badge bg-primary">Dipinjam</span>
                                    @endif
                                </td>
                                <td class="px-3 text-end">
                                    <!-- 5. Form individual sekarang TIDAK BERSARANG dan akan berfungsi dengan benar -->
                                    <form action="{{ route('admin.petugas.returns.store', $borrow) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian buku ini?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm">Kembalikan</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-check2-all d-block display-4 opacity-25"></i>Tidak ada buku yang sedang dipinjam.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Skrip "Pilih Semua" sekarang menargetkan checkbox dengan atribut 'form'
    document.getElementById('selectAll').addEventListener('click', function(event) {
        const checkboxes = document.querySelectorAll('input[form="bulk-return-form"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = event.target.checked;
        });
    });
</script>
@endpush