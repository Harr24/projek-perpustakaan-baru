<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Denda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        /* Menambahkan style untuk input group agar pas */
        .form-cicilan {
            min-width: 220px;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Manajemen Denda</h1>
            <p class="text-muted mb-0">Daftar denda keterlambatan yang belum lunas.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.petugas.fines.history') }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history"></i> Lihat Riwayat Denda
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-danger">Kembali ke Dashboard</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <!-- Menampilkan error validasi spesifik dari form -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">Terjadi Kesalahan!</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Denda Belum Lunas</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3">Nama Peminjam</th>
                            <th class="py-3 px-3">Kelas</th>
                            <th class="py-3 px-3">Kontak (WA)</th>
                            <th class="py-3 px-3">Judul Buku</th>
                            <th class="py-3 px-3">Detail Denda</th>
                            <th class="py-3 px-3">Telat (Hari Kerja)</th>
                            <th class="py-3 px-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unpaidFines as $fine)
                            <tr>
                                <td class="px-3">{{ $fine->user->name ?? 'User Terhapus' }}</td>
                                
                                {{-- ========================================================== --}}
                                {{-- --- 🔥 LOGIKA KELAS DIPERBAIKI (ANTI N/A) 🔥 --- --}}
                                {{-- ========================================================== --}}
                                <td class="px-3">
                                    @if ($fine->user)
                                        @if ($fine->user->role == 'siswa')
                                            {{-- 1. Cek Lulus --}}
                                            @if ($fine->user->class == 'Lulus')
                                                <span class="badge bg-secondary">LULUS</span>
                                            
                                            {{-- 2. Data Lengkap --}}
                                            @elseif (!empty($fine->user->class) && !empty($fine->user->major))
                                                {{ $fine->user->class }} - {{ $fine->user->major }}
                                            
                                            {{-- 3. Data Parsial --}}
                                            @elseif (!empty($fine->user->class) || !empty($fine->user->major))
                                                {{ $fine->user->class }} {{ $fine->user->major }}
                                            
                                            {{-- 4. Data Lama --}}
                                            @elseif (!empty($fine->user->class_name))
                                                {{ $fine->user->class_name }}
                                            
                                            {{-- 5. Kosong --}}
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif

                                        @elseif ($fine->user->role == 'guru')
                                            <span class="badge bg-info text-dark">Guru</span>
                                        @else
                                            -
                                        @endif
                                    @else
                                        <span class="text-muted small">User Hilang</span>
                                    @endif
                                </td>
                                {{-- ========================================================== --}}

                                <td class="px-3">
                                    @if($fine->user && $fine->user->phone_number)
                                        @php
                                            $cleanedPhone = preg_replace('/[^0-9]/', '', $fine->user->phone_number);
                                            $waNumber = (substr($cleanedPhone, 0, 1) === '0') ? '62' . substr($cleanedPhone, 1) : $cleanedPhone;
                                        @endphp
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="btn btn-sm btn-outline-success" title="Chat {{ $fine->user->name }} di WhatsApp">
                                            <i class="bi bi-whatsapp"></i> Chat
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="px-3">
                                    {{ $fine->bookCopy->book->title ?? 'Buku Terhapus' }}
                                    <small class="d-block text-muted">{{ $fine->bookCopy->book_code ?? '-' }}</small>
                                </td>

                                <td class="px-3">
                                    <div style="min-width: 170px;">
                                        <small class="d-block text-muted">Total: Rp {{ number_format($fine->fine_amount, 0, ',', '.') }}</small>
                                        <small class="d-block text-success">Dibayar: Rp {{ number_format($fine->fine_paid ?? 0, 0, ',', '.') }}</small>
                                        <strong class="d-block text-danger">Sisa: Rp {{ number_format($fine->fine_amount - ($fine->fine_paid ?? 0), 0, ',', '.') }}</strong>
                                    </div>
                                </td>
                                
                                <td class="px-3">
                                    <span class="badge bg-danger">{{ $fine->late_days }} hari</span>
                                </td>
                                
                                <td class="px-3">
                                    <form action="{{ route('admin.petugas.fines.pay', $fine) }}" method="POST" class="form-cicilan">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                   name="amount" 
                                                   class="form-control" 
                                                   placeholder="Jml Bayar" 
                                                   aria-label="Jumlah Bayar"
                                                   required
                                                   min="1"
                                                   max="{{ $fine->fine_amount - ($fine->fine_paid ?? 0) }}"
                                                   value="{{ $fine->fine_amount - ($fine->fine_paid ?? 0) }}"
                                                   title="Masukkan jumlah bayar (cicilan)">
                                            <button type="submit" class="btn btn-success" title="Bayar">
                                                <i class="bi bi-cash-stack"></i> Bayar
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-check-circle display-4 text-success mb-2"></i>
                                        <p class="mb-0">Tidak ada denda yang belum lunas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>