<h1>Tambah Genre Baru</h1>

<form action="{{ route('admin.petugas.genres.store') }}" method="POST">
    @csrf
    <label for="name">Nama Genre:</label>
    <input type="text" id="name" name="name" required>
    @error('name')
        <div style="color: red;">{{ $message }}</div>
    @enderror
    <br><br>
    <button type="submit">buang</button>
</form>