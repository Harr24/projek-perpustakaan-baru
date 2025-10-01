<section class="page">
  <header class="page-header">
    <div>
      <h1 class="page-title">Daftar Genre</h1>
      <p class="page-sub">Kelola genre buku di perpustakaan</p>
    </div>

    <div class="actions">
      <a class="btn btn-ghost" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
      <a class="btn btn-primary" href="{{ route('admin.petugas.genres.create') }}">Tambah Genre Baru</a>
    </div>
  </header>

  @if(session('success'))
    <div class="alert success" role="status">{{ session('success') }}</div>
  @endif

  <div class="table-wrap" role="table" aria-label="Daftar Genre">
    <div class="table-head">
      <div class="cell col-no">No</div>
      {{-- PERUBAHAN 1: Menambahkan header kolom Kode Genre --}}
      <div class="cell col-code">Kode Genre</div>
      <div class="cell col-name">Nama Genre</div>
      <div class="cell col-action">Aksi</div>
    </div>

    <div class="table-body">
      @forelse ($genres as $genre)
        <div class="row" role="row">
          <div class="cell col-no" data-label="No">{{ $loop->iteration }}</div>
          {{-- PERUBAHAN 2: Menampilkan data Kode Genre --}}
          <div class="cell col-code" data-label="Kode Genre">{{ $genre->genre_code }}</div>
          <div class="cell col-name" data-label="Nama Genre">{{ $genre->name }}</div>
          <div class="cell col-action" data-label="Aksi">
            <a class="action edit" href="{{ route('admin.petugas.genres.edit', $genre->id) }}" aria-label="Edit {{ $genre->name }}">Edit</a>

            <form class="inline-form" action="{{ route('admin.petugas.genres.destroy', $genre->id) }}" method="POST" onsubmit="return confirmDelete(event, this);" aria-label="Hapus {{ $genre->name }}">
              @csrf
              @method('DELETE')
              <button type="submit" class="action delete">Hapus</button>
            </form>
          </div>
        </div>
      @empty
        <div class="row empty">
          <div class="cell" style="width:100%;">Belum ada data genre.</div>
        </div>
      @endforelse
    </div>
  </div>
</section>

<style>
  :root{
    --bg:#fff;
    --muted:#6b7280;
    --red:#d9534f;
    --red-dark:#b93a37;
    --card:#ffffff;
    --radius:10px;
    --gap:14px;
    font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  }

  .page{max-width:1100px;margin:20px auto;padding:18px}
  .page-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:16px}
  .page-title{margin:0;font-size:1.25rem;color:var(--red-dark)}
  .page-sub{margin:3px 0 0;color:var(--muted);font-size:0.95rem}

  .actions{display:flex;gap:8px;align-items:center}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;font-weight:600;text-decoration:none;border:1px solid transparent;cursor:pointer}
  .btn-primary{background:var(--red);color:#fff;box-shadow:0 8px 20px rgba(217,83,79,0.12)}
  .btn-primary:hover{background:var(--red-dark)}
  .btn-ghost{background:transparent;color:var(--red);border-color:transparent}
  .alert{padding:10px 14px;border-radius:8px;margin-bottom:12px;font-weight:600}
  .alert.success{background:#eefdf5;color:#065f46;border:1px solid rgba(16,185,129,0.08)}

  /* Table like layout */
  .table-wrap{background:var(--card);border-radius:var(--radius);padding:8px;border:1px solid #f1f5f9;box-shadow:0 8px 24px rgba(15,23,36,0.04)}
  
  /* PERUBAHAN 3: Menyesuaikan layout grid untuk kolom baru */
  .table-head{display:grid;grid-template-columns:80px 120px 1fr 220px;gap:8px;padding:12px 14px;border-bottom:1px solid #f3f4f6;align-items:center}
  
  .table-head .cell{font-weight:700;color:var(--red-dark);font-size:0.95rem}
  .table-body{display:flex;flex-direction:column;gap:10px;padding:12px 6px}

  /* PERUBAHAN 4: Menyesuaikan layout grid untuk kolom baru */
  .row{display:grid;grid-template-columns:80px 120px 1fr 220px;gap:8px;align-items:center;padding:12px;border-radius:10px;border:1px solid transparent;transition:transform .14s ease,box-shadow .14s ease}
  
  .row:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(15,23,36,0.06)}
  .row.empty{justify-content:center;background:transparent;border:none;box-shadow:none;padding:20px;color:var(--muted);font-weight:600}

  .cell{padding:6px 10px}
  .col-no{color:var(--muted);font-weight:600}
  .col-code{font-weight:700;color:var(--muted)} /* Styling untuk kode genre */
  .col-name{font-weight:700}
  .col-action{display:flex;gap:8px;justify-content:flex-end;align-items:center}

  .action{padding:8px 10px;border-radius:8px;text-decoration:none;font-weight:700;cursor:pointer;border:1px solid transparent}
  .action.edit{background:transparent;color:var(--red);border-color:rgba(217,83,79,0.06)}
  .action.edit:hover{background:rgba(217,83,79,0.06)}
  .action.delete{background:transparent;color:#ef4444;border-color:rgba(239,68,68,0.06)}
  .action.delete:hover{background:rgba(239,68,68,0.06)}

  .inline-form{display:inline;margin:0}

  /* Responsive: convert to stacked cards on small screens */
  @media (max-width:800px){
    .table-head{display:none}
    .row{grid-template-columns:1fr;padding:14px;gap:6px}
    
    /* PERUBAHAN 5: Menyesuaikan urutan untuk tampilan mobile */
    .col-no{order:1}
    .col-code{order:2}
    .col-name{order:3;font-size:1.05rem}
    .col-action{order:4;justify-content:flex-start}
    
    .cell[data-label]:before{
      content: attr(data-label);
      display:block;font-size:0.85rem;color:var(--muted);margin-bottom:6px;font-weight:600
    }
    .col-action{display:flex;gap:10px}
  }

  /* Focus states & accessibility */
  a:focus, button:focus{outline:3px solid rgba(217,83,79,0.14);outline-offset:3px}
</style>

<script>
  function confirmDelete(e, form){
    e.preventDefault();
    const name = form.closest('.row')?.querySelector('.col-name')?.textContent?.trim() || 'item ini';
    if(confirm('Hapus "' + name + '"? Tindakan ini tidak dapat dibatalkan.')){
      form.submit();
    }
    return false;
  }
</script>
