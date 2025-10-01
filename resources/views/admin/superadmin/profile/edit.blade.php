@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">

    {{-- Judul Halaman --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Edit Profil Super Admin</h1>
        <p class="text-gray-500 mt-1">Perbarui informasi profil dan password Anda.</p>
    </div>

    {{-- Tombol Kembali --}}
    <div class="mb-6">
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

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 border-l-4 border-red-500 rounded-r-lg shadow">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Edit Profil --}}
    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl">
        <form action="{{ route('admin.superadmin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama:</label>
                <input type="text" id="name" name="name" value="{{ old('name', $superadmin->name) }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $superadmin->email) }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password Baru (kosongkan jika tidak diubah):</label>
                <input type="password" id="password" name="password" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password Baru:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6">
                <label for="profile_photo" class="block text-gray-700 text-sm font-bold mb-2">Foto Profil (Max 2MB):</label>
                @if($superadmin->profile_photo)
                    <div class="mb-2">
                        <img src="{{ Storage::url($superadmin->profile_photo) }}" alt="Foto Profil" class="w-24 h-24 object-cover rounded-full border border-gray-300">
                        <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                    </div>
                @endif
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                    Perbarui Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection