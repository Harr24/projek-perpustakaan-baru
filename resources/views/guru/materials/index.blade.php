{{-- Halaman ini akan menampilkan daftar materi yang sudah dibuat guru --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold">Kelola Materi Pembelajaran</h1>
            <p class="text-muted">Tambah, edit, atau hapus materi Anda.</p>
        </div>
        <a href="{{ route('guru.materials.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Materi Baru</a>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materials as $material)
                        <tr>
                            <td>
                                {{ $material->title }}
                                <small class="d-block text-muted">{{ $material->link_url }}</small>
                            </td>
                            <td>
                                @if($material->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
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
                            <td colspan="3" class="text-center">Anda belum menambahkan materi apa pun.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $materials->links() }}
        </div>
    </div>
</div>
@endsection
