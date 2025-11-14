@extends('layouts.admin')

@section('title', 'Tambah Jurusan Baru')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Judul dan Tombol Kembali -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Jurusan Baru</h1>
        <a href="{{ route('admin.superadmin.majors.index') }}" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <!-- Formulir -->
    <div class="bg-white shadow-xl rounded-lg p-6 md:p-8">
        <form action="{{ route('admin.superadmin.majors.store') }}" method="POST">
            @csrf

            <!-- Input Nama Jurusan -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Jurusan</label>
                <input type="text" name="name" id="name" 
                       class="shadow-sm appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 @error('name') border-red-500 @enderror" 
                       value="{{ old('name') }}" 
                       placeholder="Contoh: Rekayasa Perangkat Lunak 1"
                       required>
                
                <!-- Pesan Error Validasi -->
                @error('name')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Simpan -->
            <div class="flex justify-end mt-6">
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition duration-300 ease-in-out">
                    Simpan Jurusan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection