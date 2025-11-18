@extends('layouts.app')

@section('content')
{{-- 
==========================================================
Tampilan Halaman (View)
File: resources/views/admin/superadmin/members/edit.blade.php
Tujuan: Halaman untuk mengedit anggota (siswa & guru)
==========================================================
--}}
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Anggota</h1>
            <p class="text-gray-500 mt-1">Perbarui data untuk: <span class="font-semibold">{{ $member->name }}</span></p>
        </div>
        <a href="{{ route('admin.superadmin.members.index') }}" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:-translate-y-px">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Kembali ke Kelola Anggota
        </a>
    </div>

    {{-- Tampilkan error validasi umum --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 border-l-4 border-red-500 rounded-r-lg shadow" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Edit --}}
    <div class="bg-white shadow-md rounded-lg p-4 md:p-6 max-w-3xl mx-auto">
        <form action="{{ route('admin.superadmin.members.update', $member->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- ======================== --}}
            {{-- --- DATA WAJIB UMUM --- --}}
            {{-- ======================== --}}
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Umum</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama --}}
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama: <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $member->name) }}" class="shadow appearance-none border @error('name') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email: <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $member->email) }}" class="shadow appearance-none border @error('email') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Nomor Telepon (Opsional) --}}
                <div class="mb-4">
                    <label for="phone_number" class="block text-gray-700 text-sm font-bold mb-2">Nomor Telepon:</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $member->phone_number) }}" class="shadow appearance-none border @error('phone_number') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700" placeholder="Contoh: 0812...">
                    @error('phone_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Role --}}
                <div class="mb-4">
                    <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role: <span class="text-red-500">*</span></label>
                    <select id="role" name="role" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                        <option value="siswa" @selected(old('role', $member->role) == 'siswa')>Siswa</option>
                        <option value="guru" @selected(old('role', $member->role) == 'guru')>Guru</option>
                    </select>
                </div>
            </div>

            {{-- ========================== --}}
            {{-- --- DETAIL BERDASAR ROLE --- --}}
            {{-- ========================== --}}
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 my-4">Informasi Detail</h3>

            {{-- Kolom detail untuk SISWA --}}
            <div id="student-fields" style="{{ old('role', $member->role) == 'siswa' ? '' : 'display:none;' }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- NIS (Wajib untuk Siswa) --}}
                    <div class="mb-4">
                        <label for="nis" class="block text-gray-700 text-sm font-bold mb-2">NIS: <span class="text-red-500">*</span></label>
                        <input type="text" id="nis" name="nis" value="{{ old('nis', $member->nis) }}" class="shadow appearance-none border @error('nis') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700">
                        @error('nis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- Kelas (Wajib untuk Siswa) --}}
                    <div class="mb-4">
                        <label for="class" class="block text-gray-700 text-sm font-bold mb-2">Kelas: <span class="text-red-500">*</span></label>
                        <select id="class" name="class" class="shadow border @error('class') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700">
                            <option value="">Pilih Tingkat Kelas</option>
                            <option value="X" @selected(old('class', $member->class) == 'X')>X</option>
                            <option value="XI" @selected(old('class', $member->class) == 'XI')>XI</option>
                            <option value="XII" @selected(old('class', $member->class) == 'XII')>XII</option>
                            {{-- Kita tidak mengizinkan set 'Lulus' secara manual, itu otomatis --}}
                            @if($member->class == 'Lulus')
                            <option value="Lulus" @selected(true) disabled>Lulus</option>
                            @endif
                        </select>
                        @error('class') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- Jurusan (Wajib untuk Siswa) --}}
                    <div class="mb-4">
                        <label for="major" class="block text-gray-700 text-sm font-bold mb-2">Jurusan: <span class="text-red-500">*</span></label>
                        <select id="major" name="major" class="shadow border @error('major') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700">
                            <option value="">Pilih Jurusan</option>
                            {{-- Variabel $majors ini WAJIB dikirim dari MemberController --}}
                            @if(isset($majors))
                                @foreach($majors as $major)
                                    <option value="{{ $major->name }}" @selected(old('major', $member->major) == $major->name)>
                                        {{ $major->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('major') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Kolom detail untuk GURU --}}
            <div id="teacher-fields" style="{{ old('role', $member->role) == 'guru' ? '' : 'display:none;' }}">
                <div class="mb-4">
                    <label for="subject" class="block text-gray-700 text-sm font-bold mb-2">Mata Pelajaran: <span class="text-red-500">*</span></label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject', $member->subject) }}" class="shadow appearance-none border @error('subject') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700">
                    @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            {{-- ================== --}}
            {{-- --- PENGATURAN --- --}}
            {{-- ================== --}}
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 my-4">Pengaturan Akun</h3>
            
            {{-- Status Akun --}}
            <div class="mb-4 max-w-xs">
                <label for="account_status" class="block text-gray-700 text-sm font-bold mb-2">Status Akun: <span class="text-red-500">*</span></label>
                <select id="account_status" name="account_status" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                    <option value="pending" @selected(old('account_status', $member->account_status) == 'pending')>Pending</option>
                    <option value="active" @selected(old('account_status', $member->account_status) == 'active')>Active</option>
                    <option value="rejected" @selected(old('account_status', $member->account_status) == 'rejected')>Rejected</option>
                    <option value="suspended" @selected(old('account_status', $member->account_status) == 'suspended')>Suspended</option>
                </select>
            </div>
            
            {{-- Update Password (Opsional) --}}
            <hr class="my-6">
            <p class="text-gray-600 text-sm mb-4">Isi bagian di bawah ini hanya jika Anda ingin mengubah password anggota.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password Baru:</label>
                    <input type="password" id="password" name="password" class="shadow appearance-none border @error('password') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password Baru:</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-start gap-4 mt-6">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-transform transform hover:-translate-y-px">
                    Perbarui Anggota
                </button>
                <a href="{{ route('admin.superadmin.members.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">Batal</a>
            </div>
        </form>
    </div>
