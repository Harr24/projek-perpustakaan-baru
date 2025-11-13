{{-- Menggunakan layout admin --}}
@extends('layouts.admin')

{{-- Judul Halaman --}}
@section('title', 'Manajemen Jurusan')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Judul Halaman dan Tombol Tambah -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Jurusan</h1>
        <a href="{{ route('admin.superadmin.majors.create') }}" 
           class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
            + Tambah Jurusan Baru
        </a>
    </div>

    <!-- Notifikasi Sukses atau Error -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tabel Data Jurusan -->
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Jurusan
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal Dibuat
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($majors as $major)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $major->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">{{ $major->created_at->format('d F Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <!-- Tombol Edit -->
                            <a href="{{ route('admin.superadmin.majors.edit', $major) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            
                            <!-- Tombol Hapus (Form) -->
                            <form action="{{ route('admin.superadmin.majors.destroy', $major) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Anda yakin ingin menghapus jurusan ini? Ini tidak bisa dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type
="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                            {{-- 
                                CATATAN: Peringatan 'confirm' di atas adalah cara sederhana. 
                                Untuk UI yang lebih baik, gunakan modal kustom.
                                Kontroler Anda sudah AMAN dan akan mencegah penghapusan jika jurusan masih dipakai.
                            --}}
                        </td>
                    </tr>
                @empty
                    <!-- Jika tidak ada data -->
                    <tr>
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            Belum ada data jurusan. Silakan tambahkan jurusan baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Link Paginasi -->
    <div class="mt-6">
        {{ $majors->links() }}
    </div>

</div>
@endsection