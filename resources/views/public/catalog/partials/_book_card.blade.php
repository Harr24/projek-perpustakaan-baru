<div class="card h-100 book-card shadow-sm">

    {{-- ========================================================== --}}
    {{-- BAGIAN GAMBAR YANG DIPERBAIKI --}}
    {{-- ========================================================== --}}
    @if($book->cover_image)
        <img src="{{ asset('storage/' . $book->cover_image) }}" 
             class="card-img-top book-cover" 
             alt="Sampul {{ $book->title }}" 
             loading="lazy"
             style="object-fit: cover;"
             onerror="this.onerror=null;this.src='{{ asset('images/Tome.png') }}';this.style.objectFit='scale-down';this.style.padding='2rem';this.style.backgroundColor='#f4f4f4';">
    @else
        {{-- Jika BUKU TIDAK PUNYA cover, tampilkan Tome.png --}}
        <img src="{{ asset('images/Tome.png') }}" 
             class="card-img-top book-cover" 
             alt="Gambar default buku" 
             loading="lazy"
             style="object-fit: scale-down; padding: 2rem; background-color: #f4f4f4;">
    @endif
    {{-- ========================================================== --}}
    {{-- AKHIR BAGIAN GAMBAR YANG DIPERBAIKI --}}
    {{-- ========================================================== --}}

    <div class="card-body d-flex flex-column">
        <h6 class="card-title fw-bold text-truncate" title="{{ $book->title }}">{{ $book->title }}</h6>
        <p class="card-text small text-muted mb-2">{{ $book->author }} ({{ $book->publication_year ?? 'N/A' }})</p>

        <div class="mt-auto">
            {{-- Data dari withCount, sekarang DIJAMIN AKURAT --}}
            <p class="mb-2 small">
                <span class="fw-bold">{{ $book->available_copies_count }}</span> / {{ $book->copies_count }} Tersedia
            </p>

            @if($book->available_copies_count === 0)
                <span class="badge bg-secondary">Stok Habis</span>
            @elseif($book->available_copies_count > 0 && $book->available_copies_count < 4)
                <span class="badge bg-warning text-dark">Stok Rendah!</span>
            @else
                <span class="badge bg-success">Tersedia</span>
            @endif

            <!-- ========================================================== -->
            <!-- ===== PERBAIKAN: Menampilkan SEMUA Tipe Buku ===== -->
            <!-- ========================================================== -->
            @switch($book->book_type)
                @case('reguler')
                    <span class="badge bg-primary">Buku Reguler</span>
                    @break
                @case('paket')
                    <span class="badge bg-info text-dark">Buku Paket</span>
                    @break
                @case('laporan')
                    <span class="badge bg-secondary">Buku Laporan</span>
                    @break
            @endswitch
            <!-- ========================================================== -->

        </div>
    </div>

    <div class="card-footer bg-white border-0 p-2">
        <div class="d-grid gap-2">
            @if($book->available_copies_count > 0)
                {{-- Jika ada stok, tombolnya menjadi "Lihat & Pinjam" --}}
                <a href="{{ route('catalog.show', $book) }}" class="btn btn-sm btn-danger fw-bold">
                    <i class="bi bi-book-fill"></i> Lihat & Pinjam
                </a>
            @else
                {{-- Jika stok habis, tombolnya menjadi "Stok Habis" dan non-aktif --}}
                <button class="btn btn-sm btn-secondary" disabled>Stok Habis</button>
            @endif
        </div>
    </div>
</div>