{{-- Halaman ini akan menampilkan daftar materi yang sudah dibuat guru --}}
@extends('layouts.app')

@section('styles')
<style>
    /* Style untuk memaksa teks panjang (seperti URL) agar pindah baris */
    .word-break {
        word-break: break-all;
    }
    /* Memberi lebar maksimal pada kolom judul agar tidak terlalu dominan */
    .table th.title-col {
        width: 60%;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">Kelola Materi Pembelajaran</h1>
            <p class="text-muted mb-0">Tambah, edit, atau hapus materi Anda.</p>
        </div>
        {{-- ========================================================== --}}
        {{-- PERUBAHAN DI SINI: Tombol navigasi ditambahkan --}}
        {{-- ========================================================== --}}
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard</a>
            <a href="{{ route('guru.materials.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-2"></i> Tambah Materi Baru</a>
        </div>
        {{-- ========================================================== --}}
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="title-col">Judul</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materials as $material)
                            <tr>
                                <td>
                                    <strong class="d-block">{{ $material->title }}</strong>
                                    <small class="d-block text-muted word-break">
                                        <a href="{{ $material->link_url }}" target="_blank" rel="noopener noreferrer" class="text-reset">
                                            {{ Str::limit($material->link_url, 70) }}
                                        </a>
                                    </small>
                                </td>
                                <td>
                                    @if($material->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('guru.materials.edit', $material) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('guru.materials.destroy', $material) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus materi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Anda belum menambahkan materi apa pun.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if ($materials->hasPages())
            <div class="card-footer bg-transparent border-0">
                {{ $materials->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

