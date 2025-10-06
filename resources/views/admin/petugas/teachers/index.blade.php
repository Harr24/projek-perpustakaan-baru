@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Daftar Akun Guru</h1>
            <p class="text-gray-500 mt-1">Berikut adalah daftar semua akun guru yang terdaftar.</p>
        </div>
        <a href="{{ route('admin.petugas.teachers.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            + Tambah Akun Guru
        </a>
    </div>

    {{-- ========================================================== --}}
    {{-- TAMBAHAN: Tombol Kembali ke Dashboard --}}
    {{-- ========================================================== --}}
    <div class="mb-8">
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center bg-white hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-lg shadow-sm border border-gray-200 transition-colors text-sm font-medium">
            &laquo; Kembali ke Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 border-l-4 border-green-500 rounded-r-lg shadow">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Mata Pelajaran</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($teachers as $teacher)
                    <tr class="border-b">
                        <td class="py-3 px-4">{{ $teacher->name }}</td>
                        <td class="py-3 px-4">{{ $teacher->email }}</td>
                        <td class="py-3 px-4">{{ $teacher->subject }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-500">Belum ada akun guru yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection