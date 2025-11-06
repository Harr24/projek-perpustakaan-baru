@extends('layouts.admin')

@section('content')
    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        
        {{-- Judul Halaman --}}
        <h1 class="text-3xl font-bold text-gray-800 mb-4 sm:mb-0">
            Kelola Jadwal Petugas
        </h1>
        
        {{-- Tombol Aksi --}}
        <div class="flex space-x-2">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg shadow-sm transition-colors">
                Kembali
            </a>
            <a href="{{ route('admin.superadmin.schedules.create') }}" class="inline-flex items-center bg-red-700 hover:bg-red-800 text-white text-sm font-medium py-2 px-4 rounded-lg shadow-sm transition-colors">
                {{-- SVG Ikon Tambah --}}
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Jadwal
            </a>
        </div>
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-6" role="alert">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Grid Konten --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-6">
        
        @foreach($days as $dayNumber => $dayName)
            {{-- Kartu Jadwal --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                
                {{-- Header Kartu (Merah) --}}
                <div class="bg-red-700 p-4">
                    <h3 class="text-lg font-bold text-white text-center">
                        {{ $dayName }}
                    </h3>
                </div>
                
                {{-- Body Kartu --}}
                <div class="p-0">
                    @if(isset($schedulesByDay[$dayNumber]) && $schedulesByDay[$dayNumber]->isNotEmpty())
                        {{-- Daftar Petugas --}}
                        <ul class="divide-y divide-gray-200">
                            @foreach($schedulesByDay[$dayNumber] as $schedule)
                                
                                <li class="px-5 py-4 text-center group"> {{-- Hapus flex, ganti ke text-center --}}
                                    
                                    {{-- Info User (langsung di center) --}}
                                    <strong class="text-gray-900 font-semibold text-lg">{{ $schedule->user->name }}</strong>
                                    <p class="text-gray-500 text-sm">Petugas</p>
                                    
                                    {{-- Tombol Hapus (juga di center, di bawah nama) --}}
                                    <form class="mt-2" action="{{ route('admin.superadmin.schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        {{-- 
                                          Ubah opacity:
                                          - Hapus: opacity-0 group-hover:opacity-100
                                          - Tambah: opacity-40 hover:opacity-100 mx-auto
                                          (Selalu terlihat, tapi transparan & pasti di tengah) 
                                        --}}
                                        <button type="submit" class="text-gray-400 hover:text-red-600 opacity-40 hover:opacity-100 mx-auto transition-all duration-200" title="Hapus Jadwal">
                                            {{-- SVG Ikon Hapus --}}
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        </button>
                                    </form>
                                </li>
                                @endforeach
                        </ul>
                    @else
                        {{-- Tampilan Jadwal Kosong --}}
                        <div class="px-5 py-10 flex flex-col items-center justify-center text-gray-400 italic">
                            {{-- SVG Ikon Kalender/Kosong --}}
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-1.414 1.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 006.586 13H4"></path></svg>
                            <p>Jadwal Kosong</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Kolom pengisi agar layout tablet 3x2 rapi (total 6 item) --}}
        <div class="hidden md:block xl:hidden">
            </div>
    </div>
@endsection