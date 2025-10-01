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
        // Pengecekan keamanan: Pastikan user adalah siswa yang statusnya pending
        if ($user->role === 'siswa' && $user->account_status === 'pending') {
            $user->account_status = 'active';
            $user->save();

            return redirect()->back()->with('success', 'Akun siswa berhasil diaktifkan.');
        }

        // Beri pesan error jika aksi tidak valid (misal: user sudah di-acc)
        return redirect()->back()->with('error', 'Aksi tidak valid atau akun sudah diproses.');
    }

    /**
     * Menolak/menghapus pendaftaran siswa.
     */
    public function reject(User $user)
    {
        // Pengecekan keamanan: Pastikan user adalah siswa yang statusnya pending
        if ($user->role === 'siswa' && $user->account_status === 'pending') {
            // Hapus file foto dari storage sebelum menghapus data user
            if ($user->student_card_photo) {
                // Menggunakan Storage::disk('public') adalah praktik terbaik
                Storage::disk('public')->delete($user->student_card_photo);
            }

            $user->delete();

            return redirect()->back()->with('success', 'Pendaftaran siswa berhasil ditolak.');
        }
        
        // Beri pesan error jika aksi tidak valid
        return redirect()->back()->with('error', 'Aksi tidak valid atau akun sudah diproses.');
    }
}

