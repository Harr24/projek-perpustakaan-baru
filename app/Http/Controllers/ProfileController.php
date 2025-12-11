<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil statis (hanya lihat)
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Menampilkan halaman form edit profil untuk pengguna.
     */
    public function edit()
    {
        $user = Auth::user();
        
        $majors = [];
        if ($user->role === 'siswa') {
            $majors = Major::orderBy('name', 'asc')->get();
        }
        
        return view('profile.edit', compact('user', 'majors'));
    }

    /**
     * Memperbarui data profil pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi: Hanya memvalidasi data yang BISA DIUBAH oleh siswa.
        $request->validate([
            'phone_number' => ['nullable', 'string', 'max:15', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // ==========================================================
        // --- LOGIKA UPDATE FOTO (GANTI FOTO) ---
        // ==========================================================
        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada secara fisik
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            // Simpan foto baru
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        // ==========================================================
        // --- LOGIKA PENYIMPANAN DATA ---
        // ==========================================================
        // Hanya simpan data yang diizinkan ubah
        $user->phone_number = $request->phone_number;

        // Logika simpan password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Menghapus foto profil pengguna (Tombol Hapus).
     * Method BARU ditambahkan di sini.
     */
    public function deletePhoto()
    {
        $user = Auth::user();

        // Cek apakah user punya foto profil di database
        if ($user->profile_photo) {
            
            // 1. Hapus File Fisik di Storage (Cek dulu biar tidak error)
            if (Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // 2. Set kolom di database menjadi NULL
            $user->profile_photo = null;
            $user->save();

            return back()->with('success', 'Foto profil berhasil dihapus.');
        }

        return back()->with('error', 'Anda belum mengatur foto profil.');
    }
}