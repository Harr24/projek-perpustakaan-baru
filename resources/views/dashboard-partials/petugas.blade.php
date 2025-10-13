<a class="nav-item" href="{{ route('admin.petugas.genres.index') }}">
    <div class="nav-item-main"><span>Kelola Genre</span></div>
    <span class="meta">Data Master</span>
</a>
<a class="nav-item" href="{{ route('admin.petugas.books.index') }}">
    <div class="nav-item-main"><span>Kelola Buku</span></div>
    <span class="meta">Data Master</span>
</a>
<a class="nav-item" href="{{ route('admin.petugas.verification.index') }}">
    <div class="nav-item-main">
        <span>Verifikasi Siswa</span>
        @if($pendingStudentsCount > 0)
            <span class="badge">{{ $pendingStudentsCount }}</span>
        @endif
    </div>
    <span class="meta">Anggota</span>
</a>
<a class="nav-item" href="{{ route('admin.petugas.teachers.index') }}">
    <div class="nav-item-main"><span>Kelola Akun Guru</span></div>
    <span class="meta">Anggota</span>
</a>
<a class="nav-item" href="{{ route('admin.petugas.approvals.index') }}">
    <div class="nav-item-main"><span>Kelola Pengajuan Pinjaman</span></div>
    <span class="meta">Peminjaman</span>
</a>
<a class="nav-item" href="{{ route('admin.petugas.returns.index') }}">
    <div class="nav-item-main"><span>Manajemen Peminjaman</span></div>
    <span class="meta">Pengembalian</span>
</a>
<a class="nav-item" href="{{ route('admin.petugas.fines.index') }}">
    <div class="nav-item-main"><span>Manajemen Denda</span></div>
    <span class="meta">Keuangan</span>
</a>

{{-- ========================================================== --}}
{{-- MENU BARU UNTUK LAPORAN --}}
{{-- ========================================================== --}}
<a class="nav-item" href="{{ route('admin.petugas.reports.borrowings.index') }}">
    <div class="nav-item-main"><span>Laporan Peminjaman</span></div>
    <span class="meta">Laporan</span>
</a>