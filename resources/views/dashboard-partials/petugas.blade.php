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