</div>

{{-- 
==========================================================
SCRIPT BARU
Logika untuk menambah/menghapus 'required' secara dinamis
==========================================================
--}}
<script>
    // Ambil semua elemen yang kita butuhkan
    const roleSelect = document.getElementById('role');
    
    const studentFields = document.getElementById('student-fields');
    const nisInput = document.getElementById('nis');
    const classInput = document.getElementById('class');
    const majorInput = document.getElementById('major');
    
    const teacherFields = document.getElementById('teacher-fields');
    const subjectInput = document.getElementById('subject');

    // Fungsi untuk mengatur tampilan dan atribut 'required'
    function toggleRoleFields() {
        const selectedRole = roleSelect.value;

        if (selectedRole === 'siswa') {
            // Tampilkan field siswa, sembunyikan field guru
            studentFields.style.display = 'block';
            teacherFields.style.display = 'none';

            // WAJIBKAN field siswa
            nisInput.required = true;
            classInput.required = true;
            majorInput.required = true;

            // HAPUS kewajiban field guru
            subjectInput.required = false;

        } else if (selectedRole === 'guru') {
            // Sembunyikan field siswa, tampilkan field guru
            studentFields.style.display = 'none';
            teacherFields.style.display = 'block';

            // HAPUS kewajiban field siswa
            nisInput.required = false;
            classInput.required = false;
            majorInput.required = false;

            // WAJIBKAN field guru
            subjectInput.required = true;
            
        } else {
            // Sembunyikan semua jika tidak terduga
            studentFields.style.display = 'none';
            teacherFields.style.display = 'none';
            
            // HAPUS semua kewajiban
            nisInput.required = false;
            classInput.required = false;
            majorInput.required = false;
            subjectInput.required = false;
        }
    }

    // Jalankan fungsi saat dropdown 'role' berubah
    roleSelect.addEventListener('change', toggleRoleFields);

    // Jalankan fungsi saat halaman pertama kali dimuat
    // untuk mengatur state awal sesuai data dari database
    document.addEventListener('DOMContentLoaded', toggleRoleFields);
</script>
@endsection