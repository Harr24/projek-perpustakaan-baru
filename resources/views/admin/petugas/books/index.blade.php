<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #fff;
        color: #333;
        margin: 20px;
    }

    h1 {
        color: #d32f2f;
        border-bottom: 2px solid #d32f2f;
        padding-bottom: 10px;
    }

    .nav-links {
        margin-bottom: 20px;
    }

    .nav-links a {
        text-decoration: none;
        color: #d32f2f;
        font-weight: bold;
        margin-right: 15px;
    }

    .success-message {
        color: green;
        margin-bottom: 15px;
    }

    .book-table {
        width: 100%;
        border-collapse: collapse;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .book-table th, .book-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .book-table th {
        background-color: #d32f2f;
        color: white;
    }

    .book-table tr:hover {
        background-color: #f9f9f9;
    }

    .book-cover {
        width: 80px;
        border-radius: 4px;
    }

    .action-links a,
    .action-links button {
        margin-right: 10px;
        color: #d32f2f;
        text-decoration: none;
        background: none;
        border: none;
        cursor: pointer;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .book-table, .book-table thead, .book-table tbody, .book-table th, .book-table td, .book-table tr {
            display: block;
        }

        .book-table tr {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .book-table td {
            padding: 8px 0;
        }

        .book-table td::before {
            content: attr(data-label);
            font-weight: bold;
            display: block;
            color: #d32f2f;
        }
    }
</style>

<h1>Daftar Buku</h1>

<div class="nav-links">
    <a href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
    <a href="{{ route('admin.petugas.books.create') }}">Tambah Buku Baru</a>
</div>

@if(session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif

<table class="book-table">
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
                <td data-label="No">{{ $loop->iteration }}</td>
                <td data-label="Sampul">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="Cover" class="book-cover">
                    @else
                        <span>Tidak ada gambar</span>
                    @endif
                </td>
                <td data-label="Judul">{{ $book->title }}</td>
                <td data-label="Penulis">{{ $book->author }}</td>
                <td data-label="Genre">{{ $book->genre->name ?? 'N/A' }}</td>
                <td data-label="Aksi" class="action-links">
                    <a href="{{ route('admin.petugas.books.show', $book->id) }}">Lihat Detail</a>
                    <a href="{{ route('admin.petugas.books.edit', $book->id) }}">Edit</a>
                    <form action="{{ route('admin.petugas.books.destroy', $book->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</button>
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
