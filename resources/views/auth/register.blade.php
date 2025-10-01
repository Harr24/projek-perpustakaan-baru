<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Anggota</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #ffffff 50%, #f3f4f6 100%);
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
        }

        /* Decorative elements */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.1) 0%, transparent 70%);
            top: -250px;
            right: -250px;
            border-radius: 50%;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(220, 38, 38, 0.08) 0%, transparent 70%);
            bottom: -200px;
            left: -200px;
            border-radius: 50%;
        }

        .register-container {
            background: linear-gradient(145deg, #ffffff 0%, #fef2f2 100%);
            padding: 40px 35px;
            border-radius: 24px;
            box-shadow: 
                0 20px 60px rgba(220, 38, 38, 0.2),
                0 0 0 1px rgba(220, 38, 38, 0.05);
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 1;
            margin: auto;
        }

        /* Decorative stripe */
        .red-stripe {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #dc2626 0%, #991b1b 50%, #dc2626 100%);
            border-radius: 24px 24px 0 0;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-container h2 {
            color: #dc2626;
            font-weight: 700;
            font-size: 26px;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(220, 38, 38, 0.1);
        }

        .register-subtitle {
            color: #6b7280;
            font-size: 14px;
            font-weight: 400;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #ffffff;
            font-family: 'Poppins', sans-serif;
        }

        .input-group input[type="file"] {
            padding: 12px 20px;
            cursor: pointer;
        }

        .input-group input[type="file"]::file-selector-button {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            margin-right: 12px;
            transition: all 0.3s ease;
        }

        .input-group input[type="file"]::file-selector-button:hover {
            background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%);
        }

        .input-group input:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
            transform: translateY(-2px);
        }

        .input-group input:hover {
            border-color: #f87171;
        }
        
        .input-group input.is-invalid {
            border-color: #dc2626;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
            margin-top: 8px;
            position: relative;
            overflow: hidden;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        button:hover::before {
            left: 100%;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .error-message-box {
            color: #991b1b;
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #fca5a5;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .error-message-box ul {
            list-style-position: inside;
        }

        .input-error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .input-error-message::before {
            content: '⚠️';
            font-size: 12px;
        }

        /* Footer text */
        .register-footer {
            margin-top: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
        }

        .register-footer a {
            color: #dc2626;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .register-footer a:hover {
            color: #991b1b;
            text-decoration: underline;
        }

        /* File upload info */
        .file-info {
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
            font-style: italic;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .register-container {
                padding: 40px 30px;
            }
            
            .register-container h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="red-stripe"></div>
        
        <div class="register-header">
            <h2>Daftar Anggota</h2>
            <p class="register-subtitle">Lengkapi data diri Anda</p>
        </div>

        <!-- Menampilkan semua error validasi di bagian atas -->
        @if ($errors->any())
            <div class="error-message-box">
                <p><strong>Oops! Terjadi kesalahan:</strong></p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- PERUBAHAN 1: Menggunakan route helper dan menambahkan @csrf --}}
        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <div class="input-group">
                <label for="name">Nama Lengkap</label>
                {{-- PERUBAHAN 2: Menambahkan old() dan @error --}}
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama lengkap" class="@error('name') is-invalid @enderror">
                @error('name')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-group">
                <label for="email">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" class="@error('email') is-invalid @enderror">
                @error('email')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-group">
                <label for="student_card_photo">Foto Kartu Pelajar</label>
                <input id="student_card_photo" type="file" name="student_card_photo" accept="image/*" required class="@error('student_card_photo') is-invalid @enderror">
                <p class="file-info">Format: JPG, PNG, atau JPEG (Maks. 2MB)</p>
                @error('student_card_photo')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required placeholder="Minimal 8 karakter" class="@error('password') is-invalid @enderror">
                @error('password')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Ulangi password">
            </div>
            
            <button type="submit">Daftar Sekarang</button>
        </form>

        <div class="register-footer">
            Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
        </div>
    </div>
</body>
</html>

