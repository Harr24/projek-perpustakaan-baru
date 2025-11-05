@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2" style="color: #6f42c1;">Manajemen Tanggal Merah</h1>
            <p class="text-muted mb-0 small">Kelola daftar hari libur nasional dan cuti bersama.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Tampilkan Error Validasi (Berlaku untuk 'Tambah' dan 'Edit') --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading h6"><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal Memproses!</h5>
        <p class="mb-2 small">Terdapat kesalahan pada data yang Anda masukkan:</p>
        <ul class="mb-0 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">
        {{-- Kolom Kiri: Form Tambah Tanggal Merah --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white" style="background-color: #6f42c1;">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-calendar-plus me-2"></i> Tambah Tanggal Merah</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.superadmin.holidays.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="holiday_date" class="form-label fw-bold">Tanggal</label>
                            <input type="date" class="form-control" id="holiday_date" name="holiday_date" value="{{ old('holiday_date') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Keterangan</label>
                            <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}" placeholder="Contoh: Hari Kemerdekaan RI" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary fw-bold" style="background-color: #6f42c1; border: none;">
                                <i class="bi bi-save me-1"></i> Simpan Tanggal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Tanggal Merah --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-calendar-check me-2"></i> Daftar Tanggal Merah</h5>
                    
                    {{-- Form Filter Tahun --}}
                    <form action="{{ route('admin.superadmin.holidays.index') }}" method="GET" class="d-flex gap-2">
                        <select name="year" class="form-select form-select-sm" style="width: 120px;">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                    Tahun {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 px-3">Tanggal</th>
                                    <th class="py-3 px-3">Keterangan</th>
                                    <th class="py-3 px-3 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($holidays as $holiday)
                                <tr>
                                    <td class="px-3">
                                        <span class="fw-bold">{{ $holiday->holiday_date->format('d M Y') }}</span>
                                        <small class="d-block text-muted">{{ $holiday->holiday_date->format('l') }}</small>
                                    </td>
                                    <td class="px-3">{{ $holiday->description }}</td>
                                    <td class="px-3 text-end">
                                        
                                        {{-- ============================================= --}}
                                        {{-- --- TOMBOL BARU: Edit (Membuka Modal) --- --}}
                                        {{-- ============================================= --}}
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-edit" 
                                                title="Edit"
                                                data-id="{{ $holiday->id }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editHolidayModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Tombol Hapus (Form) --}}
                                        <form action="{{ route('admin.superadmin.holidays.destroy', $holiday) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus tanggal merah ini?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        Tidak ada data tanggal merah untuk tahun {{ $selectedYear }}.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ============================================= --}}
{{-- --- MODAL BARU: Edit Tanggal Merah --- --}}
{{-- ============================================= --}}
<div class="modal fade" id="editHolidayModal" tabindex="-1" aria-labelledby="editHolidayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="editHolidayModalLabel">Edit Tanggal Merah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            {{-- Form Edit akan diisi oleh JavaScript --}}
            <form id="editHolidayForm" method="POST"> 
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_holiday_date" class="form-label fw-bold">Tanggal</label>
                        {{-- Nama input harus 'edit_holiday_date' agar sesuai validasi di controller --}}
                        <input type="date" class="form-control" id="edit_holiday_date" name="edit_holiday_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label fw-bold">Keterangan</label>
                        {{-- Nama input harus 'edit_description' --}}
                        <input type="text" class="form-control" id="edit_description" name="edit_description" placeholder="Contoh: Hari Kemerdekaan RI" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #6f42c1; border: none;">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- ============================================= --}}
{{-- --- JAVASCRIPT BARU: Untuk Modal Edit --- --}}
{{-- ============================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi modal Bootstrap
        var editModalElement = document.getElementById('editHolidayModal');
        var editModal = new bootstrap.Modal(editModalElement);
        
        var editForm = document.getElementById('editHolidayForm');
        var editDateInput = document.getElementById('edit_holiday_date');
        var editDescInput = document.getElementById('edit_description');

        // Tambahkan event listener ke SEMUA tombol edit
        document.querySelectorAll('.btn-edit').forEach(function(button) {
            button.addEventListener('click', function() {
                var holidayId = this.getAttribute('data-id');
                
                // 1. Atur 'action' untuk form di dalam modal
                // Ini akan mengarahkan form ke route update yang benar, misal: /admin/superadmin/holidays/5
                var updateUrl = "{{ url('admin/superadmin/holidays') }}/" + holidayId;
                editForm.setAttribute('action', updateUrl);
                
                // 2. Buat URL untuk mengambil data (route 'edit')
                var editUrl = "{{ url('admin/superadmin/holidays') }}/" + holidayId + "/edit";
                
                // 3. Ambil data dari server menggunakan fetch()
                fetch(editUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal mengambil data');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // 4. Isi form di dalam modal dengan data yang didapat
                        editDateInput.value = data.holiday_date;
                        editDescInput.value = data.description;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Tidak dapat memuat data. Silakan coba lagi.');
                        // Sembunyikan modal jika gagal
                        editModal.hide();
                    });
            });
        });

        // Opsional: Bersihkan form saat modal ditutup
        editModalElement.addEventListener('hidden.bs.modal', function () {
            editForm.reset();
            editForm.setAttribute('action', ''); // Hapus action URL lama
        });

        // Cek jika ada error validasi edit (dari $errors->any())
        // Jika ada, kita buka modal secara otomatis untuk menunjukkan error
        @if ($errors->has('edit_holiday_date') || $errors->has('edit_description'))
            // Dapatkan URL 'action' terakhir (jika tersimpan di old input, atau perlu cara lain)
            // Cara termudah adalah membuka modal jika ada error,
            // tapi mengisi data lamanya mungkin perlu penyesuaian
            // Untuk saat ini, kita biarkan user membuka manual lagi
            // alert('Terdapat error pada editan terakhir. Silakan cek pesan error di atas.');
            
            // Cara lebih canggih: Buka modal terakhir yang diedit
            // Ini sulit dilakukan tanpa tahu ID terakhir yang diedit
            // Jadi, kita biarkan validasi di atas halaman saja.
        @endif

    });
</script>