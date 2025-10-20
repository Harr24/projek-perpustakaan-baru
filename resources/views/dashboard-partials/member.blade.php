{{-- Menu ini ditampilkan untuk role siswa dan guru --}}

{{-- Link ke Katalog Buku (selalu tampil) --}}
<a class="nav-item" href="{{ route('catalog.index') }}">
    <div class="nav-item-main">
        <span>Lihat Katalog Buku</span>
    </div>
    <span class="meta">Mulai Meminjam</span>
</a>

{{-- ========================================================== --}}
{{-- PERUBAHAN UTAMA: Link dan teks diubah ke "Lihat Profil" --}}
{{-- ========================================================== --}}
<a class="nav-item" href="{{ route('profile.show') }}">
    <div class="nav-item-main">
        <span>Lihat Profil Saya</span>
    </div>
    <span class="meta">Akun</span>
</a>

{{-- Tampilkan link riwayat HANYA jika pernah meminjam --}}
@if($hasBorrowings ?? true) {{-- Menambahkan '?? true' untuk mencegah error jika variabel tidak ada --}}
<a class="nav-item" href="{{ route('borrow.history') }}">
    <div class="nav-item-main">
        <span>Lihat Riwayat Peminjaman</span>
    </div>
    <span class="meta">Riwayat</span>
</a>
@endif