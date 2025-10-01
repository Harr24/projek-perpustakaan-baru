<h1>Tambah Buku Baru</h1>

@if ($errors->any())
    <div style="color: red;">
        <strong>Error:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.petugas.books.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div>
        <label>Judul Buku:</label><br>
        <input type="text" name="title" value="{{ old('title') }}" required>
    </div>
    <br>
    <div>
        <label>Penulis:</label><br>
        <input type="text" name="author" value="{{ old('author') }}" required>
    </div>
    <br>
    <div>
        <label>Genre:</label><br>
        <select name="genre_id" required>
            <option value="">-- Pilih Genre --</option>
            @foreach ($genres as $genre)
                <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>
                    {{ $genre->name }}
                </option>
            @endforeach
        </select>
    </div>
    <br>
    <div>
        <label>Sampul Buku (Cover):</label><br>
        <input type="file" name="cover_image">
    </div>
    <br>
    <div>
        <label>Jumlah Stok:</label><br>
        <input type="number" name="stock" min="1" value="{{ old('stock') }}" required>
    </div>
    <br>
    <button type="submit">Simpan Buku</button>
</form>
