@extends('layouts.app') {{-- Sesuaikan dengan layout utama Anda --}}

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Anggota</h1>
        <p class="text-gray-500 mt-1">Lihat, edit, atau hapus data anggota siswa dan guru.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 border-l-4 border-green-500 rounded-r-lg shadow">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-4">
            <form action="{{ route('admin.superadmin.members.index') }}" method="GET">
                <div class="flex">
                    <input type="text" name="search" placeholder="Cari nama atau email..." class="shadow appearance-none border rounded-l w-full py-2 px-3 text-gray-700" value="{{ request('search') }}">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-r">Cari</button>
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
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($members as $member)
                    <tr class="border-b">
                        <td class="py-3 px-4">{{ $member->name }}</td>
                        <td class="py-3 px-4">{{ $member->email }}</td>
                        <td class="py-3 px-4"><span class="capitalize">{{ $member->role }}</span></td>
                        <td class="py-3 px-4"><span class="capitalize px-2 py-1 text-xs rounded-full {{ $member->account_status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $member->account_status }}</span></td>
                        <td class="py-3 px-4 flex gap-2">
                            <a href="{{ route('admin.superadmin.members.edit', $member->id) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                            <form action="{{ route('admin.superadmin.members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                            </form>
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