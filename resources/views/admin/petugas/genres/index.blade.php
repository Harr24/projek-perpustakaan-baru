<h1>Daftar Genre</h1>

{{-- PERUBAHAN DI SINI --}}
<div style="margin-bottom: 20px;">
    <a href="{{ route('dashboard') }}">Kembali ke Dashboard</a> | 
    <a href="{{ route('admin.petugas.genres.create') }}">Tambah Genre Baru</a>
</div>

@if(session('success'))
    <div style="color: green;">{{ session('success') }}</div>
    <br>
@endif

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Genre</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($genres as $genre)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $genre->name }}</td>
                <td>
                    <a href="{{ route('admin.petugas.genres.edit', $genre->id) }}">Edit</a>
                    
                    <form action="{{ route('admin.petugas.genres.destroy', $genre->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus genre ini?')" style="color:red; border:none; background:none; cursor:pointer; padding:0; font-family:inherit; font-size:inherit;">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Belum ada data genre.</td>
            </tr>
        @endforelse
    </tbody>
</table>