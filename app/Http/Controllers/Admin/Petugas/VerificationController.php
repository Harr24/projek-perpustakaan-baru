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
                            ->orderBy('created_at', 'asc')
                            ->get();
        
        return view('admin.petugas.verification.index', compact('pendingUsers'));
    }

    /**
     * Menyetujui pendaftaran siswa.
     */
    public function approve(User $user)
    {
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
        if ($user->role === 'siswa' && $user->account_status === 'pending') {
            
            if (!empty($user->student_card_photo)) {
                // Gunakan Storage::delete() untuk konsistensi
                Storage::delete($user->student_card_photo);
            }

            $user->delete();

            return redirect()->back()->with('success', 'Pendaftaran siswa berhasil ditolak.');
        }
        
        return redirect()->back()->with('error', 'Aksi tidak valid atau akun sudah diproses.');
    }

    // Method showStudentCard telah dihapus dari sini.
}