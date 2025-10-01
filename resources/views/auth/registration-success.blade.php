<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #c62828 0%, #b71c1c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: #f5f5f5;
            clip-path: polygon(30% 0, 100% 0, 100% 100%, 0 100%);
            z-index: 0;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 480px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
            animation: slideUp 0.5s ease-out;
            position: relative;
            z-index: 1;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-container {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #c62828 0%, #b71c1c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .checkmark {
            width: 40px;
            height: 40px;
            border: 3px solid white;
            border-radius: 50%;
            position: relative;
        }

        .checkmark:after {
            content: '';
            position: absolute;
            left: 11px;
            top: 5px;
            width: 10px;
            height: 18px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }

        h1 {
            color: #c62828;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 35px;
        }

        .info-box {
            background: #fff5f5;
            border-left: 4px solid #c62828;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-box p {
            margin: 0;
            color: #555;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #c62828 0%, #b71c1c 100%);
            color: white;
            padding: 14px 40px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(198, 40, 40, 0.4);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(198, 40, 40, 0.6);
            background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
        }

        .btn:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            body::before {
                width: 40%;
                clip-path: polygon(50% 0, 100% 0, 100% 100%, 0 100%);
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 40px 25px;
            }

            h1 {
                font-size: 24px;
            }

            p {
                font-size: 14px;
            }

            body::before {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-container">
            <div class="checkmark"></div>
        </div>
        
        <h1>Registrasi Berhasil!</h1>
        
        <p>Terima kasih telah mendaftar. Akun Anda sedang dalam proses verifikasi oleh petugas.</p>
        
        <div class="info-box">
            <p><strong>📋 Langkah Selanjutnya:</strong><br>
            Silakan tunggu konfirmasi verifikasi melalui email. Anda akan dapat login setelah akun diaktifkan oleh admin.</p>
        </div>
        
        <a href="{{ route('login') }}" class="btn">Kembali ke Login</a>
    </div>
</body>
</html>