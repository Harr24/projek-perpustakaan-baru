<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Buku: {{ $book->title }}</title>
</head>
<body>

    <a href="{{ route('admin.petugas.books.index') }}">Kembali ke Daftar Buku</a>
    <hr>

    <h1>Detail Buku</h1>
    
    @if($book->cover_image)
        <img src="{{ Storage::url($book->cover_image) }}" alt="Cover" width="150">
    @endif
    
    <h2>{{ $book->title }}</h2>
    <p><strong>Penulis:</strong> {{ $book->author }}</p>
    
    {{-- PERUBAHAN UTAMA DI SINI --}}
    <p><strong>Genre:</strong> {{ optional($book->genre)->name ?? 'N/A' }}</p>
    
    <p><strong>Total Stok Awal:</strong> {{ $book->stock }}</p>

    <hr>

    <h3>Daftar Salinan Buku dan Kodenya</h3>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Buku</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($book->copies as $copy)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $copy->book_code }}</td>
                    <td>{{ ucfirst($copy->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Belum ada data salinan untuk buku ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>



