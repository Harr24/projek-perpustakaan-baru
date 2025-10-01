<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Genre Baru</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #f8f8f8;
            border: 2px solid #d32f2f;
            border-radius: 10px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h1 {
            color: #d32f2f;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .error {
            color: #d32f2f;
            font-size: 14px;
            margin-bottom: 10px;
        }

        button {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #b71c1c;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Genre Baru</h1>
        <form action="{{ route('admin.petugas.genres.store') }}" method="POST">
            @csrf
            <label for="name">Nama Genre:</label>
            <input type="text" id="name" name="name" required>
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>
