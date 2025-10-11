@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div>
                <h1 class="h3 fw-bold mb-2 page-title">Edit Akun Guru</h1>
                <p class="text-muted mb-0 small">Ubah informasi akun untuk guru: <strong class="text-dark">{{ $teacher->name }}</strong></p>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-pencil-square me-2 text-danger"></i>
                Formulir Perubahan Data
            </h5>
        </div>
        
        <div class="card-body">
            <form action="{{ route('admin.petugas.teachers.update', $teacher) }}" method="POST">
                @csrf
                @method('PUT') {{-- Wajib untuk metode update --}}

                {{-- Nama Lengkap --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $teacher->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Alamat Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $teacher->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mata Pelajaran --}}
                <div class="mb-3">
                    <label for="subject" class="form-label">Mata Pelajaran</label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject', $teacher->subject) }}" required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <hr class="my-4">

                <p class="text-muted mb-2">
                    <i class="bi bi-key-fill me-1"></i> <strong>Ubah Password (Opsional)</strong>
                </p>
                <small class="d-block text-muted mb-3">Kosongkan kolom password jika Anda tidak ingin mengubahnya.</small>

                {{-- Password Baru --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- Konfirmasi Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.petugas.teachers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection