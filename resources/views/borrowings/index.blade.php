<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman Saya</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f6f8f9;
            --card-bg: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --accent: #dc2626;
            --accent-dark: #b91c1c;
            --border-color: #e5e7eb;
            --success: #16a34a;
            --warning: #f59e0b;
            --radius: 12px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg);
            color: var(--text-primary);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .page-header h1 {
            font-size: 28px;
            color: var(--accent-dark);
            font-weight: 700;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            background-color: var(--accent);
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: var(--accent-dark);
        }
        
        .alert {
            padding: 16px;
            margin-bottom: 24px;
            border-radius: var(--radius);
            font-weight: 600;
            border: 1px solid transparent;
        }
        
        .alert.success {
            background-color: #dcfce7;
            color: #15803d;
            border-color: #bbf7d0;
        }
        
        .alert.error {
            background-color: #fee2e2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .borrowing-list {
            display: grid;
            gap: 20px;
        }

        .borrowing-card {
            display: flex;
            gap: 20px;
            background-color: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            border: 1px solid var(--border-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .borrowing-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .book-cover {
            flex-shrink: 0;
            width: 80px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
        }

        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .borrowing-details {
            flex-grow: 1;
        }

        .book-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .book-code {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 12px;
        }

        .borrowing-dates {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            font-size: 14px;
        }
        
        .date-item strong {
            display: block;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 4px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }

        .status-dipinjam { background-color: #fef3c7; color: #b45309; }
        .status-dikembalikan { background-color: #dcfce7; color: #16a34a; }
        .status-terlambat { background-color: #fee2e2; color: #b91c1c; }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background-color: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
        }
        
        .empty-state h2 {
            font-size: 22px;
            margin-bottom: 12px;
        }
        
        .empty-state p {
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        @media (max-width: 600px) {
            .borrowing-card {
                flex-direction: column;
            }
            .book-cover {
                width: 60px;
                height: 90px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="page-header">
            <h1>Riwayat Peminjaman Saya</h1>
            <a href="{{ route('dashboard') }}" class="btn">Kembali ke Dashboard</a>
        </header>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <div class="borrowing-list">
            @forelse ($borrowings as $borrowing)
                <div class="borrowing-card">
                    <div class="book-cover">
                        <img src="{{ $borrowing->bookCopy->book->cover_image ? Storage::url($borrowing->bookCopy->book->cover_image) : 'https://placehold.co/80x120/e2e8f0/64748b?text=Cover' }}" alt="Sampul Buku">
                    </div>

                    <div class="borrowing-details">
                        <h2 class="book-title">{{ $borrowing->bookCopy->book->title }}</h2>
                        <p class="book-code">Kode Buku: <strong>{{ $borrowing->bookCopy->book_code }}</strong></p>

                        <div class="borrowing-dates">
                            <div class="date-item">
                                <strong>Tanggal Pinjam</strong>
                                <span>{{ \Carbon\Carbon::parse($borrowing->borrowed_at)->format('d M Y') }}</span>
                            </div>
                            <div class="date-item">
                                <strong>Batas Pengembalian</strong>
                                <span>{{ \Carbon\Carbon::parse($borrowing->due_at)->format('d M Y') }}</span>
                            </div>
                            <div class="date-item">
                                <strong>Status</strong>
                                @if($borrowing->returned_at)
                                    <span class="status-badge status-dikembalikan">Dikembalikan</span>
                                @elseif(\Carbon\Carbon::now()->gt($borrowing->due_at))
                                    <span class="status-badge status-terlambat">Terlambat</span>
                                @else
                                    <span class="status-badge status-dipinjam">Dipinjam</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <h2>Anda Belum Memiliki Riwayat Peminjaman</h2>
                    <p>Silakan jelajahi katalog kami dan pinjam buku pertama Anda.</p>
                    <a href="{{ route('catalog.index') }}" class="btn">Lihat Katalog Buku</a>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>

