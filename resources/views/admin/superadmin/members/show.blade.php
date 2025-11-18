@extends('layouts.app')

@section('content')
{{-- 
==========================================================
Tampilan Halaman (View)
File: resources/views/admin/superadmin/members/show.blade.php
Tujuan: Menampilkan detail profil anggota dan riwayat peminjaman buku
==========================================================
--}}
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">
    
    {{-- Header dengan tombol Kembali --}}
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Anggota</h1>
            <p class="text-gray-500 mt-1">Informasi lengkap dan riwayat aktivitas anggota.</p>
        </div>
        <a href="{{ route('admin.superadmin.members.index') }}" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-transform transform hover:-translate-y-px">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- === KOLOM KIRI: KARTU PROFIL === --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden h-fit">
            {{-- Header Kartu --}}
            <div class="bg-gradient-to-r from-red-600 to-red-700 h-32 relative">
                <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2">
                    {{-- Foto Profil (Inisial atau Gambar) --}}
                    <div class="h-24 w-24 rounded-full bg-white p-1 shadow-lg">
                        <div class="h-full w-full rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-3xl font-bold overflow-hidden">
                            @if($member->profile_photo)
                                <img src="{{ asset('storage/' . $member->profile_photo) }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Isi Profil --}}
            <div class="pt-16 pb-8 px-6 text-center">
                <h2 class="text-2xl font-bold text-gray-800">{{ $member->name }}</h2>
                
                {{-- Badge Role --}}
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-semibold capitalize
                    @if($member->role == 'siswa') bg-blue-100 text-blue-800
                    @elseif($member->role == 'guru') bg-purple-100 text-purple-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $member->role }}
                </span>

                <div class="mt-6 space-y-4 text-left">
                    {{-- Email --}}
                    <div class="flex items-start space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Email</p>
                            <p class="text-gray-800 font-medium break-words">{{ $member->email }}</p>
                        </div>
                    </div>

                    {{-- Telepon --}}
                    <div class="flex items-start space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Telepon</p>
                            <p class="text-gray-800 font-medium">{{ $member->phone_number ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Detail Khusus Siswa --}}
                    @if($member->role == 'siswa')
                    <div class="flex items-start space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Info Akademik</p>
                            <p class="text-gray-800 font-medium">NIS: {{ $member->nis ?? '-' }}</p>
                            <p class="text-gray-800 font-medium">
                                Kelas: 
                                @if($member->class == 'Lulus') 
                                    <span class="text-green-600 font-bold">Lulus</span>
                                @elseif($member->class && $member->major) 
                                    {{ $member->class }} {{ $member->major }}
                                @else 
                                    - 
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Detail Khusus Guru --}}
                    @if($member->role == 'guru')
                    <div class="flex items-start space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Mata Pelajaran</p>
                            <p class="text-gray-800 font-medium">{{ $member->subject ?? '-' }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Status Akun --}}
                    <div class="flex items-start space-x-3 pt-2 border-t border-gray-100">
                        <div class="w-full flex justify-between items-center">
                            <p class="text-xs text-gray-500 uppercase font-semibold">Status Akun</p>
                            <span class="px-2 py-1 text-xs font-semibold rounded 
                                {{ $member->account_status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($member->account_status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 px-4">
                    <a href="{{ route('admin.superadmin.members.edit', $member->id) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg text-center transition-colors shadow">
                        Edit Profil
                    </a>
                </div>
            </div>
        </div>

        {{-- === KOLOM KANAN: TABEL RIWAYAT PEMINJAMAN === --}}
        <div class="lg:col-span-2 flex flex-col gap-6">
            
            {{-- Statistik Ringkas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Transaksi</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $totalLoans }}</h3>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-full text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 {{ $activeLoans > 0 ? 'border-yellow-500' : 'border-green-500' }} flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Sedang Dipinjam</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $activeLoans }}</h3>
                    </div>
                    <div class="p-3 {{ $activeLoans > 0 ? 'bg-yellow-50 text-yellow-600' : 'bg-green-50 text-green-600' }} rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="bg-white rounded-lg shadow-md overflow-hidden flex-1">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Riwayat Peminjaman Buku
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th class="px-6 py-3">Judul Buku</th>
                                <th class="px-6 py-3">Kode Eksemplar</th>
                                <th class="px-6 py-3">Tgl Pinjam</th>
                                <th class="px-6 py-3">Tgl Kembali</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($borrowings as $borrowing)
                            <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $borrowing->bookCopy->book->title ?? 'Data Buku Terhapus' }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500">
                                    {{ $borrowing->bookCopy->book_code ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($borrowing->borrowed_at)->format('d M Y') }}
                                    <br>
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($borrowing->borrowed_at)->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($borrowing->returned_at)
                                        {{ \Carbon\Carbon::parse($borrowing->returned_at)->format('d M Y') }}
                                        <br>
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($borrowing->returned_at)->format('H:i') }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if(in_array($borrowing->status, ['pending', 'dipinjam']))
                                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full border border-yellow-200">
                                            Dipinjam
                                        </span>
                                    @elseif($borrowing->status == 'returned')
                                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full border border-green-200">
                                            Dikembalikan
                                        </span>
                                    @elseif($borrowing->status == 'rejected')
                                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full border border-red-200">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full border border-gray-200">
                                            {{ ucfirst($borrowing->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 bg-gray-50">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <p class="text-gray-500">Belum ada riwayat peminjaman.</p>
                                    </div>
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