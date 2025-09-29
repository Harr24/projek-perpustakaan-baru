<h1>Edit Genre: {{ $genre->name }}</h1>

<form action="{{ route('admin.petugas.genres.update', $genre->id) }}" method="POST">
    @csrf
    @method('PUT') <label for="name">Nama Genre:</label>
    <input type="text" id="name" name="name" value="{{ $genre->name }}" required>
    @error('name')
        <div style="color: red;">{{ $message }}</div>
    @enderror
    <br><br>
    <button type="submit">Update</button>
</form>