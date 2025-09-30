<h1>Tambah Akun Baru</h1>

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.superadmin.petugas.store') }}" method="POST">
    @csrf
    <div>
        <label>Nama:</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
    </div>
    <br>
    <div>
        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
    </div>
    <br>
    <div>
        <label>Role:</label>
        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="petugas">Petugas</option>
            <option value="superadmin">Super Admin</option>
        </select>
    </div>
    <br>
    <div>
        <label>Password:</label>
        <input type="password" name="password" required>
    </div>
    <br>
    <div>
        <label>Konfirmasi Password:</label>
        <input type="password" name="password_confirmation" required>
    </div>
    <br>
    <button type="submit">Simpan</button>
</form>