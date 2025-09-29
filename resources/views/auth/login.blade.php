<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login Anggota</h2>

    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
        <br>
    @endif

    <form method="POST" action="/login">
        @csrf

        <div>
            <label for="email">Alamat Email</label><br>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
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
            <button type="submit">
                Login
            </button>
        </div>
    </form>
</body>
</html>