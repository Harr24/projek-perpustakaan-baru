
<div class="card h-100 book-card shadow-sm">
    
    {{-- ========================================================== --}}
    {{-- BAGIAN GAMBAR YANG DIUPDATE (menggunakan Tome.png) --}}
    {{-- ========================================================== --}}
    @if($book->cover_image)
        {{-- Jika BUKU PUNYA cover, tampilkan via route --}}
        <img src="{{ route('book.cover', $book) }}" 
             class="card-img-top book-cover" 
             alt="Sampul {{ $book->title }}" 
             loading="lazy"
             style="object-fit: cover;" {{-- Style 'cover' untuk gambar buku asli --}}
             {{-- Fallback jika gambar asli error, akan memuat Tome.png --}}
             onerror="this.onerror=null;this.src='{{ asset('images/Tome.png') }}';this.style.objectFit='scale-down';this.style.padding='2rem';this.style.backgroundColor='#f4f4f4';">
    @else
        {{-- Jika BUKU TIDAK PUNYA cover, tampilkan Tome.png --}}
        <img src="{{ asset('images/Tome.png') }}" 
             class="card-img-top book-cover" 
             alt="Gambar default buku" 
             loading="lazy"
             style="object-fit: scale-down; padding: 2rem; background-color: #f4f4f4;"> {{-- Style 'scale-down' untuk placeholder --}}
    @endif
    {{-- ========================================================== --}}
    {{-- AKHIR BAGIAN GAMBAR YANG DIUPDATE --}}
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
        </div>
    </div>

    <div class="card-footer bg-white border-0 p-2">
        <div class="d-grid gap-2">
            {{-- 
            ============================================================================
            == INILAH PERBAIKAN UTAMANYA! ==
            == Kita gunakan 'available_copies_count' untuk menentukan tombol. ==
            ============================================================================
            --}}
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