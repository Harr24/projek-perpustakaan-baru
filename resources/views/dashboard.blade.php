<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Selamat Datang, {{ Auth::user()->name }}!</h1>
    <p>Anda berhasil login ke sistem perpustakaan.</p>

    <hr>

    <h3>Menu Navigasi</h3>
    
    {{-- Tampilkan menu ini hanya jika role-nya adalah petugas --}}
    @if(Auth::user()->role == 'petugas')
        <p><a href="{{ route('admin.petugas.genres.index') }}">Kelola Genre</a></p>
        <p><a href="{{ route('admin.petugas.books.create') }}">Tambah Buku Baru</a></p>
        <p><a href="{{ route('admin.petugas.books.index') }}">Kelola Buku</a></p>
    @endif
    
    {{-- Tampilkan menu ini hanya jika role-nya adalah siswa --}}
    @if(Auth::user()->role == 'siswa')
        <p><a href="#">Lihat Riwayat Peminjaman</a></p>
    @endif

    <br>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>