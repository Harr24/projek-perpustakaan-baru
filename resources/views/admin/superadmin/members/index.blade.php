@extends('layouts.app')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Kelola Anggota</h1>
            <p class="text-gray-500 mt-1">Lihat, edit, atau hapus data anggota siswa dan guru.</p>
        </div>
        
        <div class="flex flex-wrap gap-3">
            {{-- Tombol Hapus Massal --}}
            @if(isset($graduatedCount) && $graduatedCount > 0)
            <button onclick="openBulkDeleteModal()" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 border border-red-200 rounded-lg font-bold shadow-sm hover:bg-red-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Bersihkan Siswa Lulus ({{ $graduatedCount }})
            </button>
            @endif

            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg font-bold shadow-md hover:bg-gray-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- Pesan Sukses & Error --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 border-l-4 border-green-500 rounded-r-lg shadow">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 border-l-4 border-red-500 rounded-r-lg shadow">
            {{ session('error') }}
        </div>
    @endif

    {{-- Konten Utama --}}
    <div class="bg-white shadow-md rounded-lg p-4 md:p-6">
        
        {{-- Filter & Search --}}
        <div class="mb-6 flex flex-col md:flex-row gap-4 justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-100">
            <form action="{{ route('admin.superadmin.members.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full md:w-auto items-center">
                <div class="relative w-full md:w-auto">
                    <select name="filter_role" onchange="this.form.submit()" class="w-full border-gray-300 rounded-md text-gray-700 py-2 px-3 focus:ring-red-500 focus:border-red-500 cursor-pointer shadow-sm">
                        <option value="">Semua Anggota</option>
                        <option value="siswa_aktif" {{ request('filter_role') == 'siswa_aktif' ? 'selected' : '' }}>Siswa Aktif</option>
                        <option value="siswa_lulus" {{ request('filter_role') == 'siswa_lulus' ? 'selected' : '' }}>Siswa Lulus</option>
                        <option value="guru" {{ request('filter_role') == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="petugas" {{ request('filter_role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                    </select>
                </div>
                <div class="flex w-full md:w-auto">
                    <input type="text" name="search" placeholder="Cari nama / email / NIS..." class="border-gray-300 rounded-l-md w-full md:w-64 py-2 px-3 focus:ring-red-500 focus:border-red-500 shadow-sm" value="{{ request('search') }}">
                    <button type="submit" class="bg-red-600 text-white font-bold py-2 px-4 rounded-r-md hover:bg-red-700 transition">Cari</button>
                </div>
                @if(request('search') || request('filter_role'))
                    <a href="{{ route('admin.superadmin.members.index') }}" class="text-gray-500 hover:text-red-600 text-sm font-semibold underline ml-2">Reset</a>
                @endif
            </form>
        </div>

        {{-- Tabel Anggota --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border-collapse">
                <thead class="bg-gray-100 border-b">
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
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse ($members as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">{{ $member->name }}</td>
                        <td class="py-3 px-4 text-sm">
                            @if ($member->role == 'siswa')
                                <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 font-mono">{{ $member->nis ?? '-' }}</span>
                            @else - @endif
                        </td>
                        <td class="py-3 px-4 text-sm">{{ $member->email }}</td>
                        
                        {{-- KOLOM KELAS DENGAN STYLE INLINE --}}
                        <td class="py-3 px-4 whitespace-nowrap text-sm">
                            @if ($member->role == 'siswa')
                                @if ($member->class == 'Lulus')
                                    <span style="background-color: #4b5563; color: white; padding: 4px 10px; border-radius: 9999px; font-weight: bold; font-size: 0.75rem;">
                                        LULUS
                                    </span>
                                @elseif (!empty($member->class) && !empty($member->major))
                                    {{ $member->class }} - {{ $member->major }}
                                @elseif (!empty($member->class_name)) 
                                    {{ $member->class_name }}
                                @else
                                    <span class="text-gray-400 italic">Siswa Aktif</span>
                                @endif
                            @elseif ($member->role == 'guru')
                                {{ $member->subject ?? 'Guru Mapel' }}
                            @else
                                -
                            @endif
                        </td>

                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full uppercase
                                @if($member->role == 'siswa') bg-blue-100 text-blue-800
                                @elseif($member->role == 'guru') bg-purple-100 text-purple-800
                                @elseif($member->role == 'petugas') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $member->role }}
                            </span>
                        </td>

                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($member->account_status == 'active') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $member->account_status }}
                            </span>
                        </td>

                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- TOMBOL DETAIL --}}
                                <a href="{{ route('admin.superadmin.members.show', $member->id) }}" 
                                   style="background-color: #3b82f6; color: white; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; text-decoration: none;">
                                   Detail
                                </a>

                                {{-- TOMBOL EDIT --}}
                                <a href="{{ route('admin.superadmin.members.edit', $member->id) }}" 
                                   style="background-color: #6366f1; color: white; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; text-decoration: none;">
                                   Edit
                                </a>
                                
                                {{-- TOMBOL HAPUS --}}
                                <button type="button" 
                                    onclick="openDeleteModal('{{ route('admin.superadmin.members.destroy', $member->id) }}', '{{ addslashes($member->name) }}')"
                                    style="background-color: #ef4444; color: white; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; border: none; cursor: pointer;">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500">
                            Tidak ada data anggota yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $members->links() }}
        </div>
    </div>
</div>

{{-- Modal Hapus Satuan --}}
<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center backdrop-blur-sm" style="display: none; z-index: 9999;">
    <div class="bg-white p-6 rounded-lg shadow-2xl max-w-sm mx-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Hapus Anggota?</h3>
        <p class="text-gray-600 mb-6">Yakin hapus <strong id="memberName"></strong>?</p>
        <form id="deleteForm" action="" method="POST" class="flex justify-end gap-3">
            @csrf @method('DELETE')
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 rounded text-gray-800">Batal</button>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Ya, Hapus</button>
        </form>
    </div>
</div>

{{-- Modal Bulk Delete --}}
<div id="bulkDeleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full flex items-center justify-center backdrop-blur-sm" style="display: none; z-index: 9999;">
    <div class="bg-white p-6 rounded-lg shadow-2xl max-w-md mx-auto border-t-4 border-red-600">
        <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Siswa Lulus</h3>
        <p class="text-gray-600 mb-6">Anda akan menghapus <strong>{{ $graduatedCount ?? 0 }}</strong> siswa lulus. (Siswa yang meminjam buku akan dilewati).</p>
        <form action="{{ route('admin.superadmin.members.destroy.graduated') }}" method="POST">
            @csrf @method('DELETE')
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeBulkDeleteModal()" class="px-4 py-2 bg-gray-200 rounded text-gray-800">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Hapus Semua</button>
            </div>
        </form>
    </div>
</div>

<script>
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const memberNameEl = document.getElementById('memberName');
    const bulkDeleteModal = document.getElementById('bulkDeleteModal');

    function openDeleteModal(url, name) {
        deleteForm.action = url;
        memberNameEl.textContent = name;
        deleteModal.style.display = 'flex';
    }
    function closeDeleteModal() { deleteModal.style.display = 'none'; }
    function openBulkDeleteModal() { bulkDeleteModal.style.display = 'flex'; }
    function closeBulkDeleteModal() { bulkDeleteModal.style.display = 'none'; }
    window.onclick = function(event) {
        if(event.target == deleteModal) closeDeleteModal();
        if(event.target == bulkDeleteModal) closeBulkDeleteModal();
    }
</script>
@endsection