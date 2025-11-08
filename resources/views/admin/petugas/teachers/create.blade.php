@extends('layouts.app') {{-- Menggunakan layout yang Anda berikan --}}

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Buat Akun Guru Baru</h1>
        <p class="text-gray-500 mt-1">Isi form di bawah untuk mendaftarkan akun guru.</p>
    </div>

    {{-- Menampilkan error validasi (jika ada) --}}
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 border-l-4 border-red-500 rounded-r-lg shadow">
            <strong class="font-bold">Oops! Ada kesalahan:</strong>
            <ul class="list-disc list-inside mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 
        ==========================================================
        --- REVISI: Desain Form Menggunakan Tailwind
        ==========================================================
    --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden max-w-2xl">
        
        {{-- TAMBAHAN: Memberi ID pada form untuk SweetAlert --}}
        <form action="{{ route('admin.petugas.teachers.store') }}" method="POST" id="create-teacher-form">
            @csrf
            
            <div class="p-6 space-y-6">

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            {{-- Ikon Peringatan --}}
                            <svg class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Catatan:</strong> Pastikan Anda memberi tahu guru yang bersangkutan <strong>email</strong> dan <strong>password</strong> yang Anda buatkan.
                            </p>
                        </div>
                    </div>
                </div>
                {{-- Input Nama Lengkap --}}
                <div>
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap:</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 @error('name') border-red-500 @enderror" 
                           required>
                    @error('name') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Input Email --}}
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 @error('email') border-red-500 @enderror" 
                           required>
                    @error('email') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Input Mata Pelajaran --}}
                <div>
                    <label for="subject" class="block text-gray-700 text-sm font-bold mb-2">Mata Pelajaran:</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" 
                           class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 @error('subject') border-red-500 @enderror" 
                           required>
                    @error('subject') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Input Password --}}
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                    <input type="password" id="password" name="password" 
                           class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 @error('password') border-red-500 @enderror" 
                           required>
                    @error('password') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Input Konfirmasi Password --}}
                <div>
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password:</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="shadow-sm appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" 
                           required>
                </div>
            </div>

            {{-- Footer Tombol --}}
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.petugas.teachers.index') }}" class="text-gray-600 hover:text-gray-800 font-medium text-sm">Kembali</a>
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
                    Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>

{{-- 
    ==========================================================
    --- TAMBAHAN: Script SweetAlert untuk Konfirmasi ---
    ==========================================================
--}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Ambil form berdasarkan ID
    const form = document.getElementById('create-teacher-form');
    
    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Hentikan submit
            
            Swal.fire({
                title: 'Buat Akun Guru?',
                text: "Pastikan data yang Anda masukkan sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626', // Warna merah (bg-red-600)
                cancelButtonColor: '#6B7280',  // Warna abu-abu (text-gray-500)
                confirmButtonText: 'Ya, Buat Akun!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Lanjutkan submit jika dikonfirmasi
                }
            });
        });
    }
</script>
@endsection