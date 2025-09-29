<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Form Pendaftaran Anggota</h2>

    <form method="POST" action="/register" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="name">Nama Lengkap</label><br>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="email">Alamat Email</label><br>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>

        <div>
            <label for="student_card_photo">Foto Kartu Pelajar</label><br>
            <input id="student_card_photo" type="file" name="student_card_photo" required>
            @error('student_card_photo')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>

        <div>
            <label for="password">Password</label><br>
            <input id="password" type="password" name="password" required>
            @error('password')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        <br>
        <div>
            <label for="password_confirmation">Konfirmasi Password</label><br>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>
        <br>
        <div>
            <button type="submit">
                Daftar
            </button>
        </div>
    </form>
</body>
</html>