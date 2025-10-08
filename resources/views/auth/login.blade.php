<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Anggota</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #ffffff 50%, #f3f4f6 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        .login-container {
            background: linear-gradient(145deg, #ffffff 0%, #fef2f2 100%);
            padding: 50px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(220, 38, 38, 0.2), 0 0 0 1px rgba(220, 38, 38, 0.05);
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }
        .login-header { text-align: center; margin-bottom: 35px; }
        .login-container h2 { color: #dc2626; font-weight: 700; font-size: 28px; margin-bottom: 8px; }
        .login-subtitle { color: #6b7280; font-size: 14px; }
        .input-group { margin-bottom: 24px; position: relative; }
        .input-group label { display: block; margin-bottom: 10px; font-weight: 600; color: #374151; font-size: 14px; }
        .input-group input { width: 100%; padding: 14px 20px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 15px; transition: all 0.3s ease; background-color: #ffffff; }
        .input-group input:focus { outline: none; border-color: #dc2626; box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1); }
        button { width: 100%; padding: 16px; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3); margin-top: 10px; }
        button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4); }
        .error-message { color: #991b1b; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border: 2px solid #fca5a5; border-radius: 12px; padding: 14px 18px; margin-bottom: 24px; font-size: 14px; text-align: center; font-weight: 500; }
        .input-error-message { color: #dc2626; font-size: 13px; margin-top: 8px; font-weight: 500; }
        .red-stripe { position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #dc2626 0%, #991b1b 50%, #dc2626 100%); border-radius: 24px 24px 0 0; }
        .login-footer { margin-top: 25px; text-align: center; color: #6b7280; font-size: 13px; }
        .login-footer a { color: #dc2626; text-decoration: none; font-weight: 600; }
        .login-footer a:hover { color: #991b1b; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="red-stripe"></div>
        
        <div class="login-header">
            <h2>Login Anggota</h2>
            <p class="login-subtitle">Selamat datang di website perpustakaan multicomp!</p>
        </div>

        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                @error('email')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required placeholder="Masukkan password Anda">
                @error('password')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit">Masuk Sekarang</button>
        </form>

        <div class="login-footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
        </div>
        
        {{-- =============================================== --}}
        {{-- TAMBAHAN: Tombol Kembali ke Homepage           --}}
        {{-- =============================================== --}}
        <div class="login-footer" style="margin-top: 15px; border-top: 1px solid #e5e7eb; padding-top: 15px;">
            <a href="{{ route('catalog.index') }}">Kembali ke Homepage</a>
        </div>

    </div>
</body>
</html>