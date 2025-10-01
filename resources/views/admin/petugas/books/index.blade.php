<h1>Daftar Buku</h1>

{{-- Menambahkan link kembali ke dashboard & merapikan navigasi --}}
<div style="margin-bottom: 20px;">
    <a href="{{ route('dashboard') }}">Kembali ke Dashboard</a> | 
    <a href="{{ route('admin.petugas.books.create') }}">Tambah Buku Baru</a>
</div>

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
                {{-- Menambahkan fallback jika genre tidak ada --}}
                <td>{{ $book->genre->name ?? 'N/A' }}</td> 
                <td>
                    {{-- PERUBAHAN DI SINI --}}
                    
                    <a href="{{ route('admin.petugas.books.show', $book->id) }}">Lihat Detail</a> |

                    <a href="{{ route('admin.petugas.books.edit', $book->id) }}">Edit</a> |
                    
                    <form action="{{ route('admin.petugas.books.destroy', $book->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')" style="color:red; border:none; background:none; cursor:pointer; padding:0; font-family:inherit; font-size:inherit;">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Belum ada data buku.</td>
            </tr>
        @endforelse
    </tbody>
</table>