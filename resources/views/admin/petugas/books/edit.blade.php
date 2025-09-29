<h1>Edit Buku: {{ $book->title }}</h1>

<form action="{{ route('admin.petugas.books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') {{-- Method untuk update --}}

    <div>
        <label>Judul Buku:</label><br>
        <input type="text" name="title" value="{{ $book->title }}" required>
    </div>
    <br>
    <div>
        <label>Penulis:</label><br>
        <input type="text" name="author" value="{{ $book->author }}" required>
    </div>
    <br>
    <div>
        <label>Genre:</label><br>
        <select name="genre_id" required>
            @foreach ($genres as $genre)
                <option value="{{ $genre->id }}" {{ $book->genre_id == $genre->id ? 'selected' : '' }}>
                    {{ $genre->name }}
                </option>
            @endforeach
        </select>
    </div>
    <br>
    <div>
        <label>Sampul Buku Saat Ini:</label><br>
        @if($book->cover_image)
            <img src="{{ Storage::url($book->cover_image) }}" alt="Cover" width="100">
        @else
            <span>Tidak ada gambar</span>
        @endif
    </div>
    <br>
    <div>
        <label>Ganti Sampul Buku (Kosongkan jika tidak ingin diubah):</label><br>
        <input type="file" name="cover_image">
    </div>
    <br>
    <button type="submit">Update Buku</button>
</form>