@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Bagian Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-2" style="color: #d9534f;">Kelola Pengajuan Pinjaman</h1>
                    <p class="text-muted mb-0 small">Daftar pengajuan peminjaman buku yang menunggu konfirmasi.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- Notifikasi Sukses atau Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form untuk Aksi Massal --}}
    <form action="{{ route('admin.petugas.approvals.approveMultiple') }}" method="POST" id="bulk-approve-form">
        @csrf
        {{-- Input tersembunyi akan diisi oleh JavaScript --}}
    </form>
    
    <!-- ========================================================== -->
    <!-- ===== PENAMBAHAN: Form untuk Tolak Massal ===== -->
    <!-- ========================================================== -->
    <form action="{{ route('admin.petugas.approvals.rejectMultiple') }}" method="POST" id="bulk-reject-form" onsubmit="return confirm('Anda yakin ingin MENOLAK semua pengajuan yang dipilih?');">
        @csrf
        {{-- Input tersembunyi akan diisi oleh JavaScript --}}
    </form>
    <!-- ========================================================== -->


    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-2 text-warning"></i>
                    Peminjaman Menunggu Konfirmasi
                    <span class="badge bg-warning text-dark ms-2">{{ $pendingBorrowings->count() }}</span>
                </h5>
                
                {{-- FORM PENCARIAN --}}
                <form action="{{ route('admin.petugas.approvals.index') }}" method="GET" class="d-flex w-100 w-md-auto">
                    <div class="input-group input-group-sm">
                        <input type="search" name="search" class="form-control" placeholder="Cari nama peminjam..." value="{{ request('search') }}" aria-label="Cari peminjam">
                        @if (request('search'))
                            <a href="{{ route('admin.petugas.approvals.index') }}" class="btn btn-outline-secondary" title="Hapus Filter"><i class="bi bi-x"></i></a>
                        @endif
                        <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <!-- ========================================================== -->
                <!-- ===== MODIFIKASI: Menjadikan Grup Tombol ===== -->
                <!-- ========================================================== -->
                <div class="btn-group btn-group-sm" role="group" aria-label="Aksi Massal">
                    <button type="submit" form="bulk-approve-form" class="btn btn-primary" id="btn-approve-multiple" disabled>
                        <i class="bi bi-check2-all me-1"></i> Konfirmasi
                    </button>
                    <button type="submit" form="bulk-reject-form" class="btn btn-danger" id="btn-reject-multiple" disabled>
                        <i class="bi bi-x-circle me-1"></i> Tolak
                    </button>
                </div>
                <!-- ========================================================== -->

            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="approvalTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4" style="width: 5%;"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                            <th class="py-3">Nama Peminjam</th>
                            <th class="py-3">Kelas</th>
                            <th class="py-3">Judul Buku</th>
                            <th class="py-3">Kode Buku</th>
                            <th class="py-3">Tanggal Pengajuan</th>
                            <th class="py-3 pe-4 text-end" style="width: 200px;">Aksi Individual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentUserId = null; @endphp
                        @forelse ($pendingBorrowings as $borrow)
                            @php
                                $isNewUser = $borrow->user_id !== $currentUserId;
                                $currentUserId = $borrow->user_id;
                            @endphp
                        
                        <tr data-user-id="{{ $borrow->user_id }}">
                            <td class="ps-4">
                                {{-- Setiap baris WAJIB memiliki checkbox ini untuk data --}}
                                <input class="form-check-input borrowing-checkbox" 
                                    type="checkbox" 
                                    value="{{ $borrow->id }}"
                                    data-user-id="{{ $borrow->user_id }}"
                                    id="borrowing-{{ $borrow->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items-center; justify-content: center; font-weight: 600;">
                                        {{ strtoupper(substr($borrow->user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-medium me-3">{{ $borrow->user->name }}</span>
                                    
                                    {{-- Kontrol "Pilih Semua" per grup, HANYA muncul di baris pertama --}}
                                    @if ($isNewUser)
                                        <span class="ms-1 border rounded px-2 py-1 bg-light-subtle" title="Pilih Semua Pengajuan {{ $borrow->user->name }}">
                                            <input class="form-check-input check-all-user" 
                                                type="checkbox" 
                                                data-user-id="{{ $borrow->user_id }}" 
                                                id="user-{{ $borrow->user_id }}-check">
                                            <label class="form-check-label small text-muted" for="user-{{ $borrow->user_id }}-check">Pilih Semua</label>
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $borrow->user->class_name ?? 'N/A' }}</td>
                            <td><span class="text-dark">{{ $borrow->bookCopy->book->title }}</span></td>
                            <td><span class="badge bg-secondary">{{ $borrow->bookCopy->book_code }}</span></td>
                            <td>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ $borrow->created_at->format('d M Y, H:i') }}</small>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <form action="{{ route('admin.petugas.approvals.approve', $borrow) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Konfirmasi" onclick="return confirm('Anda yakin ingin MENYETUJUI pengajuan ini?');"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                    <form action="{{ route('admin.petugas.approvals.reject', $borrow) }}" method="POST" onsubmit="return confirm('Anda yakin ingin MENOLAK pengajuan ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" title="Tolak"><i class="bi bi-x-lg"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
                                    <p class="mb-0">Tidak ada pengajuan pinjaman baru.</p>
                                </div>
                            </td>
                        </tr>
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
document.addEventListener('DOMContentLoaded', function() {
    const selectAllHeader = document.getElementById('selectAll');
    const bulkApproveForm = document.getElementById('bulk-approve-form');
    const btnApproveMultiple = document.getElementById('btn-approve-multiple');
    
    // ==========================================================
    // ===== PENAMBAHAN: Variabel untuk Tolak Massal =====
    // ==========================================================
    const bulkRejectForm = document.getElementById('bulk-reject-form');
    const btnRejectMultiple = document.getElementById('btn-reject-multiple');
    // ==========================================================

    // Fungsi Helper untuk mengelola data yang dikirim dan status tombol
    function updateFormPayloadAndButton() {
        // Hapus semua input tersembunyi yang ada
        const csrfToken = '{{ csrf_token() }}';
        bulkApproveForm.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
        
        // ==========================================================
        // ===== PENAMBAHAN: Kosongkan juga form tolak =====
        // ==========================================================
        bulkRejectForm.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
        // ==========================================================
        
        let checkedCount = 0;
        document.querySelectorAll('.borrowing-checkbox:checked').forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'borrowing_ids[]';
            hiddenInput.value = checkbox.value;
            
            // Masukkan ke kedua form
            bulkApproveForm.appendChild(hiddenInput);
            bulkRejectForm.appendChild(hiddenInput.cloneNode(true)); // Kloning untuk form reject
            
            checkedCount++;
        });
        
        // ==========================================================
        // ===== MODIFIKASI: Aktifkan/Nonaktifkan kedua tombol =====
        // ==========================================================
        btnApproveMultiple.disabled = checkedCount === 0;
        btnRejectMultiple.disabled = checkedCount === 0;
        // ==========================================================
    }

    // Fungsi untuk sinkronisasi semua checkbox kontrol (TIDAK BERUBAH)
    function syncControlCheckboxes() {
        // Sinkronisasi header utama
        const total = document.querySelectorAll('.borrowing-checkbox').length;
        const checked = document.querySelectorAll('.borrowing-checkbox:checked').length;
        if (selectAllHeader) {
            selectAllHeader.checked = total > 0 && total === checked;
            selectAllHeader.indeterminate = checked > 0 && checked < total;
        }

        // Sinkronisasi kontrol per grup
        document.querySelectorAll('.check-all-user').forEach(userCheck => {
            const userId = userCheck.dataset.userId;
            const relatedCheckboxes = document.querySelectorAll(`.borrowing-checkbox[data-user-id="${userId}"]`);
            const totalUser = relatedCheckboxes.length;
            const checkedUser = document.querySelectorAll(`.borrowing-checkbox[data-user-id="${userId}"]:checked`).length;

            userCheck.checked = totalUser > 0 && totalUser === checkedUser;
            userCheck.indeterminate = checkedUser > 0 && checkedUser < totalUser;
        });
    }

    // 1. Event Listener untuk 'Pilih Semua' di Header (TIDAK BERUBAH)
    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.borrowing-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            syncControlCheckboxes();
            updateFormPayloadAndButton();
        });
    }
    
    // 2. Event Listener untuk 'Pilih Semua per Peminjam' (TIDAK BERUBAH)
    document.querySelectorAll('.check-all-user').forEach(userCheck => {
        userCheck.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const isChecked = this.checked;
            document.querySelectorAll(`.borrowing-checkbox[data-user-id="${userId}"]`).forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            syncControlCheckboxes();
            updateFormPayloadAndButton();
        });
    });

    // 3. Event Listener untuk setiap checkbox individual (TIDAK BERUBAH)
    document.querySelectorAll('.borrowing-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            syncControlCheckboxes();
            updateFormPayloadAndButton();
        });
    });
    
    // Inisialisasi saat halaman dimuat (TIDAK BERUBAH)
    updateFormPayloadAndButton();
    syncControlCheckboxes();
});
</script>
@endpush
