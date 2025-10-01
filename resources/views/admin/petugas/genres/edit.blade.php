<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Genre - {{ $genre->name }}</title>

  <!-- Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --brand-red: #c62828;
      --brand-red-dark: #a21f1f;
    }
    body{ font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, Arial; background:#f6f7fb; }
    .topbar{ background: linear-gradient(180deg, var(--brand-red), var(--brand-red-dark)); color:#fff; }
    .card-hero{ margin-top:-28px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
    .form-card{ border-left:4px solid var(--brand-red); }
    .btn-danger-soft{ background: rgba(198,40,40,0.08); color:var(--brand-red); border:1px solid rgba(198,40,40,0.12); }
    label.required::after{ content: " *"; color:#d11; }
    .help-text{ font-size:0.9rem; color:#6c757d; }
  </style>
</head>
<body>

  <!-- Topbar -->
  <header class="topbar py-3">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center" style="width:44px;height:44px;font-weight:700;">
          LP
        </div>
        <div>
          <div class="small text-white-50">Selamat Datang,</div>
          <div class="h6 mb-0">Petugas</div>
        </div>
      </div>
      <div>
        <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm">
          <i class="bi bi-box-arrow-right"></i> LOGOUT
        </a>
      </div>
    </div>
  </header>

  <main class="container py-4">
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card card-hero mb-3">
          <div class="card-body">
            <h1 class="h4 mb-1">Edit Genre: <strong>{{ $genre->name }}</strong></h1>
            <p class="text-muted mb-0">Perbarui nama genre buku. Pastikan nama tidak duplikat dan deskriptif.</p>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="card form-card">
          <div class="card-body">
            <form action="{{ route('admin.petugas.genres.update', $genre->id) }}" method="POST" class="needs-validation" novalidate>
              @csrf
              @method('PUT')

              <div class="mb-3">
                <label for="name" class="form-label required">Nama Genre</label>
                <input type="text" id="name" name="name" value="{{ old('name', $genre->name) }}" required
                       class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Fiksi, Sejarah">
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @else
                  <div class="form-text help-text">Nama genre maksimal 100 karakter.</div>
                @enderror
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger">
                  <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('admin.petugas.genres.index') }}" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn btn-outline-danger ms-auto" data-bs-toggle="modal" data-bs-target="#deleteModal">
                  <i class="bi bi-trash"></i> Hapus Genre
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Delete confirmation modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header border-0">
                <h5 class="modal-title">Hapus Genre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus genre <strong>{{ $genre->name }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
              </div>
              <div class="modal-footer">
                <form action="{{ route('admin.petugas.genres.destroy', $genre->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                  <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="card sidebar-card mb-3">
          <div class="card-body">
            <h6 class="mb-2">Ringkasan Cepat</h6>
            <p class="mb-1"><strong>Total Genre:</strong> {{ \App\Models\Genre::count() }}</p>
            <p class="mb-0 text-muted">Gunakan nama singkat dan konsisten untuk memudahkan filter dan laporan.</p>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h6 class="mb-2">Petunjuk</h6>
            <ul class="mb-0">
              <li class="mb-1">Nama genre wajib diisi dan unik.</li>
              <li class="mb-1">Hindari karakter khusus yang tidak perlu.</li>
              <li class="mb-1">Gunakan tombol Hapus hanya jika tidak ada buku yang terhubung.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // simple client-side validation feedback
    (function () {
      'use strict'
      var forms = document.querySelectorAll('.needs-validation')
      Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
    })()
  </script>
</body>
</html>
