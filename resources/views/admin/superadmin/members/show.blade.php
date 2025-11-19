@extends('layouts.app')

@section('content')
{{-- 
==========================================================
Tampilan Halaman (View) - DETAIL ANGGOTA
File: resources/views/admin/superadmin/members/show.blade.php
==========================================================
--}}
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Anggota</h1>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap dan riwayat aktivitas.</p>
        </div>
        
        {{-- Tombol Kembali dengan Style Pasti --}}
        <a href="{{ route('admin.superadmin.members.index') }}" 
           class="inline-flex items-center px-4 py-2 rounded-lg font-medium shadow-sm text-sm hover:bg-gray-100 transition-colors bg-white border border-gray-200 text-gray-700"
           style="text-decoration: none; background-color: #fff; color: #374151; border: 1px solid #e5e7eb;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- === KOLOM KIRI: PROFIL (Lebih Padat) === --}}
        <div class="lg:col-span-1 flex flex-col gap-6">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Bagian Atas: Foto & Nama --}}
                <div class="p-6 flex flex-col items-center text-center border-b border-gray-100">
                     <div class="mb-4">
                        @if($member->profile_photo)
                            <img src="{{ asset('storage/' . $member->profile_photo) }}" alt="{{ $member->name }}" class="h-24 w-24 rounded-full object-cover border-4 border-blue-50 shadow-sm">
                        @else
                            <div class="h-24 w-24 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 text-3xl font-bold border-4 border-white shadow-sm">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>

                    <h2 class="text-lg font-bold text-gray-900">{{ $member->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $member->email }}</p>

                    <div class="flex gap-2 mt-3">
                         <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold 
                            @if($member->role == 'siswa') bg-blue-100 text-blue-700
                            @elseif($member->role == 'guru') bg-purple-100 text-purple-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($member->role) }}
                        </span>
                         <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold 
                            {{ $member->account_status == 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($member->account_status) }}
                        </span>
                    </div>
                </div>

                {{-- Bagian Bawah: Detail List --}}
                <div class="p-6 bg-gray-50">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Detail Informasi</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200 border-dashed">
                            <span class="text-sm text-gray-500">Telepon</span>
                            <span class="text-sm font-medium text-gray-800">{{ $member->phone_number ?? '-' }}</span>
                        </div>

                        @if($member->role == 'siswa')
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 border-dashed">
                                <span class="text-sm text-gray-500">NIS</span>
                                <span class="text-sm font-medium text-gray-800 font-mono">{{ $member->nis ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 border-dashed">
                                <span class="text-sm text-gray-500">Kelas</span>
                                <span class="text-sm font-medium text-gray-800">
                                    @if($member->class == 'Lulus') 
                                        <span class="text-xs bg-gray-600 text-white px-2 py-0.5 rounded">Alumni</span>
                                    @elseif(!empty($member->class) && !empty($member->major)) 
                                        {{ $member->class }} {{ $member->major }}
                                    @elseif(!empty($member->class_name))
                                        {{ $member->class_name }}
                                    @else 
                                        -
                                    @endif
                                </span>
                            </div>
                        @endif

                        @if($member->role == 'guru')
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 border-dashed">
                                <span class="text-sm text-gray-500">Mata Pelajaran</span>
                                <span class="text-sm font-medium text-gray-800">{{ $member->subject ?? '-' }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('admin.superadmin.members.edit', $member->id) }}" 
                           class="block w-full py-2 px-4 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 text-center transition-colors"
                           style="text-decoration: none;">
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- === KOLOM KANAN: STATISTIK & TABEL === --}}
        <div class="lg:col-span-2 flex flex-col gap-6">
            
            {{-- Statistik Dashboard --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Transaksi</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalLoans ?? 0 }}</h3>
                    </div>
                    <div class="h-10 w-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sedang Dipinjam</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $activeLoans ?? 0 }}</h3>
                    </div>
                    <div class="h-10 w-10 rounded-full flex items-center justify-center 
                        {{ ($activeLoans ?? 0) > 0 ? 'bg-yellow-50 text-yellow-600' : 'bg-green-50 text-green-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Tabel Riwayat --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex-1">
                <div class="px-6 py-4 border-b border-gray-100 bg-white flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-800">Riwayat Peminjaman</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 font-semibold">
                            <tr>
                                <th class="px-6 py-3">Buku</th>
                                <th class="px-6 py-3">Pinjam</th>
                                <th class="px-6 py-3">Kembali</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($borrowings as $borrowing)
                            <tr class="bg-white hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-8 flex-shrink-0 bg-gray-200 rounded overflow-hidden">
                                            @if(isset($borrowing->bookCopy->book->cover_image))
                                                <img src="{{ asset('storage/' . $borrowing->bookCopy->book->cover_image) }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center bg-gray-100 text-[10px] text-gray-400">NO</div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 line-clamp-1" title="{{ $borrowing->bookCopy->book->title ?? '-' }}">
                                                {{ $borrowing->bookCopy->book->title ?? 'Buku Terhapus' }}
                                            </div>
                                            <div class="text-xs text-gray-400 font-mono">
                                                {{ $borrowing->bookCopy->book_code ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($borrowing->borrowed_at)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($borrowing->returned_at)
                                        {{ \Carbon\Carbon::parse($borrowing->returned_at)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if(in_array($borrowing->status, ['pending', 'dipinjam']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Dipinjam
                                        </span>
                                    @elseif($borrowing->status == 'returned')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Kembali
                                        </span>
                                    @elseif($borrowing->status == 'rejected')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($borrowing->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                    Belum ada riwayat peminjaman.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection