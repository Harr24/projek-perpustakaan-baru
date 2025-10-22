<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Genre Baru</title>
    {{-- Import Bootstrap CSS untuk styling tombol yang konsisten --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container-card {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-left: 4px solid #d32f2f;
            border-radius: 8px;
            padding: 30px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        h1 {
            color: #d32f2f;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .form-label {
            font-weight: 600;
            color: #343a40;
        }

        .form-control {
             border-radius: 6px;
             padding: 0.5rem 0.75rem;
        }
        .form-control:focus {
             border-color: #d32f2f;
             box-shadow: 0 0 0 0.25rem rgba(211, 47, 47, 0.25);
        }

        .invalid-feedback {
            display: block;
            font-size: 0.875em;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 0.6rem 1rem;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 500;
            text-align: center;
            flex-grow: 1;
        }

    </style>
</head>
<body>
    <div class="container-card">
        <h1>Tambah Genre Baru</h1>
        <form action="{{ route('admin.petugas.genres.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nama Genre:</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Input untuk genre_code dihapus dari sini --}}

            <div class="button-group">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
                <a href="{{ route('admin.petugas.genres.index') }}" class="btn btn-outline-secondary">
                   <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

