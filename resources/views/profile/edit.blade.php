@extends('layouts.app') 

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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-4">
            {{-- FORM UPDATE UTAMA --}}
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Bagian Foto Profil --}}
                    <div class="col-md-4 text-center">
                        {{-- Tampilan Foto --}}
                        <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : 'https://placehold.co/150x150/6c757d/FFFFFF?text=' . strtoupper(substr($user->name, 0, 1)) }}" 
                             alt="Foto Profil" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        
                        {{-- ========================================================== --}}
                        {{-- 🔥 LOGIKA BARU: HAPUS DULU BARU BISA UPLOAD 🔥 --}}
                        {{-- ========================================================== --}}
                        
                        @if($user->profile_photo)
                            {{-- KONDISI 1: SUDAH ADA FOTO --}}
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="confirmDeletePhoto()">
                                    <i class="bi bi-trash me-1"></i> Hapus Foto Saat Ini
                                </button>
                                
                                <div class="alert alert-warning mt-3 py-2 small text-start">
                                    <i class="bi bi-info-circle-fill me-1"></i> 
                                    Ingin mengganti foto? Silakan <strong>hapus foto saat ini</strong> terlebih dahulu, lalu kolom upload akan muncul.
                                </div>
                            </div>

                        @else
                            {{-- KONDISI 2: BELUM ADA FOTO (Input File Muncul Disini) --}}
                            <div class="mb-3 animate__animated animate__fadeIn">
                                <label for="profile_photo" class="form-label fw-bold text-primary">
                                    <i class="bi bi-upload me-1"></i> Upload Foto Baru
                                </label>
                                <input class="form-control form-control-sm @error('profile_photo') is-invalid @enderror" type="file" id="profile_photo" name="profile_photo">
                                @error('profile_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        {{-- ========================================================== --}}

                    </div>

                    {{-- Bagian Data Diri --}}
                    <div class="col-md-8">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" disabled>
                            <div class="form-text">Nama tidak dapat diubah. Hubungi petugas untuk pembaruan data.</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" disabled>
                            <div class="form-text">Email tidak dapat diubah. Hubungi petugas untuk pembaruan data.</div>
                        </div>

                        {{-- Nomor WhatsApp --}}
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Nomor WhatsApp</label>
                            <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="Contoh: 081234567890">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kelas (HANYA UNTUK SISWA & DIKUNCI) --}}
                        @if ($user->role === 'siswa')
                            <div class="mb-3">
                                <label for="class" class="form-label">Kelas</label>
                                <select class="form-select" id="class" name="class" disabled>
                                    <option value="X" {{ old('class', $user->class) == 'X' ? 'selected' : '' }}>X</option>
                                    <option value="XI" {{ old('class', $user->class) == 'XI' ? 'selected' : '' }}>XI</option>
                                    <option value="XII" {{ old('class', $user->class) == 'XII' ? 'selected' : '' }}>XII</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="major" class="form-label">Jurusan</label>
                                <select class="form-select" id="major" name="major" disabled>
                                    @if(isset($majors))
                                        @foreach($majors as $major)
                                            <option value="{{ $major->name }}" {{ old('major', $user->major) == $major->name ? 'selected' : '' }}>
                                                {{ $major->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ $user->major }}" selected>{{ $user->major }}</option>
                                    @endif
                                </select>
                                <div class="form-text">Kelas & Jurusan tidak dapat diubah. Hubungi petugas untuk pembaruan data.</div>
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

{{-- FORM TERSEMBUNYI UNTUK HAPUS FOTO --}}
<form id="delete-photo-form" action="{{ route('profile.photo.delete') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDeletePhoto() {
        if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
            document.getElementById('delete-photo-form').submit();
        }
    }
</script>

@endsection