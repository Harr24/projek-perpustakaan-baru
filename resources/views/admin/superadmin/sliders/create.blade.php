@extends('layouts.app') {{-- Sesuaikan dengan layout admin Anda --}}

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Tambah Slider Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.superadmin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul (Opsional)</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror">
                            <div class="form-text">Teks singkat yang akan muncul di atas gambar (jika didukung tema).</div>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image_path" class="form-label">Gambar Slider</label>
                            <input type="file" id="image_path" name="image_path" class="form-control @error('image_path') is-invalid @enderror" required accept="image/*">
                            <div class="form-text">Rekomendasi ukuran: 1200x400 pixels. Maksimal 2MB.</div>
                            @error('image_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="link_url" class="form-label">URL Link (Opsional)</label>
                            <input type="url" id="link_url" name="link_url" value="{{ old('link_url') }}" class="form-control @error('link_url') is-invalid @enderror" placeholder="https://contoh.com/halaman-promo">
                            <div class="form-text">Jika diisi, gambar slider akan bisa diklik untuk menuju ke halaman ini.</div>
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Aktifkan Slider Ini</label>
                            <div class="form-text">Hilangkan centang untuk menyembunyikan slider ini dari halaman depan.</div>
                        </div>
                        
                        <div class="d-flex gap-2">
                             <button type="submit" class="btn btn-danger">Simpan Slider</button>
                             <a href="{{ route('admin.superadmin.sliders.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection