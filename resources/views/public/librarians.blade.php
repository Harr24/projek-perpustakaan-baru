@extends('layouts.public')

@section('content')

{{-- Style khusus untuk halaman ini --}}
<style>
    .profile-card {
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
    }
    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    .profile-photo {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    .profile-details {
        font-size: 0.95rem;
    }
    .profile-details .label {
        font-weight: 600;
        min-width: 80px; /* Agar posisi titik dua (:) rapi */
        display: inline-block;
    }
</style>

<div class="container py-5">
    <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-outline-secondary me-2">
        <i class="bi bi-house-door-fill"></i> Kembali ke Beranda
    </a>
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">Profil Pustakawan & Staf</h1>
        <p class="lead text-muted">Kenali lebih dekat tim di balik Perpustakaan SMK Multicomp Depok.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @forelse ($staff as $person)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100 profile-card">
                    <div class="card-body p-4 d-flex align-items-center">
                        {{-- Bagian Foto --}}
                        <div class="flex-shrink-0 me-4">
                            @if($person->profile_photo)
                                <img src="{{ asset('storage/' . $person->profile_photo) }}" alt="Foto {{ $person->name }}" class="profile-photo">
                            @else
                                {{-- Fallback jika tidak ada foto, tampilkan inisial --}}
                                <div class="profile-photo d-flex align-items-center justify-content-center bg-danger text-white fs-1 fw-bold">
                                    {{ strtoupper(substr($person->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        {{-- Bagian Detail Teks --}}
                        <div class="profile-details">
                            <h5 class="fw-bold mb-2">{{ $person->name }}</h5>
                            <p class="mb-1"><span class="label">Jabatan</span>: 
                                @if($person->role == 'petugas')
                                    Pustakawan
                                @elseif($person->role == 'guru')
                                    Guru
                                @endif
                            </p>
                            <p class="mb-0"><span class="label">Surel</span>: {{ $person->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    Data pustakawan dan staf belum tersedia.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection