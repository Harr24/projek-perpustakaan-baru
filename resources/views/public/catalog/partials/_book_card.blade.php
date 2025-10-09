{{-- 
============================================================================
== KARTU BUKU VERSI FINAL -- PERBAIKAN LOGIKA TOMBOL ==
============================================================================
--}}
<div class="card h-100 book-card shadow-sm">
    <img src="{{ $book->cover_image ? route('book.cover', $book) : 'https://placehold.co/300x400/0284C7/ffffff?text=' . urlencode(substr($book->title, 0, 15)) }}" 
         onerror="this.onerror=null;this.src='https://placehold.co/300x400/D9534F/ffffff?text=No+Cover'"
         class="card-img-top book-cover" alt="Sampul {{ $book->title }}" loading="lazy">
    
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

