<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>Verifikasi Pendaftar</title>
 
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 
 <style>
  /* [ SELURUH KODE CSS KAMU YANG SUDAH ADA DI SINI, TIDAK ADA YANG DIUBAH ] */
  body {
   font-family: 'Segoe UI', Tahoma, sans-serif;
   background: #f9fafb;
   margin: 0;
   padding: 20px;
   color: #333;
  }
  .header-container {
   display: flex;
   justify-content: space-between;
   align-items: center;
   margin-bottom: 20px;
   flex-wrap: wrap;
   gap: 15px;
  }
  h1 {
   font-size: 1.8rem;
   margin: 0;
   color: #c62828;
  }
  .btn-back {
   padding: 8px 14px;
   border: 1px solid #ddd;
   border-radius: 6px;
   font-weight: 600;
   cursor: pointer;
   font-size: 0.9rem;
   text-decoration: none;
   background-color: #fff;
   color: #555;
   transition: background-color 0.2s, border-color 0.2s;
  }
  .btn-back:hover {
   background-color: #f3f4f6;
   border-color: #ccc;
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

 <div class="header-container">
  <h1>Daftar Siswa Menunggu Verifikasi</h1>
  <a href="{{ route('dashboard') }}" class="btn-back">Kembali ke Dashboard</a>
 </div>

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
    <th>NISN</th> 
    <th>Email</th>
    <th>Kelas</th>
    <th>Kartu Pelajar</th>
    <th>Aksi</th>
   </tr>
  </thead>
  <tbody>
   @forelse ($pendingUsers as $student)
    <tr>
     <td data-label="Nama">{{ $student->name }}</td>
     <td data-label="NISN">{{ $student->nis ?? 'N/A' }}</td>
     <td data-label="Email">{{ $student->email }}</td>
     <td data-label="Kelas">{{ $student->class_name ?? 'N/A' }}</td>
     <td data-label="Kartu Pelajar">
      @if($student->student_card_photo)
       <a href="{{ Storage::url($student->student_card_photo) }}" target="_blank">Lihat Foto</a>
      @else
       <span>Belum ada foto</span>
      @endif
     </td>
     <td data-label="Aksi">
      <div class="actions">

       <form action="{{ route('admin.petugas.verification.approve', $student) }}" method="POST" class="form-confirm-acc">
        @csrf
        <button type="submit" class="btn-approve">ACC</button>
       </form>
       
       {{-- Saya tambahkan konfirmasi untuk Tolak juga --}}
       <form action="{{ route('admin.petugas.verification.reject', $student) }}" method="POST" class="form-confirm-reject">
        @csrf
        <button type="submit" class="btn-reject">Tolak</button>
       </form>
      </div>
     </td>
    </tr>
   @empty
    <tr>
     <td colspan="6">Tidak ada pendaftar baru.</td>
    </tr>
   @endforelse
  </tbody>
 </table>

 
 <script>
    // Logika untuk tombol SETUJU (ACC)
    // 1. Ambil semua form dengan class 'form-confirm-acc'
    const accForms = document.querySelectorAll('.form-confirm-acc');
    
    // 2. Beri event listener ke setiap form
    accForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Hentikan form agar tidak langsung submit
            
            Swal.fire({
                title: 'Setujui Siswa Ini?',
                text: "Apakah Anda yakin data siswa ini sudah benar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2e7d32', // Warna dari .btn-approve
                cancelButtonColor: '#555',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Jika dikonfirmasi, lanjutkan submit form
                }
            });
        });
    });

    // Logika untuk tombol TOLAK
    const rejectForms = document.querySelectorAll('.form-confirm-reject');
    rejectForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Hentikan submit
            
            Swal.fire({
                title: 'Tolak Siswa Ini?',
                text: "Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c62828', // Warna dari .btn-reject
                cancelButtonColor: '#555',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Lanjutkan submit jika dikonfirmasi
                }
            });
        });
    });
 </script>

</body>
</html>