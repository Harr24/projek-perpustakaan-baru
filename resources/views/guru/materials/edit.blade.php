@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h1 class="h3 fw-bold mb-4">Edit Materi</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('guru.materials.update', $material) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="title" class="form-label">Judul Materi</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ $material->title }}" required>
                </div>
                <div class="mb-3">
                    <label for="link_url" class="form-label">URL Link Materi</label>
                    <input type="url" name="link_url" id="link_url" class="form-control" value="{{ $material->link_url }}" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi (Opsional)</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ $material->description }}</textarea>
                </div>
                 <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $material->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Aktifkan (tampilkan di halaman publik)
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Update Materi</button>
                <a href="{{ route('guru.materials.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
