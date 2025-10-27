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

                        {{-- Judul Slider --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul (Opsional)</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $slider->title) }}" class="form-control @error('title') is-invalid @enderror">
                            <div class="form-text">Teks singkat yang akan muncul di atas gambar.</div>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi (Opsional)</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $slider->description) }}</textarea>
                            <div class="form-text">Teks penjelasan yang akan muncul di bawah judul.</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Gambar Saat Ini --}}
                        @if($slider->image_path)
                        <div class="mb-3">
                            <label class="form-label d-block">Gambar Saat Ini</label>
                            <img src="{{ asset('storage/' . $slider->image_path) }}" alt="{{ $slider->title }}" style="width: 250px; height: auto; border-radius: 4px;" class="mb-2">
                        </div>
                        @endif

                        {{-- Upload Gambar --}}
                        <div class="mb-3">
                            <label for="image_path" class="form-label">Ganti Gambar (Opsional)</label>
                            <input type="file" id="image_path" name="image_path" class="form-control @error('image_path') is-invalid @enderror" accept="image/*">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah gambar. Rekomendasi: 1920x1080px.</div>
                            @error('image_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Link URL --}}
                        <div class="mb-3">
                            <label for="link_url" class="form-label">URL Link (Opsional)</label>
                            <input type="url" id="link_url" name="link_url" value="{{ old('link_url', $slider->link_url) }}" class="form-control @error('link_url') is-invalid @enderror" placeholder="https://contoh.com">
                            <div class="form-text">Jika diisi, gambar slider akan bisa diklik.</div>
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Urutan</label>
                            <input type="number" id="order" name="order" value="{{ old('order', $slider->order) }}" class="form-control @error('order') is-invalid @enderror" style="max-width: 150px;">
                            <div class="form-text">Urutan tampil slider (0 adalah yang pertama).</div>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select id="is_active" name="is_active" class="form-select @error('is_active') is-invalid @enderror" style="max-width: 200px;">
                                <option value="1" {{ old('is_active', $slider->is_active) == 1 ? 'selected' : '' }}>Aktif (Tampilkan)</option>
                                <option value="0" {{ old('is_active', $slider->is_active) == 0 ? 'selected' : '' }}>Nonaktif (Sembunyikan)</option>
                            </select>
                            <div class="form-text">Pilih status tampil slider.</div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex gap-2 mt-4">
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