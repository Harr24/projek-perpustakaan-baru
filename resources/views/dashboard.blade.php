<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard Petugas</title>
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

    /* Reset ringkas */
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

    /* Layout */
    .page{
      max-width:var(--container-max);
      margin:24px auto;
      padding:20px;
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

    /* Right area header */
    .header-actions{
      display:flex;
      align-items:center;
      gap:12px;
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

    /* Mobile hamburger */
    .hamburger{
      display:none;
      width:44px;
      height:44px;
      border-radius:10px;
      background:rgba(255,255,255,0.12);
      border:0;
      color:#fff;
      cursor:pointer;
      align-items:center;
      justify-content:center;
    }

    /* Main content */
    .grid{
      display:grid;
      grid-template-columns: 1fr 360px;
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
    }

    .welcome h2{
      margin:0 0 8px 0;
      font-size:1.1rem;
    }

    .welcome p{margin:0;color:var(--muted);font-size:0.95rem}

    /* Navigation list */
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

    /* Right column summary */
    .summary{
      display:flex;
      flex-direction:column;
      gap:12px;
    }

    .stat{
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:12px;
      border-radius:10px;
      background:linear-gradient(180deg,#fff,#fbfdff);
      border:1px solid #eef3f6;
    }

    .stat .label{color:var(--muted);font-weight:600}
    .stat .value{font-weight:700;font-size:1.05rem}

    /* Small helper */
    .muted{color:var(--muted);font-size:0.92rem}

    /* Focus states */
    a:focus, button:focus{outline:3px solid rgba(217,83,79,0.18);outline-offset:3px;border-radius:8px}

    /* Responsive rules */
    @media (max-width:900px){
      .grid{grid-template-columns:1fr; padding-bottom:10px}
      .header{padding:14px}
      .brand .logo{width:44px;height:44px}
      .header-actions{gap:8px}
      .hamburger{display:flex}
      .profile-badge{display:none}
      .page{padding:12px}
    }

    @media (max-width:480px){
      .title h1{font-size:1rem}
      .nav-item{padding:12px}
      .logout-btn{padding:9px 12px}
    }
  </style>
</head>
<body>
  <div class="page">
    <header class="header" role="banner">
      <div class="brand" aria-hidden="false">
        <div class="logo" aria-hidden="true">LP</div>
        <div class="title">
          <h1>Selamat Datang, {{ Auth::user()->name }}!</h1>
          <p class="muted">Dashboard Petugas Perpustakaan</p>
        </div>
      </div>

      <div class="header-actions">
        <button class="hamburger" id="menuToggle" aria-label="Buka menu navigasi" aria-expanded="false">
          <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect width="20" height="2" rx="1" fill="white"></rect>
            <rect y="6" width="20" height="2" rx="1" fill="white" opacity="0.9"></rect>
            <rect y="12" width="20" height="2" rx="1" fill="white" opacity="0.8"></rect>
          </svg>
        </button>

        <div class="profile-badge" role="img" aria-label="Nama pengguna">
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
            <p class="muted">Akses cepat untuk mengelola genre, menambah buku baru dan melihat daftar buku</p>

            <div class="nav-list" id="navList">
              @if(Auth::user()->role == 'petugas')
                <a class="nav-item" href="{{ route('admin.petugas.genres.index') }}">
                  <span>Kelola Genre</span>
                  <span class="meta">Genre</span>
                </a>

                <a class="nav-item" href="{{ route('admin.petugas.books.create') }}">
                  <span>Tambah Buku Baru</span>
                  <span class="meta">Tambah</span>
                </a>

                <a class="nav-item" href="{{ route('admin.petugas.books.index') }}">
                  <span>Kelola Buku</span>
                  <span class="meta">Daftar Buku</span>
                </a>
              @endif

              @if(Auth::user()->role == 'siswa')
                <a class="nav-item" href="#">
                  <span>Lihat Riwayat Peminjaman</span>
                  <span class="meta">Riwayat</span>
                </a>
              @endif
            </div>
          </div>
        </div>
      </section>

      <aside class="summary" aria-labelledby="summaryTitle">
        <div class="card stat" role="status" aria-live="polite">
          <div>
            <div class="label" id="summaryTitle">Ringkasan Cepat</div>
            <div class="muted">Statistik dasar sistem</div>
          </div>
          <div class="value">--</div>
        </div>

        <div class="card" style="padding:14px;">
          <div class="muted" style="margin-bottom:8px;font-weight:600">Petunjuk</div>
          <div class="muted" style="font-size:0.92rem">Gunakan tombol di samping untuk mengakses fungsi utama. Di layar kecil ketuk ikon menu untuk melihat navigasi.</div>
        </div>
      </aside>
    </main>
  </div>

  <script>
    // Toggle visi menu navigasi pada layar kecil
    (function(){
      const btn = document.getElementById('menuToggle');
      const navList = document.getElementById('navList');
      let open = false;
      btn.addEventListener('click', function(){
        open = !open;
        btn.setAttribute('aria-expanded', String(open));
        if(open){
          navList.style.display = 'flex';
          navList.style.flexDirection = 'column';
        } else {
          navList.style.display = '';
        }
      });

      // Pastikan navigasi terlihat secara default di layar besar
      function check(){
        if(window.innerWidth > 900){
          navList.style.display = '';
          btn.setAttribute('aria-expanded','false');
        } else {
          navList.style.display = 'none';
          btn.setAttribute('aria-expanded','false');
        }
      }
      window.addEventListener('resize', check);
      check();
    })();
  </script>
</body>
</html>