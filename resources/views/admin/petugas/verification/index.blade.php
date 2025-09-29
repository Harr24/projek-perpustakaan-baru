<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pendaftar</title>
    <style>
        table, th, td { border: 1px solid black; border-collapse: collapse; padding: 8px; }
        .btn-approve { color: green; }
        .btn-reject { color: red; }
    </style>
</head>
<body>
    <h1>Daftar Siswa Menunggu Verifikasi</h1>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
        <br>
    @endif

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Kartu Pelajar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pendingUsers as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a href="{{ Storage::url($user->student_card_photo) }}" target="_blank">Lihat Foto</a>
                    </td>
                    <td style="display: flex; gap: 10px;">
                        <form action="{{ route('admin.petugas.verification.approve', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-approve">ACC</button>
                        </form>
                        <form action="{{ route('admin.petugas.verification.reject', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-reject">Tolak</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Tidak ada pendaftar baru.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>