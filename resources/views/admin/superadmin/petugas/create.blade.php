@extends('layouts.app') {{-- Menggunakan layout utama --}}

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2" style="color: #d9534f;">Tambah Akun Petugas Baru</h1>
            <p class="text-muted mb-0 small">Masukkan detail untuk akun petugas baru.</p>
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

    <div class="card shadow-sm border-start border-danger border-4">
        <div class="card-body">
            <form action="{{ route('admin.superadmin.petugas.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                {{-- Nama --}}
                <div class="mb-3">
                    <label for="name" class="form-label required">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap petugas">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label required">Alamat Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="Contoh: petugas@email.com">
                     @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                     @else
                         <div class="form-text">Pastikan email unik dan valid.</div>
                    @enderror
                </div>

                {{-- ========================================================== --}}
                {{-- PERUBAHAN: Role diset otomatis ke 'petugas' --}}
                {{-- ========================================================== --}}
                {{-- Input Role (tersembunyi, otomatis 'petugas') --}}
                <input type="hidden" name="role" value="petugas">
                {{-- Menampilkan Role (hanya teks, tidak bisa diubah) --}}
                <div class="mb-3">
                    <label class="form-label">Role Akun</label>
                    <input type="text" class="form-control" value="Petugas" disabled readonly>
                    <div class="form-text">Akun yang dibuat melalui form ini akan otomatis memiliki role Petugas.</div>
                </div>
                {{-- ========================================================== --}}


                {{-- Password --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label required">Password</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Minimal 8 karakter">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label required">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Ulangi password">
                        {{-- Error konfirmasi biasanya ditangani oleh validasi 'confirmed' --}}
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-save me-1"></i> Simpan Akun Petugas
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
{{-- Script untuk validasi form Bootstrap (jika belum ada di layout utama) --}}
<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function () {
      'use strict'

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.querySelectorAll('.needs-validation')

      // Loop over them and prevent submission
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
