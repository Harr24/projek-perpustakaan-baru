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

    {{-- Form untuk Aksi Massal --}}
    <form action="{{ route('admin.petugas.returns.storeMultiple') }}" method="POST" id="bulk-return-form">
        @csrf
        @method('PUT')
        {{-- Input tersembunyi akan ditambahkan oleh JavaScript --}}
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 py-3">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-journal-arrow-up me-2"></i> Buku Sedang Dipinjam</h5>
            
            {{-- Form Pencarian --}}
            <form action="{{ route('admin.petugas.returns.index') }}" method="GET" class="d-flex w-100 w-md-auto">
                <div class="input-group input-group-sm">
                    <input type="search" name="search" class="form-control" placeholder="Cari peminjam atau buku..." value="{{ $search ?? '' }}">
                    @if(isset($search) && $search)
                        <a href="{{ route('admin.petugas.returns.index') }}" class="btn btn-light" title="Hapus Filter"><i class="bi bi-x"></i></a>
                    @endif
                    <button class="btn btn-light" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <button type="submit" form="bulk-return-form" class="btn btn-light btn-sm fw-bold" id="btn-return-multiple" disabled>
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
                            <th class="py-3 px-3">Kelas</th>
                            <th class="py-3 px-3">Kontak (WA)</th>
                            <th class="py-3 px-3">Jatuh Tempo</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-end" style="min-width: 190px;">Aksi Individual</th> {{-- Sedikit dilebarkan lagi --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activeBorrowings as $borrow)
                            @php
                                $dueDate = \Carbon\Carbon::parse($borrow->due_at);
                                $isOverdue = now()->gt($dueDate);
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                <td class="px-3">
                                    <input class="form-check-input borrowing-checkbox" type="checkbox" value="{{ $borrow->id }}">
                                </td>
                                <td class="px-3">
                                    {{ $borrow->bookCopy->book->title }}
                                    <small class="d-block text-muted">{{ $borrow->bookCopy->book_code }}</small>
                                </td>
                                <td class="px-3">{{ $borrow->user->name }}</td>
                                <td class="px-3">{{ $borrow->user->class_name ?? 'N/A' }}</td>
                                <td class="px-3">
                                    @if($borrow->user->phone_number)
                                        @php
                                            $cleanedPhone = preg_replace('/[^0-9]/', '', $borrow->user->phone_number);
                                            $waNumber = '62' . ltrim($cleanedPhone, '0');
                                        @endphp
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="btn btn-sm btn-outline-success" title="Chat {{ $borrow->user->name }} di WhatsApp">
                                            <i class="bi bi-whatsapp"></i> Chat
                                        </a>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td class="px-3 fw-bold">{{ $dueDate->format('d M Y') }}</td>
                                <td class="px-3">
                                    @if($isOverdue)
                                        <span class="badge bg-danger">Terlambat</span>
                                    @else
                                        <span class="badge bg-primary">Dipinjam</span>
                                    @endif
                                </td>
                                <td class="px-3 text-end">
                                    {{-- ========================================================== --}}
                                    {{-- PERBAIKAN: Menambahkan Tombol Tandai Hilang --}}
                                    {{-- ========================================================== --}}
                                    <div class="d-flex justify-content-end gap-2">
                                        {{-- Tombol Kembalikan --}}
                                        <form action="{{ route('admin.petugas.returns.store', $borrow) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian buku ini?');" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm" title="Proses Pengembalian">Kembalikan</button>
                                        </form>
                                        
                                        {{-- Tombol Tandai Hilang --}}
                                        <form action="{{ route('admin.petugas.returns.markAsLost', $borrow) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menandai buku ini HILANG? Denda penggantian akan diterapkan.');" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-warning btn-sm text-dark" title="Tandai Buku Hilang">
                                                 <i class="bi bi-exclamation-triangle-fill"></i> Hilang
                                            </button>
                                        </form>
                                    </div>
                                    {{-- ========================================================== --}}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-check2-all d-block display-4 opacity-25"></i>Tidak ada buku yang sedang dipinjam.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script Select All tidak diubah, sudah benar --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllHeader = document.getElementById('selectAll');
        const bulkReturnForm = document.getElementById('bulk-return-form');
        const btnReturnMultiple = document.getElementById('btn-return-multiple');
        const borrowingCheckboxes = document.querySelectorAll('.borrowing-checkbox');
        const csrfToken = document.querySelector('input[name="_token"]').value;

        function updateFormAndButton() {
            bulkReturnForm.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="PUT">
            `;
            
            let checkedCount = 0;
            borrowingCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'borrowing_ids[]';
                    hiddenInput.value = checkbox.value;
                    bulkReturnForm.appendChild(hiddenInput);
                    checkedCount++;
                }
            });
            
            btnReturnMultiple.disabled = checkedCount === 0;
        }

        function syncControls() {
            const total = borrowingCheckboxes.length;
            const checked = document.querySelectorAll('.borrowing-checkbox:checked').length;
            
            selectAllHeader.checked = total > 0 && total === checked;
            selectAllHeader.indeterminate = checked > 0 && checked < total;
        }

        selectAllHeader.addEventListener('change', function() {
            borrowingCheckboxes.forEach(cb => cb.checked = this.checked);
            updateFormAndButton();
        });

        borrowingCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                syncControls();
                updateFormAndButton();
            });
        });
        
        // Initial state
        updateFormAndButton();
        syncControls();
    });
</script>
@endpush

