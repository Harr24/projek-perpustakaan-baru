{{-- Link ke Katalog Buku (selalu tampil) --}}
<a class="nav-item" href="{{ route('catalog.index') }}">
    <div class="nav-item-main">
        <span>Lihat Katalog Buku</span>
    </div>
    <span class="meta">Mulai Meminjam</span>
</a>

{{-- ========================================================== --}}
{{-- MENU BARU: Link untuk Edit Profil --}}
{{-- ========================================================== --}}
<a class="nav-item" href="{{ route('profile.edit') }}">
    <div class="nav-item-main">
        <span>Edit Profil Saya</span>
    </div>
    <span class="meta">Akun</span>
</a>

{{-- Tampilkan link riwayat HANYA jika pernah meminjam --}}
@if($hasBorrowings)
<a class="nav-item" href="{{ route('borrow.history') }}">
    <div class="nav-item-main">
        <span>Lihat Riwayat Peminjaman</span>
    </div>
    <span class="meta">Riwayat</span>
</a>
@endif