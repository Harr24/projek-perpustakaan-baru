{{-- Menggunakan layout admin --}}
@extends('layouts.admin')

{{-- Judul Halaman --}}
@section('title', 'Edit Rak')

@section('content')

<div class="container mx-auto px-4 py-8">

<!-- Judul Halaman dan Tombol Kembali -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Edit Rak: <span class="text-red-600">{{ $shelf->name }}</span></h1>
    <a href="{{ route('admin.superadmin.shelves.index') }}" 
       class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
        &larr; Kembali ke Daftar Rak
    </a>
</div>

<!-- Form Edit Rak (Versi Sederhana) -->
<div class="bg-white shadow-xl rounded-lg overflow-hidden">
    <div class="p-6">
        
        <!-- Menampilkan Error Validasi -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops! Ada yang salah:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.superadmin.shelves.update', $shelf) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Input Nama Rak -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Rak <span class="text-red-600">*</span></label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-600 @error('name') border-red-500 @enderror" 
                       value="{{ old('name', $shelf->name) }}"  {{-- <-- INI PENTING UNTUK EDIT --}}
                       placeholder="Contoh: Rak Fiksi Ilmiah" 
                       required>
                
                @error('name')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Simpan dan Batal -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.superadmin.shelves.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                    Batal
                </a>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                    Update Data Rak
                </button>
            </div>
        </form>

    </div>
</div>


</div>
@endsection