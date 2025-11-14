<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - Perpustakaan Multicomp</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f6f8f9;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #d1d5db;
            --accent: #d9534f;
            --accent-hover: #b93a37;
            --accent-light: #fef2f2;
            --success: #16a34a;
            --error: #d9534f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            display: grid;
            place-items: center;
            min-height: 100vh;
            padding: 24px;
        }

        .container {
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            padding: 32px 40px;
            max-width: 800px;
            width: 100%;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header .logo {
            font-size: 3rem;
            line-height: 1;
            color: var(--accent);
            margin-bottom: 8px;
        }
        .header h2 {
            font-weight: 600;
            font-size: 1.75rem;
            color: var(--text);
        }
        .header p {
            color: var(--muted);
            font-size: 1rem;
        }

        /* Kotak Error */
        .error-box {
            background-color: var(--accent-light);
            border: 1px solid var(--error);
            color: var(--accent-hover);
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .error-box p {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .error-box ul {
            padding-left: 20px;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr; /* 1 kolom di HP */
            gap: 20px;
        }
        
        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr; /* 2 kolom di Desktop */
            }
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        
        .input-group.full-width {
            grid-column: 1 / -1; /* Span 2 kolom */
        }

        .input-group label {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text);
        }

        /* ========================================================== */
        /* --- PERUBAHAN 1: Menambahkan 'select' ke styling --- */
        /* ========================================================== */
        .input-group input[type="text"],
        .input-group input[type="email"],
        .input-group input[type="tel"],
        .input-group input[type="password"],
        .input-group select { /* <-- TAMBAHKAN INI */
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            -webkit-appearance: none; /* Hapus style bawaan browser */
            -moz-appearance: none;
            appearance: none;
            background-color: var(--card); /* Pastikan background putih */
            /* Tambahkan ikon panah dropdown kustom */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 1.25em;
        }
        
        .input-group input::placeholder {
            color: #9ca3af;
        }

        .input-group input:focus,
        .input-group select:focus { /* <-- TAMBAHKAN INI */
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(217, 83, 79, 0.1);
        }

        .input-group input.is-invalid,
        .input-group select.is-invalid { /* <-- TAMBAHKAN INI */
            border-color: var(--error);
        }
        /* ========================================================== */

        .input-error-message {
            color: var(--error);
            font-size: 0.875rem;
            margin-top: 2px;
        }

        /* Custom File Input */
        .file-upload-label {
            border: 2px dashed var(--border);
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .file-upload-label:hover {
            border-color: var(--accent);
            background-color: var(--accent-light);
        }
        .file-upload-label.has-file {
            border-color: var(--success);
            background-color: #f0fdf4;
        }
        .file-upload-label svg {
            width: 40px;
            height: 40px;
            color: var(--muted);
            margin-bottom: 12px;
        }
        .file-upload-label span {
            display: block;
            font-weight: 600;
            color: var(--accent);
            font-size: 1rem;
        }
        .file-upload-label p {
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 4px;
        }
        /* Sembunyikan input file asli */
        .file-input-hidden {
            display: none;
        }


        /* Tombol & Footer */
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, var(--accent), var(--accent-hover));
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 12px;
        }
        .submit-btn:hover {
            opacity: 0.9;
            box-shadow: 0 4px 15px rgba(217, 83, 79, 0.25);
        }

        .footer-link {
            text-align: center;
            margin-top: 24px;
            color: var(--muted);
            font-size: 0.9rem;
        }
        .footer-link a {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
        }
        .footer-link a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    
    <div class="container">
        
        <div class="header">
            <div class="logo" aria-hidden="true">📚</div>
            <h2>Daftar Anggota Baru</h2>
            <p>Perpustakaan Multicomp</p>
        </div>

        @if ($errors->any())
            <div class="error-box">
                <p>Oops! Terjadi kesalahan:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="form-grid">

                <div class="input-group">
                    <label for="name">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Masukkan nama lengkap" class="@error('name') is-invalid @enderror">
                    @error('name')
                        <div class="input-error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="input-group">
                    <label for="nis">NISN</label>
                    <input id="nis" type="text" name="nis" value="{{ old('nis') }}" required placeholder="Masukkan NISN Anda" class="@error('nis') is-invalid @enderror">
                    @error('nis')
                        <div class="input-error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group full-width">
                    <label for="email">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" class="@error('email') is-invalid @enderror">
                    @error('email')
                        <div class="input-error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- ========================================================== -->
                <!-- --- PERUBAHAN 2: Mengganti Input Teks 'Kelas' --- -->
                <!-- ========================================================== -->

                <!-- INPUT 'class_name' LAMA DIHAPUS -->

                <!-- MENJADI DROPDOWN KELAS (TINGKAT) -->
                <div class="input-group">
                    <label for="class">Kelas</label>
                    <select id="class" name="class" class="@error('class') is-invalid @enderror" required>
                        <option value="">Pilih Tingkat Kelas</option>
                        <option value="X" {{ old('class') == 'X' ? 'selected' : '' }}>X</option>
                        <option value="XI" {{ old('class') == 'XI' ? 'selected' : '' }}>XI</option>
                        <option value="XII" {{ old('class') == 'XII' ? 'selected' : '' }}>XII</option>
                    </select>
                    @error('class')
                        <div class="input-error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- MENJADI DROPDOWN JURUSAN (DINAMIS) -->
                <div class="input-group">
                    <label for="major">Jurusan</label>
                    <select id="major" name="major" class="@error('major') is-invalid @enderror" required>
                        <option value="">Pilih Jurusan</option>
                        {{-- Loop data $majors (pastikan $majors dikirim dari AuthController) --}}
                        @if(isset($majors))
                            @foreach($majors as $major)
                                <option value="{{ $major->name }}" {{ old('major') == $major->name ? 'selected' : '' }}>
                                    {{ $major->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('major')
                        <div class="input-error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- INPUT 'phone_number' DIPINDAH KE BAWAH MENJADI full-width -->
                <div class="input-group full-width">
                    <label for="phone_number">Nomor WhatsApp (Aktif)</label>
                    <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}" required placeholder="Contoh: 081234567890" class="@error('phone_number') is-invalid @enderror">
                    @error('phone_number')
                        <div class="input-error-message">{{ $message }}</div>
                    @enderror
                </div>
                <!-- ========================================================== -->

                
                <div class="input-group full-width">
                    <label for="student_card_photo">Foto Kartu Pelajar (Untuk Verifikasi)</label>
                    <label class="file-upload-label" for="student_card_photo" id="file-label">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                        <span id="file-name">Klik untuk meng-upload</span>
                        <p>JPG, PNG, atau JPEG (Maks. 2MB)</p>
                    </label>
                    <input id="student_card_photo" class="file-input-hidden" type="file" name="student_card_photo" accept="image/*" required>
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
                
                <div class="input-group full-width">
                    <button type="submit" class="submit-btn">Daftar Sekarang</button>
                </div>
            </div>
        </form>

        <div class="footer-link">
            Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('student_card_photo');
        const fileNameSpan = document.getElementById('file-name');
        const fileLabel = document.getElementById('file-label');

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                // Tampilkan nama file
                fileNameSpan.textContent = e.target.files[0].name;
                // Ubah style label untuk menandakan file berhasil dipilih
                fileLabel.classList.add('has-file');
                fileLabel.querySelector('span').style.color = 'var(--success)';
            } else {
                fileNameSpan.textContent = 'Klik untuk meng-upload';
                fileLabel.classList.remove('has-file');
                fileLabel.querySelector('span').style.color = 'var(--accent)';
            }
        });
    </script>
</body>
</html>