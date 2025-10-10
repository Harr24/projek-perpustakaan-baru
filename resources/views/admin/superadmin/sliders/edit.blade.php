@extends('layouts.app') {{-- Sesuaikan dengan layout admin Anda --}}

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Edit Slider</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.superadmin.sliders.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul (Opsional)</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $slider->title) }}" class="form-control @error('title') is-invalid @enderror">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Gambar Saat Ini</label>
                            <img src="{{ Storage::url($slider->image_path) }}" alt="{{ $slider->title }}" style="width: 250px; height: auto; border-radius: 4px;" class="mb-2">
                        </div>

                        <div class="mb-3">
                            <label for="image_path" class="form-label">Ganti Gambar (Opsional)</label>
                            <input type="file" id="image_path" name="image_path" class="form-control @error('image_path') is-invalid @enderror" accept="image/*">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah gambar. Rekomendasi: 1200x400 pixels.</div>
                            @error('image_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="link_url" class="form-label">URL Link (Opsional)</label>
                            <input type="url" id="link_url" name="link_url" value="{{ old('link_url', $slider->link_url) }}" class="form-control @error('link_url') is-invalid @enderror" placeholder="https://contoh.com/halaman-promo">
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $slider->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktifkan Slider Ini</label>
                        </div>
                        
                        <div class="d-flex gap-2">
                             <button type="submit" class="btn btn-danger">Update Slider</button>
                             <a href="{{ route('admin.superadmin.sliders.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection