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
        {{-- Input Tersembunyi untuk ID Pinjaman akan diisi oleh JavaScript --}}
    </form>

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

                <button type="submit" form="bulk-approve-form" class="btn btn-primary btn-sm" id="btn-approve-multiple" disabled>
                    <i class="bi bi-check2-all me-1"></i> Konfirmasi yang Dipilih
                </button>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="approvalTable">
                    <thead class="bg-light">
                        <tr>
                            {{-- Checkbox header untuk Pilih Semua (total) --}}
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
                        
                        <tr data-borrowing-id="{{ $borrow->id }}" data-user-id="{{ $borrow->user_id }}">
                            <td class="ps-4">
                                {{-- Checkbox Pengelompokan (Hanya tampil jika user baru) --}}
                                @if ($isNewUser)
                                     <input class="form-check-input check-all-user" 
                                        type="checkbox" 
                                        data-user-id="{{ $borrow->user_id }}" 
                                        id="user-{{ $borrow->user_id }}-check"
                                        title="Pilih Semua Pengajuan {{ $borrow->user->name }}">
                                @endif
                                
                                {{-- Checkbox Individual (Selalu ada di setiap baris, disembunyikan jika ada kontrol grup) --}}
                                <input class="form-check-input borrowing-checkbox {{ $isNewUser ? 'd-none' : '' }}" 
                                    type="checkbox" 
                                    value="{{ $borrow->id }}"
                                    data-user-id="{{ $borrow->user_id }}"
                                    id="borrowing-{{ $borrow->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                        {{ strtoupper(substr($borrow->user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-medium">{{ $borrow->user->name }}</span>
                                    
                                    {{-- Teks "Pilih Semua" (Hanya muncul di baris pertama per peminjam) --}}
                                    @if ($isNewUser)
                                        <label class="form-check-label small text-muted ms-2" for="user-{{ $borrow->user_id }}-check">
                                            Pilih Semua
                                        </label>
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
        const selectAllCheckbox = document.getElementById('selectAll');
        const bulkApproveForm = document.getElementById('bulk-approve-form');
        const btnApproveMultiple = document.getElementById('btn-approve-multiple');
        const borrowingCheckboxes = document.querySelectorAll('.borrowing-checkbox');
        const checkAllUserCheckboxes = document.querySelectorAll('.check-all-user');

        // Fungsi Helper untuk mengelola input tersembunyi (payload yang dikirim)
        function updateHiddenInputs() {
            // Hapus semua input tersembunyi yang ada
            bulkApproveForm.querySelectorAll('input[type="hidden"][name="borrowing_ids[]"]').forEach(input => input.remove());

            let checkedCount = 0;

            // Ambil SEMUA borrowing-checkbox, termasuk yang tersembunyi (yang disembunyikan adalah kontrol per baris)
            // Kita harus mengambil status centang dari KONTROL TERLIHAT, yaitu checkAllUser atau borrowing-checkbox yang tidak disembunyikan.

            // 1. Kumpulkan ID dari semua yang dicentang secara individual (yang terlihat)
            document.querySelectorAll('.borrowing-checkbox:not(.d-none):checked').forEach(checkbox => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'borrowing_ids[]';
                hiddenInput.value = checkbox.value;
                bulkApproveForm.appendChild(hiddenInput);
                checkedCount++;
            });
            
            // 2. Kumpulkan ID dari kontrol grup yang dicentang (yang akan mencakup item d-none di barisnya)
            document.querySelectorAll('.check-all-user:checked').forEach(userCheck => {
                const userId = userCheck.dataset.userId;
                
                // Cari ID peminjaman untuk baris yang memiliki kontrol grup ini (baris pertama)
                const firstRowCheckbox = document.getElementById(`borrowing-${userId}`).value;
                
                // Cek apakah ID ini sudah masuk. Jika belum, masukkan.
                if (!bulkApproveForm.querySelector(`input[type="hidden"][value="${firstRowCheckbox}"]`)) {
                     const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'borrowing_ids[]';
                    hiddenInput.value = firstRowCheckbox;
                    bulkApproveForm.appendChild(hiddenInput);
                    checkedCount++;
                }
            });

            // Aktifkan/Nonaktifkan tombol Konfirmasi Massal
            btnApproveMultiple.disabled = checkedCount === 0;
            return checkedCount;
        }

        // 1. Logika 'Pilih Semua' (Seluruh Tabel)
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                
                // Centang semua checkbox individu (termasuk yang tersembunyi)
                document.querySelectorAll('.borrowing-checkbox').forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                
                // Centang semua checkbox kontrol grup
                checkAllUserCheckboxes.forEach(userCheck => {
                    userCheck.checked = isChecked;
                });
                
                updateHiddenInputs(); // Wajib dipanggil untuk update payload
                updateSelectAllStatus(); // Update status header
            });
        }
        
        // 2. Logika 'Pilih Semua per Peminjam' (Centang Otomatis)
        checkAllUserCheckboxes.forEach(userCheck => {
            userCheck.addEventListener('change', function() {
                const userId = this.dataset.userId;
                const isChecked = this.checked;
                
                // Centang/Hapus centang semua checkbox individu yang memiliki data-user-id yang sama
                const relatedCheckboxes = document.querySelectorAll(`.borrowing-checkbox[data-user-id="${userId}"]`);
                relatedCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                
                // Update status 'Pilih Semua' utama dan hidden inputs
                updateSelectAllStatus();
                updateHiddenInputs(); // Wajib dipanggil untuk update payload
            });
        });

        // 3. Logika Update Status 'Pilih Semua' Utama & Per Peminjam (Manual Change)
        borrowingCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllStatus();
                updateUserCheckStatus(this.dataset.userId);
                updateHiddenInputs(); // Wajib dipanggil untuk update payload
            });
        });

        // 4. Logika Update Status 'Pilih Semua per Peminjam'
        function updateUserCheckStatus(userId) {
            const userCheck = document.getElementById(`user-${userId}-check`);
            if (userCheck) {
                const relatedCheckboxes = document.querySelectorAll(`.borrowing-checkbox[data-user-id="${userId}"]`);
                const totalUser = relatedCheckboxes.length;
                const checkedUser = document.querySelectorAll(`.borrowing-checkbox[data-user-id="${userId}"]:checked`).length;
                
                if (checkedUser === 0) {
                    userCheck.checked = false;
                    userCheck.indeterminate = false;
                } else if (checkedUser === totalUser) {
                    userCheck.checked = true;
                    userCheck.indeterminate = false;
                } else {
                    userCheck.checked = false;
                    userCheck.indeterminate = true;
                }
            }
        }

        // Helper: Memastikan status Pilih Semua Utama sudah benar
        function updateSelectAllStatus() {
            const total = borrowingCheckboxes.length;
            const checked = document.querySelectorAll('.borrowing-checkbox:checked').length;
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = total > 0 && total === checked;
                selectAllCheckbox.indeterminate = checked > 0 && checked < total;
            }
        }
        
        // Inisialisasi: Panggil updateHiddenInputs() saat DOMContentLoaded
        updateHiddenInputs(); 
    });
</script>
@endpush
