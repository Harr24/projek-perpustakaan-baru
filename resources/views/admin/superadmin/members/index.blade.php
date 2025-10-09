@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">
    {{-- Header dengan tombol Kembali --}}
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Kelola Anggota</h1>
            {{-- Teks deskripsi diperbarui sesuai informasi Anda --}}
            <p class="text-gray-500 mt-1">Lihat, edit, atau hapus data anggota siswa dan guru.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:-translate-y-px">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Kembali ke Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 border-l-4 border-green-500 rounded-r-lg shadow">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-4 md:p-6">
        <div class="mb-4">
            <form action="{{ route('admin.superadmin.members.index') }}" method="GET">
                <div class="flex">
                    <input type="text" name="search" placeholder="Cari nama atau email..." class="shadow-sm appearance-none border rounded-l-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500" value="{{ request('search') }}">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-r-lg">Cari</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($members as $member)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">{{ $member->name }}</td>
                        <td class="py-3 px-4">{{ $member->email }}</td>
                        <td class="py-3 px-4">
                            {{-- ========================================================== --}}
                            {{-- PERBAIKAN: Logika disederhanakan hanya untuk guru dan siswa --}}
                            {{-- ========================================================== --}}
                            <span class="capitalize px-3 py-1 text-xs font-semibold rounded-full 
                                @if($member->role == 'guru') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $member->role }}
                            </span>
                        </td>
                        <td class="py-3 px-4"><span class="capitalize px-3 py-1 text-xs font-semibold rounded-full {{ $member->account_status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $member->account_status }}</span></td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('admin.superadmin.members.edit', $member->id) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-bold py-1 px-3 rounded-md transition-colors">Edit</a>
                                <form action="{{ route('admin.superadmin.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white text-xs font-bold py-1 px-3 rounded-md transition-colors">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">Tidak ada anggota yang ditemukan.</td>
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
@endsection

