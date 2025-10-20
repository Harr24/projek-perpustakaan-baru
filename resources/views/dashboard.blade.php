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
            flex-shrink: 0; /* Mencegah logo menyusut */
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
        }
        .title p{
            margin:0;
            font-size:0.85rem;
            opacity:0.95;
            color:rgba(255,255,255,0.92);
        }
        .header-actions{
            display:flex;
            align-items:center;
            gap:12px;
            flex-shrink: 0; /* Mencegah grup tombol menyusut */
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
            gap:20px;
            margin-top:20px;
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
        
        /* ========================================================== */
        /* PERBAIKAN UTAMA: Tambahkan Media Query untuk Responsif */
        /* ========================================================== */
        @media (max-width: 600px) {
            header.header {
                flex-wrap: wrap; /* Izinkan item turun ke baris baru */
                justify-content: center; /* Pusatkan item saat baris baru terbentuk */
                gap: 12px; /* Kurangi jarak antar item */
            }
            .header-actions {
                width: 100%; /* Buat grup tombol memenuhi lebar */
                justify-content: space-between;
            }
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
            <section class="card" aria-labelledby="welcomeTitle">
                <div class="welcome">
                    <div class="icon" aria-hidden="true">📚</div>
                    <div>
                        <h2 id="welcomeTitle">Anda berhasil login ke sistem perpustakaan.</h2>
                        <p>Pilih menu navigasi di bawah ini untuk memulai.</p>
                        
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
                                    @include('dashboard-partials.member')
                                    @break

                                @case('siswa')
                                    @include('dashboard-partials.member')
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

