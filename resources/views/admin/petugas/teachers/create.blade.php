@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Buat Akun Guru Baru</h1>
        <p class="text-gray-500 mt-1">Isi form di bawah untuk mendaftarkan akun guru.</p>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 border-l-4 border-red-500 rounded-r-lg shadow">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl">
        <form action="{{ route('admin.petugas.teachers.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap:</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            <div class="mb-4">
                <label for="subject" class="block text-gray-700 text-sm font-bold mb-2">Mata Pelajaran:</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" style="background-color: #dc2626 !important; color: white !important;">
                    Buat Akun
                </button>
                <a href="{{ route('admin.petugas.teachers.index') }}" class="text-gray-600 hover:text-gray-800">Kembali</a>
            </div>
        </form>
    </div>
</div>

{{-- Inline CSS untuk memastikan tombol terlihat --}}
<style>
    /* Memastikan button terlihat dengan inline style */
    button[type="submit"] {
        background-color: #dc2626 !important;
        color: white !important;
        font-weight: bold;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    button[type="submit"]:hover {
        background-color: #b91c1c !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
    }
    
    /* Memastikan input terlihat */
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        border: 1px solid #d1d5db;
        background-color: white;
        color: #374151;
    }
    
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
</style>
@endsection