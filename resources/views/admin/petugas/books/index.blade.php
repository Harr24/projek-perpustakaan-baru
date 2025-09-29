<h1>Daftar Buku</h1>
<a href="{{ route('admin.petugas.books.create') }}">Tambah Buku Baru</a>
<br><br>

@if(session('success'))
    <div style="color: green;">{{ session('success') }}</div>
    <br>
@endif

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Sampul</th>
            <th>Judul</th>
            <th>Penulis</th>
            <th>Genre</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($books as $book)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="Cover" width="80">
                    @else
                        <span>Tidak ada gambar</span>
                    @endif
                </td>
                <td>{{ $book->title }}</td>
                <td>{{ $book->author }}</td>
                <td>{{ $book->genre->name }}</td>
                <td>
                    <a href="#">Edit</a>
                    <a href="#">Hapus</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Belum ada data buku.</td>
            </tr>
        @endforelse
    </tbody>
</table>