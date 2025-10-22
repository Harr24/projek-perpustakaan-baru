<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Dashboard - Perpustakaan Multicomp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#f6f8f9;
            --card:#ffffff;
            --muted:#667085;
            --accent:#d9534f;
            --accent-600:#b93a37;
            --success:#16a34a;
            --info: #3498db;
            --radius:12px;
            --container-max:1100px;
            --glass: rgba(255,255,255,0.6);
            font-size:16px;
        }
        *{box-sizing:border-box}
        html,body{height:100%}
        body{
            margin:0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background: linear-gradient(180deg,var(--bg),#eef3f6 60%);
            color:#0f1724;
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
            line-height:1.4;
        }
        .page{
            max-width:var(--container-max);
            margin:24px auto;
            padding:0 20px;
        }
        header.header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            background: linear-gradient(90deg,var(--accent),var(--accent-600));
            color:#fff;
            padding:18px 20px;
            border-radius:14px;
            box-shadow:0 8px 24px rgba(13,18,23,0.12);
        }
        .brand{
            display:flex;
            align-items:center;
            gap:14px;
            min-width: 0; 
        }
        .logo{
            width:48px;
            height:48px;
            border-radius:10px;
            background:rgba(255,255,255,0.12);
            display:grid;
            place-items:center;
            font-weight:700;
            font-size:18px;
            letter-spacing:0.6px;
            box-shadow:inset 0 -4px 12px rgba(0,0,0,0.08);
            overflow: hidden;
            flex-shrink: 0;
        }
        .title{
            display:flex;
            flex-direction:column;
            line-height:1;
        }
        .title h1{
            margin:0;
            font-size:1.1rem;
            font-weight:600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .title p{
            margin:0;
            font-size:0.85rem;
            opacity:0.95;
            color:rgba(255,255,255,0.92);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .header-actions{
            display:flex;
            align-items:center;
            gap:12px;
            flex-shrink: 0;
        }
        .btn{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:10px 14px;
            border-radius:10px;
            border:0;
            cursor:pointer;
            font-weight:600;
            text-decoration:none;
            font-size:0.95rem;
        }
        .logout-btn{
            background:#fff;
            color:var(--accent);
            box-shadow:0 6px 14px rgba(185,58,55,0.12);
        }
        .logout-btn:hover{background:#fff9f9;color:var(--accent-600)}
        .profile-badge{
            display:inline-flex;
            align-items:center;
            gap:10px;
            background:rgba(255,255,255,0.08);
            padding:8px 12px;
            border-radius:999px;
            color:#fff;
            font-weight:600;
            font-size:0.9rem;
        }
        .grid{
            display:grid;
            gap:24px;
            margin-top:24px;
        }
        @media (min-width: 992px) {
            .grid {
                grid-template-columns: 1fr 1fr;
            }
            .grid-full-width {
                grid-column: 1 / -1; 
            }
        }
        .card{
            background:var(--card);
            border-radius:var(--radius);
            box-shadow:0 8px 20px rgba(15,23,36,0.06);
            padding:22px;
        }
        .welcome{
            display:flex;
            align-items:flex-start;
            gap:16px;
        }
        .welcome .icon{
            width:56px;
            height:56px;
            border-radius:12px;
            display:grid;
            place-items:center;
            background:linear-gradient(135deg,#fff,#f8fafc);
            box-shadow:0 6px 18px rgba(15,23,36,0.04);
            font-size:22px;
            color: var(--accent);
            flex-shrink: 0;
        }
        .welcome h2{ margin:0 0 8px 0; font-size:1.1rem; }
        .welcome p{margin:0;color:var(--muted);font-size:0.95rem}
        
        .nav-list{
            margin-top:18px;
            display:flex;
            flex-direction:column;
            gap:10px;
        }
        
        .nav-item{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            padding:12px 14px;
            border-radius:10px;
            border:1px solid #eef2f5;
            text-decoration:none;
            color:var(--accent-600);
            background:linear-gradient(180deg, rgba(217,83,79,0.03), transparent);
            transition:transform .12s ease, box-shadow .12s ease;
            font-weight:600;
        }
        .nav-item:hover{
            transform:translateY(-4px);
            box-shadow:0 12px 30px rgba(13,18,23,0.06);
            color:#fff;
            background:linear-gradient(90deg,var(--accent),var(--accent-600));
            border-color:transparent;
        }
        .nav-item .meta{font-size:0.9rem;color:var(--muted);font-weight:500}
        .nav-item-main {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-grow: 1;
        }
        .badge {
            background-color: #ffffff;
            color: var(--accent-600);
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 0.8rem;
            font-weight: 700;
            line-height: 1.2;
            min-width: 22px; height: 22px;
            display: grid;
            place-items: center;
            border: 1px solid var(--accent);
        }
        .nav-item:hover .badge {
            background-color: var(--accent);
            color: #fff;
            border-color: #fff;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
        }
        .stat-card {
            background: #f8fafc;
            border-radius: 10px;
            padding: 16px;
            border: 1px solid #eef2f5;
            text-align: center;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-600);
            line-height: 1.1;
        }
        .stat-card .label {
            font-size: 0.9rem;
            color: var(--muted);
            margin-top: 4px;
        }
        .clock {
            font-weight: 600;
            background: rgba(255,255,255,0.1);
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.95rem;
            min-width: 80px;
            text-align: center;
        }
        .last-login {
            text-align: center;
            padding: 10px;
            font-size: 0.85rem;
            color: var(--muted);
            background: #eef2f5;
            border-radius: 10px;
            margin-top: 20px;
        }
        .guide-card h2 {
            margin: 0 0 4px 0;
            font-size: 1.2rem;
            color: var(--accent-600);
        }
        .guide-card p.subtitle {
            color:var(--muted); 
            margin-top:0; 
            margin-bottom:20px;
            font-size: 0.95rem;
        }
        .guide-section {
            position: relative;
            padding-left: 30px;
            margin-bottom: 14px;
        }
        .guide-section span.icon {
            position: absolute;
            left: 0;
            top: 2px;
            font-size: 1.1rem;
        }
        .guide-section h4 {
            margin: 0 0 2px 0;
            font-size: 1rem;
            font-weight: 600;
        }
        .guide-section p {
            margin: 0;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .alert-pending {
            background-color: #fff9f0;
            border: 1px solid #ffe6b3;
            color: #b96a00;
            padding: 14px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            margin-top: 16px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-pending a {
            color: var(--accent-600);
            text-decoration: underline;
            font-weight: 700;
        }
        .alert-pending span {
            font-size: 1.2rem;
        }
        
        @media (max-width: 700px) {
            header.header {
                flex-wrap: wrap; 
                justify-content: center;
                gap: 12px;
            }
            .header-actions {
                width: 100%; 
                justify-content: space-between;
            }
            .clock {
                display: none;
            }
        }
        
        
        {{-- ============================ --}}
        {{-- CSS UNTUK WIDGET STATUS BARU --}}
        {{-- ============================ --}}
        .widget-title {
            margin: 0 0 16px 0;
            font-size: 1.2rem;
            color: var(--accent-600);
        }

        /* Tampilan Kartu Kutipan */
        .quote-card {
            margin: 0;
            padding: 16px;
            background: #fdf8f8;
            border-left: 4px solid var(--accent);
            border-radius: 8px;
        }
        .quote-card p {
            margin: 0 0 8px 0;
            font-size: 1rem;
            font-style: italic;
            color: #333;
        }
        .quote-card footer {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--muted);
        }

        /* Tampilan Tombol */
        .btn-widget-full {
            display: block;
            width: 100%;
            text-align: center;
            padding: 12px;
            margin-top: 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            background: var(--accent);
            color: #fff;
            border: 1px solid var(--accent);
            transition: all .2s ease;
        }
        .btn-widget-full:hover {
            background: var(--accent-600);
        }
        .btn-widget-full.btn-start-reading {
             background: var(--success);
             border-color: var(--success);
        }
        .btn-widget-full.btn-start-reading:hover {
            background: #148a3e; /* Warna hijau lebih gelap */
        }

        /* Tampilan Kartu Sedang Pinjam */
        .active-borrowing-card {
            display: flex;
            gap: 16px;
            background: #f8fafc;
            border: 1px solid #eef2f5;
            border-radius: 10px;
            padding: 14px;
        }
        .borrowed-cover {
            width: 80px;
            height: 110px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .borrowed-cover-placeholder {
            width: 80px;
            height: 110px;
            border-radius: 8px;
            flex-shrink: 0;
            background: #eef2f5;
            display: grid;
            place-items: center;
            text-align: center;
            font-size: 0.85rem;
            color: var(--muted);
        }
        .borrowed-info {
            display: flex;
            flex-direction: column;
            justify-content: center; /* Agar rapi */
        }
        .borrowed-info h4 {
            margin: 0 0 10px 0;
            font-size: 1.1rem;
            line-height: 1.3;
        }
        .borrowed-meta {
            font-size: 0.9rem;
            color: var(--muted);
            margin-bottom: 4px;
        }
        .borrowed-meta strong {
            color: #0f1724;
            font-weight: 600;
            margin-left: 6px;
        }
        
    </style>
</head>
<body>
    <div class="page">
        <header class="header" role="banner">
            <div class="brand">
                <div class="logo" aria-hidden="true">
                    @if(Auth::user()->profile_photo)
                        <img src="{{ Storage::url(Auth::user()->profile_photo) }}" alt="Foto Profil" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    @endif
                </div>
                <div class="title">
                    <h1>Selamat Datang, {{ strtok(Auth::user()->name, " ") }}!</h1>
                    <p>Dashboard {{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </div>
            <div class="header-actions">
                <div id="clock" class="clock">00:00:00</div>
                <div class="profile-badge" role="img" aria-label="Role Pengguna">
                    {{ ucfirst(Auth::user()->role) }}
                </div>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn logout-btn" aria-label="Logout">LOGOUT</button>
                </form>
            </div>
        </header>

        <main class="grid" role="main">

            @if(Auth::user()->role == 'petugas' || Auth::user()->role == 'superadmin')
            <section class="card grid-full-width">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="value">{{ $totalBuku ?? '0' }}</div>
                        <div class="label">Total Buku</div>
                    </div>
                    <div class="stat-card">
                        <div class="value">{{ $anggotaAktif ?? '0' }}</div>
                        <div class="label">Anggota Aktif</div>
                    </div>
                    <div class="stat-card">
                        <div class="value">{{ $pengajuanPinjaman ?? '0' }}</div>
                        <div class="label">Pengajuan Baru</div>
                    </div>
                    <div class="stat-card">
                        <div class="value">{{ $terlambat ?? '0' }}</div>
                        <div class="label">Terlambat</div>
                    </div>
                </div>
            </section>
            @endif
            
            {{-- KARTU 1: WELCOME & NAV-LIST --}}
            <section class="card" aria-labelledby="welcomeTitle">
                <div class="welcome">
                    <div class="icon" aria-hidden="true">📚</div>
                    <div>
                        <h2 id="welcomeTitle">Anda berhasil login ke sistem perpustakaan.</h2>
                        <p>Pilih menu navigasi di bawah ini untuk memulai.</p>
                        
                        @if(Auth::user()->role == 'petugas' || Auth::user()->role == 'superadmin')
                            @if(isset($pendingStudentsCount) && $pendingStudentsCount > 0)
                                <div class="alert-pending">
                                    <span>🔔</span>
                                    <div>
                                        Ada <strong>{{ $pendingStudentsCount }} siswa</strong> menunggu verifikasi. 
                                        <a href="{{ route('admin.petugas.verification.index') }}">Lihat di sini</a>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="nav-list" id="navList">
                            @switch(Auth::user()->role)
                                @case('superadmin')
                                    @include('dashboard-partials.superadmin')
                                    @break

                                @case('petugas')
                                    @include('dashboard-partials.petugas')
                                    @break
                                
                                @case('guru')
                                    <a href="{{ route('guru.materials.index') }}" class="nav-item">
                                        <div class="nav-item-main">
                                            <span>Kelola Materi Pembelajaran</span>
                                        </div>
                                        <span class="meta">Tambah/edit materi</span>
                                    </a>
                                    {{-- ============================================= --}}
                                    {{-- PERBAIKAN: Mengirim variabel $hasBorrowings --}}
                                    {{-- ============================================= --}}
                                    @include('dashboard-partials.member', ['hasBorrowings' => $hasBorrowings ?? false])
                                    @break

                                @case('siswa')
                                    {{-- ============================================= --}}
                                    {{-- PERBAIKAN: Mengirim variabel $hasBorrowings --}}
                                    {{-- ============================================= --}}
                                    @include('dashboard-partials.member', ['hasBorrowings' => $hasBorrowings ?? false])
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>
            </section>

            {{-- =================================== --}}
            {{-- KARTU BARU: WIDGET STATUS DINAMIS --}}
            {{-- =================================== --}}
            @if(Auth::user()->role == 'siswa' || Auth::user()->role == 'guru')
            <section class="card" aria-labelledby="widgetTitle">
                {{-- Memanggil partial widget baru dan mengirimkan datanya --}}
                @include('dashboard-partials.status-widget', [
                    'activeBorrowing' => $activeBorrowing ?? null,
                    'quote' => $quote ?? null
                ])
            </section>
            @endif


            {{-- KARTU PANDUAN UNTUK ADMIN/PETUGAS --}}
            @if(Auth::user()->role == 'petugas')
            <section class="card guide-card" aria-labelledby="guideTitle">
                <h2 id="guideTitle">🚀 Panduan Cepat Petugas</h2>
                <p class="subtitle">Alur kerja utama Anda sebagai petugas.</p>
            
                <div class="guide-section">
                    <span class="icon">🗃️</span>
                    <h4>Data Master</h4>
                    <p>Kelola data inti seperti genre dan daftar buku.</p>
                </div>

                <div class="guide-section">
                    <span class="icon">👥</span>
                    <h4>Anggota</h4>
                    <p>Verifikasi siswa baru dan kelola akun guru.</p>
                </div>

                <div class="guide-section">
                    <span class="icon">🔄</span>
                    <h4>Sirkulasi Buku</h4>
                    <p>Proses pengajuan pinjaman, pengembalian, dan denda.</p>
                </div>

                <div class="guide-section">
                    <span class="icon">📊</span>
                    <h4>Laporan</h4>
                    <p>Lihat rekapitulasi data peminjaman di menu Laporan.</p>
                </div>
                
                @if(Auth::user()->last_login_at)
                <div class="last-login">
                    Login terakhir: {{ Auth::user()->last_login_at->format('d M Y, H:i') }}
                </div>
                @endif
            </section>

            @elseif(Auth::user()->role == 'superadmin')
            <section class="card guide-card" aria-labelledby="guideTitle">
                <h2 id="guideTitle">🚀 Panduan Cepat Superadmin</h2>
                <p class="subtitle">Alur kerja utama Anda sebagai superadmin.</p>
            
                <div class="guide-section">
                    <span class="icon">👨‍💼</span>
                    <h4>Manajemen Staf</h4>
                    <p>Tambah, edit, atau hapus akun untuk petugas perpustakaan.</p>
                </div>

                <div class="guide-section">
                    <span class="icon">👥</span>
                    <h4>Kelola Anggota</h4>
                    <p>Kelola semua data anggota (siswa & guru) yang terdaftar.</p>
                </div>

                <div class="guide-section">
                    <span class="icon">🖥️</span>
                    <h4>Tampilan Depan</h4>
                    <p>Mengatur gambar dan tautan pada Hero Slider di halaman utama.</p>
                </div>

                <div class="guide-section">
                    <span class="icon">⚙️</span>
                    <h4>Akun</h4>
                    <p>Mengelola profil dan kata sandi akun superadmin Anda.</p>
                </div>
                
                @if(Auth::user()->last_login_at)
                <div class="last-login">
                    Login terakhir: {{ Auth::user()->last_login_at->format('d M Y, H:i') }}
                </div>
                @endif
            </section>
            @endif
            </main>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const options = { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            const timeString = now.toLocaleTimeString('id-ID', options);
            
            const clockElement = document.getElementById('clock');
            if (clockElement) {
                clockElement.textContent = timeString.replace(/\./g, ':');
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>