@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">Profil Saya</h1>
                    <p class="text-muted mb-0">Informasi akun Anda di sistem perpustakaan.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="Foto Profil" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <div class="col-md-8">
                            <h3 class="fw-bold">{{ Auth::user()->name }}</h3>
                            <p class="text-muted">
                                @if(Auth::user()->role === 'siswa')
                                    Siswa
                                @elseif(Auth::user()->role === 'guru')
                                    Guru
                                @else
                                    Anggota
                                @endif
                            </p>
                            <hr>
                            {{-- ========================================================== --}}
                            {{-- PERUBAHAN DI SINI: Tampilkan NISN --}}
                            {{-- ========================================================== --}}
                            @if (Auth::user()->role === 'siswa')
                            <div class="row mt-2">
                                <div class="col-sm-4"><strong class="text-muted">NISN</strong></div>
                                <div class="col-sm-8">{{ Auth::user()->nis ?: '-' }}</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-4"><strong class="text-muted">Kelas</strong></div>
                                <div class="col-sm-8">{{ Auth::user()->class_name ?: '-' }}</div>
                            </div>
                            @endif
                            <div class="row mt-2">
                                <div class="col-sm-4"><strong class="text-muted">Email</strong></div>
                                <div class="col-sm-8">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-4"><strong class="text-muted">Nomor WhatsApp</strong></div>
                                <div class="col-sm-8">{{ Auth::user()->phone_number ?: '-' }}</div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('profile.edit') }}" class="btn btn-danger">
                                    <i class="bi bi-pencil-square me-2"></i> Edit Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

