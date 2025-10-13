@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Anggota: {{ $member->name }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl">
        <form action="{{ route('admin.superadmin.members.update', $member->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Data Umum --}}
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama:</label>
                <input type="text" id="name" name="name" value="{{ old('name', $member->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $member->email) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
            </div>
            <div class="mb-4">
                <label for="phone_number" class="block text-gray-700 text-sm font-bold mb-2">Nomor Telepon:</label>
                <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $member->phone_number) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <div class="mb-4">
                <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
                <select id="role" name="role" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    <option value="siswa" @selected(old('role', $member->role) == 'siswa')>Siswa</option>
                    <option value="guru" @selected(old('role', $member->role) == 'guru')>Guru</option>
                </select>
            </div>

            {{-- Kolom detail berdasarkan role --}}
            <div id="student-fields" style="{{ old('role', $member->role) == 'siswa' ? '' : 'display:none;' }}">
                <div class="mb-4">
                    <label for="nis" class="block text-gray-700 text-sm font-bold mb-2">NIS:</label>
                    <input type="text" id="nis" name="nis" value="{{ old('nis', $member->nis) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                
                {{-- ====================================================== --}}
                {{-- PERUBAHAN DI SINI: Sesuaikan input 'class' menjadi 'class_name' --}}
                {{-- ====================================================== --}}
                <div class="mb-4">
                    <label for="class_name" class="block text-gray-700 text-sm font-bold mb-2">Kelas:</label>
                    <input type="text" id="class_name" name="class_name" value="{{ old('class_name', $member->class_name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>

            <div id="teacher-fields" style="{{ old('role', $member->role) == 'guru' ? '' : 'display:none;' }}">
                <div class="mb-4">
                    <label for="subject" class="block text-gray-700 text-sm font-bold mb-2">Mata Pelajaran:</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject', $member->subject) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>
            
            {{-- Status Akun --}}
            <div class="mb-4">
                <label for="account_status" class="block text-gray-700 text-sm font-bold mb-2">Status Akun:</label>
                <select id="account_status" name="account_status" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    <option value="pending" @selected(old('account_status', $member->account_status) == 'pending')>Pending</option>
                    <option value="active" @selected(old('account_status', $member->account_status) == 'active')>Active</option>
                    <option value="rejected" @selected(old('account_status', $member->account_status) == 'rejected')>Rejected</option>
                    <option value="suspended" @selected(old('account_status', $member->account_status) == 'suspended')>Suspended</option>
                </select>
            </div>
            
            {{-- Update Password --}}
            <hr class="my-6">
            <p class="text-gray-600 text-sm mb-4">Isi bagian di bawah ini hanya jika Anda ingin mengubah password anggota.</p>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password Baru:</label>
                <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password Baru:</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Perbarui Anggota</button>
                <a href="{{ route('admin.superadmin.members.index') }}" class="text-gray-600 hover:text-gray-800">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Script ini sudah benar, tidak perlu diubah
    const roleSelect = document.getElementById('role');
    const studentFields = document.getElementById('student-fields');
    const teacherFields = document.getElementById('teacher-fields');

    roleSelect.addEventListener('change', function() {
        if (this.value === 'siswa') {
            studentFields.style.display = 'block';
            teacherFields.style.display = 'none';
        } else if (this.value === 'guru') {
            studentFields.style.display = 'none';
            teacherFields.style.display = 'block';
        } else {
            studentFields.style.display = 'none';
            teacherFields.style.display = 'none';
        }
    });
</script>
@endsection