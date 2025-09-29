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
                            ->get();
        
        return view('admin.petugas.verification.index', compact('pendingUsers'));
    }

    /**
     * Menyetujui pendaftaran siswa.
     */
    public function approve(User $user)
    {
        $user->account_status = 'active';
        $user->save();

        return redirect()->back()->with('success', 'Akun siswa berhasil diaktifkan.');
    }

    /**
     * Menolak/menghapus pendaftaran siswa.
     */
    public function reject(User $user)
    {
        // Hapus file foto dari storage sebelum menghapus data user
        if ($user->student_card_photo) {
            Storage::delete($user->student_card_photo);
        }

        $user->delete();

        return redirect()->back()->with('success', 'Pendaftaran siswa berhasil ditolak.');
    }
}