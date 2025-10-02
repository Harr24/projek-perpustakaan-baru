<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    /**
     * Menampilkan daftar siswa yang statusnya pending.
     */
    public function index()
    {
        $pendingUsers = User::where('role', 'siswa')
                            ->where('account_status', 'pending')
                            ->orderBy('created_at', 'asc') // Urutkan dari yang paling lama mendaftar
                            ->get();
        
        return view('admin.petugas.verification.index', compact('pendingUsers'));
    }

    /**
     * Menyetujui pendaftaran siswa.
     */
    public function approve(User $user)
    {
        // Pastikan hanya siswa dengan status pending yang bisa di-approve
        if ($user->role === 'siswa' && $user->account_status === 'pending') {
            $user->account_status = 'active';
            $user->save();

            return redirect()->back()->with('success', 'Akun siswa berhasil diaktifkan.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid atau akun sudah diproses.');
    }

    /**
     * Menolak/menghapus pendaftaran siswa.
     */
    public function reject(User $user)
    {
        // Pastikan hanya siswa dengan status pending yang bisa ditolak
        if ($user->role === 'siswa' && $user->account_status === 'pending') {
            
            // Hapus file foto kartu pelajar dari storage (jika ada)
            // Catatan: Storage::disk('public') tidak diperlukan jika default filesystem Anda sudah 'public'
            if (!empty($user->student_card_photo)) {
                Storage::delete($user->student_card_photo);
            }

            // Hapus data user
            $user->delete();

            return redirect()->back()->with('success', 'Pendaftaran siswa berhasil ditolak.');
        }
        
        return redirect()->back()->with('error', 'Aksi tidak valid atau akun sudah diproses.');
    }

    // ==========================================================
    // TAMBAHAN: Method untuk menampilkan foto kartu pelajar
    // ==========================================================
    /**
     * Menampilkan foto kartu pelajar dari storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function showStudentCard(User $user)
    {
        // Ambil path foto dari database
        $path = $user->student_card_photo;

        // Pastikan path ada dan file-nya benar-benar ada di storage
        if (!$path || !Storage::exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Baca file dari storage
        $file = Storage::get($path);
        
        // Dapatkan tipe mime file (contoh: image/jpeg)
        $type = Storage::mimeType($path);

        // Kirim response ke browser dengan isi file dan header yang benar
        return response($file)->header('Content-Type', $type);
    }
}