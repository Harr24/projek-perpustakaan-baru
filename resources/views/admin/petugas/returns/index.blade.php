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
            <a href="{{ route('admin.petugas.fines.index') }}" class="btn btn-warning btn-sm text-dark fw-bold">
                <i class="bi bi-cash-coin"></i> Lihat Daftar Denda
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
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
    <form action="{{ route('admin.petugas.returns.storeMultiple') }}" method="POST" id="bulk-return-form" onsubmit="return confirm('Anda yakin ingin MENGEMBALIKAN semua buku yang dipilih?');">
        @csrf
        @method('PUT')
        {{-- Input tersembunyi akan ditambahkan oleh JavaScript --}}
    </form>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-danger text-white d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 py-3">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-journal-arrow-up me-2"></i> Buku Sedang Dipinjam</h5>

            {{-- Form Pencarian --}}
            <div class="d-flex gap-2 w-100 w-md-auto">
                <form action="{{ route('admin.petugas.returns.index') }}" method="GET" class="d-flex flex-grow-1">
                    <div class="input-group input-group-sm">
                        <input type="search" name="search" class="form-control border-0" placeholder="Cari peminjam / buku..." value="{{ $search ?? '' }}">
                        @if(isset($search) && $search)
                            <a href="{{ route('admin.petugas.returns.index') }}" class="btn btn-light text-danger" title="Hapus Filter"><i class="bi bi-x-lg"></i></a>
                        @endif
                        <button class="btn btn-light text-danger" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <button type="submit" form="bulk-return-form" class="btn btn-light btn-sm fw-bold text-danger" id="btn-return-multiple" disabled>
                    <i class="bi bi-check2-all"></i> Kembalikan Dipilih
                </button>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small uppercase">
                        <tr>
                            <th class="py-3 px-3 text-center" style="width: 5%;">
                                <input class="form-check-input" type="checkbox" id="selectAll" style="cursor: pointer;">
                            </th>
                            <th class="py-3 px-3">Buku</th>
                            <th class="py-3 px-3">Peminjam</th>
                            <th class="py-3 px-3">Kelas</th>
                            <th class="py-3 px-3">Kontak</th>
                            <th class="py-3 px-3">Jatuh Tempo</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-end" style="min-width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @php $currentUserId = null; @endphp
                        @forelse ($activeBorrowings as $borrow)
                            @php
                                // --- Logika Pelacak User ---
                                $isNewUser = $borrow->user_id !== $currentUserId;
                                $currentUserId = $borrow->user_id;

                                // --- Logika Jatuh Tempo ---
                                $officialDueDate = $borrow->due_date; 
                                $bookType = $borrow->bookCopy->book->book_type;
                                $displayDate = 'N/A';
                                $isOverdue = false;
                                $userRole = $borrow->user->role ?? 'siswa';
                                
                                if ($officialDueDate) {
                                    $dueDateCarbon = \Carbon\Carbon::parse($officialDueDate);
                                    $displayDate = $dueDateCarbon->format('d M Y');
                                    $isOverdue = now()->startOfDay()->isAfter($dueDateCarbon);
                                } else if ($bookType == 'laporan' || $bookType == 'paket_semester') {
                                    $displayDate = '∞'; // Simbol Infinity untuk tanpa batas
                                    $isOverdue = false;
                                }
                                
                                if ($userRole === 'guru') {
                                    $isOverdue = false;
                                    $displayDate = '∞';
                                }
                            @endphp
                        
                            <tr class="{{ $isOverdue ? 'table-danger bg-opacity-10' : '' }} transition-colors">
                                <td class="px-3 text-center">
                                    <input class="form-check-input borrowing-checkbox" 
                                           type="checkbox" 
                                           value="{{ $borrow->id }}" 
                                           data-user-id="{{ $borrow->user_id }}"
                                           id="borrowing-{{ $borrow->id }}"
                                           style="cursor: pointer;">
                                </td>
                                
                                {{-- 🔥 KOLOM BUKU DENGAN COVER (UPDATE UTAMA) 🔥 --}}
                                <td class="px-3">
                                    <div class="d-flex align-items-center">
                                        {{-- Cover Image --}}
                                        <div class="flex-shrink-0 me-3 position-relative" style="width: 40px; height: 55px;">
                                            @if(isset($borrow->bookCopy->book->cover_image))
                                                <img src="{{ asset('storage/' . $borrow->bookCopy->book->cover_image) }}" 
                                                     alt="Cover" 
                                                     class="w-100 h-100 rounded-1 border shadow-sm"
                                                     style="object-fit: cover;">
                                            @else
                                                <div class="w-100 h-100 bg-light border rounded-1 d-flex align-items-center justify-content-center text-muted small">
                                                    <i class="bi bi-book"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        {{-- Judul & Kode --}}
                                        <div>
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 200px;" title="{{ $borrow->bookCopy->book->title }}">
                                                {{ $borrow->bookCopy->book->title }}
                                            </div>
                                            <small class="d-block text-muted font-monospace" style="font-size: 0.8rem;">
                                                {{ $borrow->bookCopy->book_code }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                {{-- 🔥 AKHIR UPDATE KOLOM BUKU 🔥 --}}
                                
                                <td class="px-3">
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium text-dark">{{ $borrow->user->name }}</span>
                                        @if ($isNewUser)
                                            <div class="form-check form-check-inline m-0 mt-1">
                                                <input class="form-check-input check-all-user small-checkbox" 
                                                       type="checkbox" 
                                                       data-user-id="{{ $borrow->user_id }}" 
                                                       id="user-{{ $borrow->user_id }}-check">
                                                <label class="form-check-label small text-primary" for="user-{{ $borrow->user_id }}-check" style="font-size: 0.75rem; cursor: pointer;">
                                                    Pilih Semua
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-3">
                                    @if($borrow->user->role == 'guru')
                                        <span class="badge bg-info text-dark">Guru</span>
                                    @elseif($borrow->user->class == 'Lulus')
                                        <span class="badge bg-secondary">Lulus</span>
                                    @elseif(!empty($borrow->user->class) && !empty($borrow->user->major))
                                        <span class="small">{{ $borrow->user->class }} {{ $borrow->user->major }}</span>
                                    @elseif(!empty($borrow->user->class_name))
                                        <span class="small">{{ $borrow->user->class_name }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <td class="px-3">
                                    @if($borrow->user->phone_number)
                                        @php
                                            $cleanedPhone = preg_replace('/[^0-9]/', '', $borrow->user->phone_number);
                                            $waNumber = '62' . ltrim($cleanedPhone, '0');
                                        @endphp
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 0.8rem;" title="Chat WA">
                                            <i class="bi bi-whatsapp"></i> Chat
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                
                                <td class="px-3">
                                    @if($isOverdue)
                                        <span class="text-danger fw-bold small">
                                            {{ $displayDate }}
                                            <i class="bi bi-exclamation-circle-fill ms-1" title="Terlambat"></i>
                                        </span>
                                    @else
                                        <span class="text-dark small fw-medium">{{ $displayDate }}</span>
                                    @endif
                                </td>
                                
                                <td class="px-3 text-center">
                                    @if($isOverdue)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Terlambat</span>
                                    @else
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">Dipinjam</span>
                                    @endif
                                </td>

                                <td class="px-3 text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <form action="{{ route('admin.petugas.returns.store', $borrow) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian buku ini?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm" title="Kembalikan">
                                                <i class="bi bi-check-lg"></i> <span class="d-none d-xl-inline">Kembali</span>
                                            </button>
                                        </form>
                                        
                                        @if($bookType != 'laporan' && $bookType != 'paket_semester')
                                        <form action="{{ route('admin.petugas.returns.markAsLost', $borrow) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menandai buku ini HILANG?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline-warning btn-sm text-dark" title="Hilang">
                                                <i class="bi bi-exclamation-triangle"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-journal-check display-4 opacity-25 mb-2"></i>
                                        <p class="mb-0">Tidak ada buku yang sedang dipinjam saat ini.</p>
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
    const bulkReturnForm = document.getElementById('bulk-return-form');
    const btnReturnMultiple = document.getElementById('btn-return-multiple');

    // Pastikan elemen form ada sebelum mencoba mengakses isinya
    if (!bulkReturnForm) return;

    const csrfInput = bulkReturnForm.querySelector('input[name="_token"]');
    const csrfToken = csrfInput ? csrfInput.value : '';

    function updateFormPayloadAndButton() {
        // Reset isi form (tetap simpan token dan method)
        bulkReturnForm.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="PUT">
        `;

        let checkedCount = 0;

        document.querySelectorAll('.borrowing-checkbox:checked').forEach(checkbox => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'borrowing_ids[]';
            hidden.value = checkbox.value;
            bulkReturnForm.appendChild(hidden);
            checkedCount++;
        });

        if (btnReturnMultiple) {
            btnReturnMultiple.disabled = checkedCount === 0;
            // Update text tombol agar user tahu berapa yang dipilih
            const originalText = '<i class="bi bi-check2-all"></i> Kembalikan yang Dipilih';
            btnReturnMultiple.innerHTML = checkedCount > 0 ? 
                `<i class="bi bi-check2-all"></i> Kembalikan (${checkedCount})` : originalText;
        }
    }

    function syncControlCheckboxes() {
        const total = document.querySelectorAll('.borrowing-checkbox').length;
        const checked = document.querySelectorAll('.borrowing-checkbox:checked').length;

        if (selectAllHeader) {
            selectAllHeader.checked = total > 0 && total === checked;
            selectAllHeader.indeterminate = checked > 0 && checked < total;
        }

        document.querySelectorAll('.check-all-user').forEach(userCheck => {
            const userId = userCheck.dataset.userId;
            const related = document.querySelectorAll(`.borrowing-checkbox[data-user-id="${userId}"]`);
            
            const totalUser = related.length;
            const checkedUser = document.querySelectorAll(`.borrowing-checkbox[data-user-id="${userId}"]:checked`).length;

            userCheck.checked = totalUser > 0 && totalUser === checkedUser;
            userCheck.indeterminate = checkedUser > 0 && checkedUser < totalUser;
        });
    }

    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function () {
            const checked = this.checked;
            document.querySelectorAll('.borrowing-checkbox').forEach(cb => {
                cb.checked = checked;
            });
            syncControlCheckboxes();
            updateFormPayloadAndButton();
        });
    }

    document.body.addEventListener('change', function(e) {
        if (e.target.classList.contains('check-all-user')) {
            const userId = e.target.dataset.userId;
            const checked = e.target.checked;
            document.querySelectorAll(`.borrowing-checkbox[data-user-id="${userId}"]`)
                .forEach(cb => cb.checked = checked);
            
            syncControlCheckboxes();
            updateFormPayloadAndButton();
        }

        if (e.target.classList.contains('borrowing-checkbox')) {
            syncControlCheckboxes();
            updateFormPayloadAndButton();
        }
    });

    // Initial sync
    updateFormPayloadAndButton();
    syncControlCheckboxes();
});
</script>
@endpush