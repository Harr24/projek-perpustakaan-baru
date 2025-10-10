{{-- ====================================================================== --}}
{{-- Komponen Hero Slider Responsif --}}
{{-- Dibuat dengan Bootstrap 5 Carousel --}}
{{-- ====================================================================== --}}

<style>
    .hero-slider .carousel-item {
        /* Tentukan tinggi slider. Anda bisa menggunakan vh (viewport height) */
        /* atau piksel. Ini akan menjadi 'bingkai' untuk gambar Anda. */
        height: 60vh; 
        min-height: 400px; /* Tinggi minimal untuk layar kecil */
        background-color: #333; /* Warna fallback jika gambar gagal dimuat */
    }

    .hero-slider .carousel-item img {
        /* ========================================================== */
        /* INILAH 'SIHIRNYA' - BAGIAN PALING PENTING! */
        /* ========================================================== */
        width: 100%;            /* Paksa gambar untuk mengisi lebar kontainer */
        height: 100%;           /* Paksa gambar untuk mengisi tinggi kontainer */
        object-fit: cover;      /* Kunci utama: Pangkas gambar agar pas tanpa distorsi */
        object-position: center;/* Posisikan gambar di tengah sebelum dipangkas */
        filter: brightness(0.7);/* Efek opsional: gelapkan gambar agar teks lebih terbaca */
    }

    .hero-slider .carousel-caption {
        /* Posisi teks di tengah secara vertikal dan horizontal */
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80%;
    }
</style>

@if($heroSliders->isNotEmpty())
<div id="heroSlider" class="carousel slide hero-slider mb-5" data-bs-ride="carousel">
    {{-- Indicators (titik-titik di bawah) --}}
    <div class="carousel-indicators">
        @foreach ($heroSliders as $index => $slider)
            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
        @endforeach
    </div>

    {{-- Konten Slide --}}
    <div class="carousel-inner">
        @foreach ($heroSliders as $index => $slider)
            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                {{-- Gambar akan secara otomatis responsif karena CSS di atas --}}
                <img src="{{ asset('storage/' . $slider->image_path) }}" class="d-block w-100" alt="{{ $slider->title }}">
                
                <div class="carousel-caption text-center">
                    <h1 class="display-4 fw-bold">{{ $slider->title }}</h1>
                    <p class="lead">{{ $slider->description }}</p>
                    @if($slider->button_link)
                    <a href="{{ $slider->button_link }}" class="btn btn-danger btn-lg mt-3">
                        {{ $slider->button_text ?? 'Lihat Selengkapnya' }} <i class="bi bi-arrow-right"></i>
                    </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tombol Navigasi Kiri/Kanan --}}
    <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide-to="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide-to="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
@endif
