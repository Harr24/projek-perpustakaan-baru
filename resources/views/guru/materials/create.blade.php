@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h1 class="h3 fw-bold mb-4">Tambah Materi Baru</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('guru.materials.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Judul Materi</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="link_url" class="form-label">URL Link Materi</label>
                    <input type="url" name="link_url" id="link_url" class="form-control" placeholder="https://youtube.com/..." required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi (Opsional)</label>
                    <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Materi</button>
                <a href="{{ route('guru.materials.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
