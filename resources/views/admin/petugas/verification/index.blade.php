<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>Verifikasi Pendaftar</title>
 <style>
  /* [ SELURUH KODE CSS KAMU YANG SUDAH ADA DI SINI, TIDAK ADA YANG DIUBAH ] */
  body {
   font-family: 'Segoe UI', Tahoma, sans-serif;
   background: #f9fafb;
   margin: 0;
   padding: 20px;
   color: #333;
  }
  h1 {
   font-size: 1.8rem;
   margin-bottom: 20px;
   color: #c62828;
  }
  .alert-success, .alert-error {
   padding: 12px 16px;
   border-radius: 6px;
   margin-bottom: 16px;
   font-weight: 500;
  }
  .alert-success {
   background-color: #e6f4ea;
   color: #2e7d32;
   border-left: 4px solid #2e7d32;
  }
  .alert-error {
   background-color: #fdecea;
   color: #c62828;
   border-left: 4px solid #c62828;
  }
  table {
   width: 100%;
   border-collapse: collapse;
   background: #fff;
   box-shadow: 0 2px 6px rgba(0,0,0,0.05);
   border-radius: 6px;
   overflow: hidden;
  }
  th, td {
   padding: 12px 16px;
   border-bottom: 1px solid #eee;
   text-align: left;
  }
  td a {
   color: #c62828;
   text-decoration: none;
   font-weight: 600;
  }
  td a:hover {
   text-decoration: underline;
  }
  th {
   background-color: #f3f4f6;
   font-size: 0.9rem;
   text-transform: uppercase;
   color: #555;
  }
  td {
   font-size: 0.95rem;
  }
  td span {
   font-style: italic;
   color: #888;
  }
  .actions {
   display: flex;
   gap: 8px;
   flex-wrap: wrap;
  }
  .btn-approve, .btn-reject {
   padding: 6px 12px;
   border: none;
   border-radius: 4px;
   font-weight: 600;
   cursor: pointer;
   font-size: 0.85rem;
  }
  .btn-approve {
   background-color: #2e7d32;
   color: #fff;
  }
  .btn-reject {
   background-color: #c62828;
   color: #fff;
  }
  @media (max-width: 600px) {
   table, thead, tbody, th, td, tr {
    display: block;
   }
   thead {
    display: none;
   }
   tr {
    margin-bottom: 16px;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    padding: 12px;
   }
   td {
    border: none;
    padding: 8px 0;
   }
   td::before {
    content: attr(data-label);
    font-weight: 600;
    display: block;
    margin-bottom: 4px;
    color: #555;
   }
   .actions {
    flex-direction: column;
   }
  }
 </style>
</head>
<body>
 <h1>Daftar Siswa Menunggu Verifikasi</h1>

 @if(session('success'))
  <div class="alert-success">{{ session('success') }}</div>
 @endif

 @if(session('error'))
  <div class="alert-error">{{ session('error') }}</div>
 @endif

 <table>
  <thead>
   <tr>
    <th>Nama</th>
    <th>Email</th>
    <th>Kelas</th> {{-- <-- PERUBAHAN 1: Menambahkan judul kolom --}}
    <th>Kartu Pelajar</th>
    <th>Aksi</th>
   </tr>
  </thead>
  <tbody>
   @forelse ($pendingUsers as $student)
    <tr>
     <td data-label="Nama">{{ $student->name }}</td>
     <td data-label="Email">{{ $student->email }}</td>
     <td data-label="Kelas">{{ $student->class_name ?? 'N/A' }}</td> {{-- <-- PERUBAHAN 2: Menampilkan data kelas --}}
     <td data-label="Kartu Pelajar">
      @if($student->student_card_photo)
       <a href="{{ Storage::url($student->student_card_photo) }}" target="_blank">Lihat Foto</a>
      @else
       <span>Belum ada foto</span>
      @endif
     </td>
     <td data-label="Aksi">
      <div class="actions">
       <form action="{{ route('admin.petugas.verification.approve', $student) }}" method="POST">
        @csrf
        <button type="submit" class="btn-approve">ACC</button>
       </form>
       <form action="{{ route('admin.petugas.verification.reject', $student) }}" method="POST">
        @csrf
        <button type="submit" class="btn-reject">Tolak</button>
       </form>
      </div>
     </td>
    </tr>
   @empty
    <tr>
     <td colspan="5">Tidak ada pendaftar baru.</td> {{-- <-- PERUBAHAN 3: Mengubah colspan menjadi 5 --}}
    </tr>
   @endforelse
  </tbody>
 </table>
</body>
</html>