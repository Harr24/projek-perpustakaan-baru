@extends('layouts.app')

@section('content')
<div class="p-6">

    <h1 class="text-2xl font-bold text-gray-800 mb-4">
        Edit Akun Petugas: {{ $petugas->name }}
    </h1>

    {{-- Error --}}
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg shadow">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Edit --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <form action="{{ route('admin.superadmin.petugas.update', $petugas->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-semibold">Nama</label>
                <input type="text" name="name" value="{{ old('name', $petugas->name) }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email', $petugas->email) }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">Role</label>
                <select name="role" class="w-full border rounded px-3 py-2" required>
                    <option value="petugas" {{ $petugas->role == 'petugas' ? 'selected' : '' }}>Petugas</option>
                    <option value="superadmin" {{ $petugas->role == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-semibold">Password Baru (opsional)</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block font-semibold">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow">
                    Update
                </button>
                <a href="{{ route('admin.superadmin.petugas.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
