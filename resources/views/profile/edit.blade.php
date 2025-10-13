@extends('layouts.app') {{-- Sesuaikan dengan layout utamamu --}}

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Edit Profil Saya</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Kembali ke Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Bagian Foto Profil --}}
                    <div class="col-md-4 text-center">
                        <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : 'https://placehold.co/150x150/6c757d/FFFFFF?text=' . strtoupper(substr($user->name, 0, 1)) }}" 
                             alt="Foto Profil" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Ubah Foto Profil</label>
                            <input class="form-control form-control-sm @error('profile_photo') is-invalid @enderror" type="file" id="profile_photo" name="profile_photo">
                            @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Bagian Data Diri --}}
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                             @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ========================================================== --}}
                        {{-- PERUBAHAN DI SINI: Tambahkan Input Nomor WhatsApp --}}
                        {{-- ========================================================== --}}
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Nomor WhatsApp</label>
                            <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="Contoh: 081234567890">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kelas (HANYA UNTUK SISWA) --}}
                        @if ($user->role === 'siswa')
                            <div class="mb-3">
                                <label for="class_name" class="form-label">Kelas</label>
                                <input type="text" class="form-control @error('class_name') is-invalid @enderror" id="class_name" name="class_name" value="{{ old('class_name', $user->class_name) }}" placeholder="Contoh: XII RPL 1">
                                <div class="form-text">Mohon isi kelas Anda saat ini.</div>
                                @error('class_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <hr class="my-4">

                        {{-- Bagian Ubah Password --}}
                        <h5 class="mb-3">Ubah Password (Opsional)</h5>
                        <p class="text-muted small">Kosongkan kolom di bawah ini jika Anda tidak ingin mengubah password.</p>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                             @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-danger">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection