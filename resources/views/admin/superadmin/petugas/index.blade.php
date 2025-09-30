@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

    {{-- Judul Halaman --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Akun Petugas</h1>
        <p class="text-gray-500 mt-1">Atur akun petugas dan superadmin yang memiliki akses ke sistem.</p>
    </div>

    {{-- Tombol Aksi --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.superadmin.petugas.create') }}" 
           class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Akun
        </a>
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center bg-white hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-lg shadow border border-gray-200 transition-colors">
            &laquo; Kembali ke Dashboard
        </a>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 border-l-4 border-green-500 rounded-r-lg shadow">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel Akun --}}
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-red-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($petugas as $akun)
                    <tr class="hover:bg-red-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $akun->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $akun->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $akun->role === 'superadmin' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($akun->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap flex items-center gap-3">
                            <a href="{{ route('admin.superadmin.petugas.edit', $akun->id) }}" 
                               class="bg-white hover:bg-gray-100 text-gray-700 px-3 py-1 rounded-md shadow-sm border border-gray-300 text-sm font-medium">
                                Edit
                            </a>
                            <form action="{{ route('admin.superadmin.petugas.destroy', $akun->id) }}" 
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md shadow-sm border border-transparent text-sm font-medium">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            Belum ada akun petugas atau superadmin yang dikelola.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection