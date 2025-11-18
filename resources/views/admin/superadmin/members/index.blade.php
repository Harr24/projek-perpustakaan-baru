@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
{{-- 
==========================================================
Tampilan Halaman (View)
File: resources/views/admin/superadmin/members/index.blade.php
Tujuan: Halaman untuk mengelola anggota (siswa & guru)
==========================================================
--}}
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Kelola Anggota</h1>
            <p class="text-gray-500 mt-1">Lihat, edit, atau hapus data anggota siswa dan guru.</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            {{-- 🔥 TOMBOL HAPUS MASSAL (Hanya muncul jika ada siswa lulus) --}}
            @if(isset($graduatedCount) && $graduatedCount > 0)
            <button onclick="openBulkDeleteModal()" class="inline-flex items-center bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 font-bold py-2 px-4 rounded-lg transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Bersihkan Siswa Lulus ({{ $graduatedCount }})
            </button>
            @endif

            <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:-translate-y-px">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- Pesan Sukses & Error --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 border-l-4 border-green-500 rounded-r-lg shadow" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 border-l-4 border-red-500 rounded-r-lg shadow" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Konten Utama: Card Putih --}}
    <div class="bg-white shadow-md rounded-lg p-4 md:p-6">
        
        {{-- 🔥 AREA FILTER & PENCARIAN --}}
        <div class="mb-6 flex flex-col md:flex-row gap-4 justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-100">
            <form action="{{ route('admin.superadmin.members.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto items-center">
                
                {{-- Dropdown Filter Role --}}
                <div class="relative w-full md:w-auto">
                    <select name="filter_role" onchange="this.form.submit()" class="w-full appearance-none bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-red-500 cursor-pointer">
                        <option value="">Semua Anggota</option>
                        <option value="siswa_aktif" {{ request('filter_role') == 'siswa_aktif' ? 'selected' : '' }}>Siswa Aktif</option>
                        <option value="siswa_lulus" {{ request('filter_role') == 'siswa_lulus' ? 'selected' : '' }}>Siswa Lulus</option>
                        <option value="guru" {{ request('filter_role') == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="petugas" {{ request('filter_role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>

                {{-- Input Pencarian --}}
                <div class="flex w-full md:w-auto">
                    <input type="text" name="search" placeholder="Cari nama / email / NIS..." class="shadow-sm appearance-none border rounded-l-lg w-full md:w-64 py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500" value="{{ request('search') }}">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-r-lg transition-colors">Cari</button>
                </div>
                
                {{-- Tombol Reset (Muncul jika sedang filter/search) --}}
                @if(request('search') || request('filter_role'))
                    <a href="{{ route('admin.superadmin.members.index') }}" class="text-gray-500 hover:text-red-600 text-sm font-semibold underline decoration-dotted md:ml-2">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        {{-- Tabel Anggota --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">ID / NIS</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Kelas / Mapel</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($members as $member)
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 font-medium">{{ $member->name }}</td>
                        <td class="py-3 px-4">
                            @if ($member->role == 'siswa')
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $member->nis ?? '-' }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm">{{ $member->email }}</td>
                        
                        {{-- ========================================================== --}}
                        {{-- 🔥 LOGIKA KELAS/MAPEL YANG DIPERBAIKI (ANTI BUG N/A) 🔥 --}}
                        {{-- ========================================================== --}}
                        <td class="py-3 px-4 whitespace-nowrap">
                            @if ($member->role == 'siswa')
                                
                                {{-- 1. Cek Status Lulus --}}
                                @if ($member->class === 'Lulus')
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-600 text-white shadow-sm">
                                        LULUS
                                    </span>
                                
                                {{-- 2. Cek Data Lengkap (Kelas & Jurusan) --}}
                                @elseif (!empty($member->class) && !empty($member->major))
                                    {{ $member->class }} - {{ $member->major }}
                                
                                {{-- 3. Cek Data Parsial (Salah satu ada) --}}
                                @elseif (!empty($member->class) || !empty($member->major))
                                    {{ $member->class }} {{ $member->major }}
                                
                                {{-- 4. Fallback Data Lama --}}
                                @elseif (!empty($member->class_name)) 
                                    {{ $member->class_name }}
                                
                                {{-- 5. Data Kosong --}}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif

                            @elseif ($member->role == 'guru')
                                {{ $member->subject ?? '-' }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        {{-- ========================================================== --}}

                        {{-- Kolom Role --}}
                        <td class="py-3 px-4">
                            @php
                                $roleColors = [
                                    'siswa' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                    'guru' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                    'petugas' => 'bg-orange-100 text-orange-800 border border-orange-200',
                                    'superadmin' => 'bg-red-100 text-red-800 border border-red-200',
                                ];
                                $colorClass = $roleColors[$member->role] ?? 'bg-gray-100 text-gray-800 border border-gray-200';
                            @endphp
                            <span class="capitalize px-3 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                                {{ $member->role }}
                            </span>
                        </td>

                        <td class="py-3 px-4">
                            <span class="capitalize px-3 py-1 text-xs font-semibold rounded-full 
                                @if($member->account_status == 'active') bg-green-100 text-green-800 border border-green-200
                                @elseif($member->account_status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @else bg-red-100 text-red-800 border border-red-200 @endif">
                                {{ $member->account_status }}
                            </span>
                        </td>

                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- 🔥 TOMBOL DETAIL BARU 🔥 --}}
                                <a href="{{ route('admin.superadmin.members.show', $member->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-1.5 px-3 rounded-md transition-colors whitespace-nowrap shadow-sm">
                                    Detail
                                </a>

                                <a href="{{ route('admin.superadmin.members.edit', $member->id) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-bold py-1.5 px-3 rounded-md transition-colors whitespace-nowrap shadow-sm">Edit</a>
                                
                                <button type="button" 
                                    onclick="openDeleteModal('{{ route('admin.superadmin.members.destroy', $member->id) }}', '{{ addslashes($member->name) }}')"
                                    class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-1.5 px-3 rounded-md transition-colors whitespace-nowrap shadow-sm">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 px-4 text-center text-gray-500 bg-gray-50 rounded-b-lg">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="font-medium">Tidak ada data anggota yang ditemukan.</p>
                                <p class="text-sm mt-1">Coba sesuaikan filter pencarian Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginasi --}}
        <div class="mt-6">
            {{ $members->links() }}
        </div>
    </div>
</div>

{{-- 
==========================================================
MODAL KONFIRMASI HAPUS SATUAN
==========================================================
--}}
<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center backdrop-blur-sm" style="display: none; z-index: 9999;">
    <div class="bg-white p-6 rounded-lg shadow-2xl max-w-sm mx-auto transform transition-all">
        <div class="flex items-center mb-4">
            <div class="mr-3 flex-shrink-0 bg-red-100 rounded-full p-2">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Hapus Anggota?</h3>
        </div>
        <p class="text-gray-600 mb-6">
            Apakah Anda yakin ingin menghapus <strong id="memberName" class="text-gray-900"></strong>? Tindakan ini tidak dapat dibatalkan.
        </p>
        <form id="deleteForm" action="" method="POST" class="flex justify-end gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition-colors">Batal</button>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium shadow-md transition-colors">Ya, Hapus</button>
        </form>
    </div>
</div>

{{-- 
==========================================================
🔥 MODAL KONFIRMASI HAPUS MASSAL (SISWA LULUS) 🔥
==========================================================
--}}
<div id="bulkDeleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full flex items-center justify-center backdrop-blur-sm" style="display: none; z-index: 9999;">
    <div class="bg-white p-6 rounded-lg shadow-2xl max-w-md mx-auto border-t-4 border-red-600 transform transition-all">
        <div class="flex items-center mb-4 text-red-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-900">Peringatan Hapus Massal</h3>
        </div>
        
        <div class="bg-red-50 p-3 rounded-md border border-red-100 mb-4">
            <p class="text-red-800 font-medium">
                Anda akan menghapus <span class="text-xl font-bold">{{ $graduatedCount ?? 0 }}</span> akun siswa yang berstatus "Lulus".
            </p>
        </div>

        <p class="text-sm text-gray-500 mb-6 italic">
            Tindakan ini bersifat permanen dan tidak dapat dibatalkan. Pastikan semua data penting (seperti laporan peminjaman) sudah diamankan jika diperlukan.
        </p>
        
        <form action="{{ route('admin.superadmin.members.destroy.graduated') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeBulkDeleteModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Semua Siswa Lulus
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 
==========================================================
SCRIPT JAVASCRIPT UNTUK MODAL
==========================================================
--}}
<script>
    // --- Logika Modal Hapus Satuan ---
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const memberNameEl = document.getElementById('memberName');

    function openDeleteModal(actionUrl, memberName) {
        deleteForm.action = actionUrl;
        memberNameEl.textContent = memberName;
        deleteModal.style.display = 'flex';
    }

    function closeDeleteModal() {
        deleteModal.style.display = 'none';
    }

    // --- Logika Modal Hapus Massal ---
    const bulkDeleteModal = document.getElementById('bulkDeleteModal');

    function openBulkDeleteModal() {
        bulkDeleteModal.style.display = 'flex';
    }

    function closeBulkDeleteModal() {
        bulkDeleteModal.style.display = 'none';
    }

    // --- Tutup Modal saat klik di luar area ---
    window.onclick = function(event) {
        if (event.target == deleteModal) {
            closeDeleteModal();
        }
        if (event.target == bulkDeleteModal) {
            closeBulkDeleteModal();
        }
    }
</script>

@endsection