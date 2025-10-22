@extends('layouts.app') {{-- Menggunakan layout utama --}}

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2" style="color: #d9534f;">Edit Akun Petugas: {{ $petugas->name }}</h1>
            <p class="text-muted mb-0 small">Perbarui detail akun petugas.</p>
        </div>
        <a href="{{ route('admin.superadmin.petugas.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Petugas
        </a>
    </div>

    {{-- Menampilkan Error Validasi --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i> Terjadi Kesalahan</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
     {{-- Notifikasi Sukses (jika ada setelah update) --}}
     @if(session('success'))
     <div class="alert alert-success alert-dismissible fade show" role="alert">
         <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
     @endif

    <div class="card shadow-sm border-start border-danger border-4">
        <div class="card-body">
            <form action="{{ route('admin.superadmin.petugas.update', $petugas->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div class="mb-3">
                    <label for="name" class="form-label required">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $petugas->name) }}" required placeholder="Masukkan nama lengkap petugas">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label required">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $petugas->email) }}" required placeholder="Contoh: petugas@email.com">
                     @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                     @else
                         <div class="form-text">Pastikan email unik dan valid.</div>
                    @enderror
                </div>

                {{-- ========================================================== --}}
                {{-- PERUBAHAN: Role diset otomatis ke 'petugas' (tidak bisa diubah) --}}
                {{-- ========================================================== --}}
                <input type="hidden" name="role" value="petugas">
                <div class="mb-3">
                    <label class="form-label">Role Akun</label>
                    <input type="text" class="form-control" value="Petugas" disabled readonly>
                    <div class="form-text">Role untuk akun Petugas tidak dapat diubah di sini.</div>
                </div>
                {{-- ========================================================== --}}

                {{-- Password (Opsional) --}}
                 <hr class="my-4">
                 <p class="text-muted mb-3 small">Kosongkan field password jika tidak ingin mengubahnya.</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Isi hanya jika ingin ganti">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex gap-2 pt-3 border-top mt-4">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-save me-1"></i> Update Akun Petugas
                    </button>
                    <a href="{{ route('admin.superadmin.petugas.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk validasi form Bootstrap --}}
<script>
    (function () {
      'use strict'
      var forms = document.querySelectorAll('.needs-validation')
      Array.prototype.slice.call(forms)
        .forEach(function (form) {
          form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }
            form.classList.add('was-validated')
          }, false)
        })
    })()
</script>
@endpush
