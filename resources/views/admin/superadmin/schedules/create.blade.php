@extends('layouts.admin')

@section('content')
    {{-- Header Halaman --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Tambah Jadwal Petugas
        </h1>
        <a href="{{ route('admin.superadmin.schedules.index') }}" class="inline-flex items-center bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg shadow-sm transition-colors">
            Kembali ke Daftar
        </a>
    </div>

    {{-- 
        ==========================================================
        --- KARTU FORM DENGAN TAILWIND ---
        ==========================================================
    --}}
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            
            <form action="{{ route('admin.superadmin.schedules.store') }}" method="POST">
                @csrf
                
                {{-- Header Kartu (Merah) --}}
                <div class="bg-red-700 p-4">
                    <h3 class="text-lg font-bold text-white">
                        Formulir Jadwal Baru
                    </h3>
                </div>

                {{-- Body Kartu (Form) --}}
                <div class="p-6 space-y-6">
                    
                    {{-- Dropdown Pilih Petugas --}}
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Petugas
                        </label>
                        <select name="user_id" id="user_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('user_id') border-red-500 @enderror" 
                                required>
                            <option value="">-- Pilih Petugas --</option>
                            @foreach($staff as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} (Petugas)
                                </option>
                            @endforeach
                        </select>
                        
                        @error('user_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dropdown Pilih Hari --}}
                    <div>
                        <label for="day_of_week" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Hari
                        </label>
                        <select name="day_of_week" id="day_of_week" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('day_of_week') border-red-500 @enderror" 
                                required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach($days as $dayNumber => $dayName)
                                <option value="{{ $dayNumber }}" {{ old('day_of_week') == $dayNumber ? 'selected' : '' }}>
                                    {{ $dayName }}
                                </option>
                            @endforeach
                        </select>
                        
                        @error('day_of_week')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Footer Kartu (Tombol Aksi) --}}
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <a href="{{ route('admin.superadmin.schedules.index') }}" class="inline-flex items-center bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg shadow-sm transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center bg-red-700 hover:bg-red-800 text-white text-sm font-medium py-2 px-4 rounded-lg shadow-sm transition-colors">
                        Simpan Jadwal
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection