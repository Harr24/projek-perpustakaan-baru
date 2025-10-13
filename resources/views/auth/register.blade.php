<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Anggota</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* [ SELURUH KODE CSS KAMU YANG SUDAH ADA DI SINI, TIDAK ADA YANG DIUBAH ] */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #ffffff 50%, #f3f4f6 100%);
            /* ... sisa CSS tidak diubah ... */
        }
        .register-container {
            background: linear-gradient(145deg, #ffffff 0%, #fef2f2 100%);
            padding: 40px 35px;
            border-radius: 24px;
            /* ... sisa CSS tidak diubah ... */
        }
        /* ... sisa CSS tidak diubah ... */
    </style>
</head>
<body>
    <div class="register-container">
        <div class="red-stripe"></div>
        
        <div class="register-header">
            <h2>Daftar Anggota</h2>
            <p class="register-subtitle">Lengkapi data diri Anda</p>
        </div>

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

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <div class="input-group">
                <label for="name">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama lengkap" class="@error('name') is-invalid @enderror">
                @error('name')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="input-group">
                <label for="nis">NISN (Nomor Induk Siswa Nasional)</label>
                <input id="nis" type="text" name="nis" value="{{ old('nis') }}" required placeholder="Masukkan NISN Anda" class="@error('nis') is-invalid @enderror">
                @error('nis')
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
                <label for="class_name">Kelas</label>
                <input id="class_name" type="text" name="class_name" value="{{ old('class_name') }}" required placeholder="Contoh: XII RPL 1" class="@error('class_name') is-invalid @enderror">
                @error('class_name')
                    <div class="input-error-message">{{ $message }}</div>
                @enderror
            </div>

            {{-- ========================================================== --}}
            {{-- INPUT BARU: Tambahkan Input Nomor WhatsApp di Sini --}}
            {{-- ========================================================== --}}
            <div class="input-group">
                <label for="phone_number">Nomor WhatsApp (Aktif)</label>
                <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}" required placeholder="Contoh: 081234567890" class="@error('phone_number') is-invalid @enderror">
                @error('phone_number')
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