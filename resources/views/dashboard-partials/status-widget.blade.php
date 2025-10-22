{{-- 
    Widget ini akan menerima variabel:
    $borrowingInfo (berisi data pinjaman, bisa grouped atau individual)
    $displayMode ('grouped', 'individual', atau null)
    $quote (jika tidak ada pinjaman)
--}}

{{-- Cek dulu apakah ada info pinjaman ($borrowingInfo tidak null) --}}
@if(isset($borrowingInfo) && $borrowingInfo !== null)

    {{-- ========================================================== --}}
    {{-- TAMPILAN UNTUK GURU (GROUPED) --}}
    {{-- ========================================================== --}}
    @if($displayMode == 'grouped') 
        <h3 class="widget-title" id="widgetTitle">
            📚 Buku Paket yang Sedang Dipinjam
        </h3>
        <div class="borrowing-list-stack">
            {{-- Loop data yang sudah dikelompokkan --}}
            @foreach($borrowingInfo as $group)
                <div class="active-borrowing-card">
                    {{-- Tampilkan Cover Buku --}}
                    @if($group->book && $group->book->cover_image)
                        <img src="{{ Storage::url($group->book->cover_image) }}" alt="Cover {{ $group->book->title }}" class="borrowed-cover">
                    @else
                        <div class="borrowed-cover-placeholder">
                            <span>Tidak ada cover</span>
                        </div>
                    @endif
                    
                    <div class="borrowed-info">
                        {{-- Tampilkan Judul Buku --}}
                        <h4>{{ $group->book->title ?? 'Judul Tidak Ditemukan' }}</h4>
                        
                        {{-- Tampilkan Jumlah Eksemplar --}}
                        <div class="borrowed-meta">
                            <span>Jumlah:</span>
                            <strong>{{ $group->count }} eksemplar</strong>
                        </div>
                        
                        {{-- Tampilkan Rentang Tanggal (Opsional, tapi informatif) --}}
                        {{-- Pastikan earliest_borrowed dan latest_due adalah objek Carbon --}}
                        @if($group->earliest_borrowed instanceof \Carbon\Carbon)
                        <div class="borrowed-meta">
                            <span>Dipinjam Sejak:</span>
                            <strong>{{ $group->earliest_borrowed->format('d M Y') }}</strong>
                        </div>
                        @endif
                        @if($group->latest_due instanceof \Carbon\Carbon)
                        <div class="borrowed-meta">
                            <span>Jatuh Tempo Hingga:</span>
                            <strong>{{ $group->latest_due->format('d M Y') }}</strong>
                        </div>
                         @elseif(is_string($group->latest_due)) {{-- Fallback jika masih string --}}
                         <div class="borrowed-meta">
                            <span>Jatuh Tempo Hingga:</span>
                            <strong>{{ \Carbon\Carbon::parse($group->latest_due)->format('d M Y') }}</strong>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        {{-- Tombol untuk melihat detail/riwayat --}}
        <a href="{{ route('borrow.history') }}" class="btn-widget-full">Lihat Rincian & Riwayat Peminjaman</a>

    {{-- ========================================================== --}}
    {{-- TAMPILAN UNTUK SISWA (INDIVIDUAL) - Kode Lama Kamu --}}
    {{-- ========================================================== --}}
    @elseif($displayMode == 'individual') 
         <h3 class="widget-title" id="widgetTitle">
            📖 Buku yang Sedang Dipinjam ({{ $borrowingInfo->count() }})
        </h3>
         <div class="borrowing-list-stack">
            {{-- Loop data individual (variabel $borrowingInfo berisi koleksi asli) --}}
            @foreach($borrowingInfo as $activeBorrowing) 
                <div class="active-borrowing-card">
                    @if($activeBorrowing->bookCopy && $activeBorrowing->bookCopy->book && $activeBorrowing->bookCopy->book->cover_image)
                        <img src="{{ Storage::url($activeBorrowing->bookCopy->book->cover_image) }}" alt="Cover {{ $activeBorrowing->bookCopy->book->title }}" class="borrowed-cover">
                    @else
                        <div class="borrowed-cover-placeholder">
                            <span>Tidak ada cover</span>
                        </div>
                    @endif
                    <div class="borrowed-info">
                        <h4>{{ $activeBorrowing->bookCopy->book->title ?? 'Judul Tidak Ditemukan' }}</h4>
                        <div class="borrowed-meta">
                            <span>Kode Eksemplar:</span>
                            <strong>{{ $activeBorrowing->bookCopy->book_code ?? 'N/A' }}</strong>
                        </div>
                        <div class="borrowed-meta">
                            <span>Tanggal Pinjam:</span>
                            <strong>{{ $activeBorrowing->borrowed_at->format('d M Y') }}</strong>
                        </div>
                        <div class="borrowed-meta">
                            <span>Jatuh Tempo:</span>
                            {{-- Pastikan due_date adalah objek Carbon atau string tanggal --}}
                            @if($activeBorrowing->due_date instanceof \Carbon\Carbon)
                            <strong>{{ $activeBorrowing->due_date->format('d M Y') }}</strong>
                            @elseif(is_string($activeBorrowing->due_date))
                            <strong>{{ \Carbon\Carbon::parse($activeBorrowing->due_date)->format('d M Y') }}</strong>
                            @else
                            <strong>N/A</strong>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
         <a href="{{ route('borrow.history') }}" class="btn-widget-full">Lihat Semua Riwayat Peminjaman</a>
    @endif

{{-- ========================================================== --}}
{{-- TAMPILAN JIKA TIDAK ADA PINJAMAN AKTIF --}}
{{-- ========================================================== --}}
@else 
    <h3 class="widget-title" id="widgetTitle">💡 Kutipan Hari Ini</h3>
    <blockquote class="quote-card">
        <p>"{{ $quote['content'] ?? 'Membaca adalah jendela dunia.' }}"</p>
        <footer>— {{ $quote['author'] ?? 'Pribahasa' }}</footer>
    </blockquote>
    <a href="{{ route('catalog.index') }}" class="btn-widget-full btn-start-reading">
        Mulai Membaca Buku
    </a>
@endif