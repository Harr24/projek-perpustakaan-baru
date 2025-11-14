<?php

namespace App\Http\Controllers;

use App\Models\Major; // Ini sudah benar
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
     * (Fungsi ini sudah benar, dia mengirim data $majors)
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

        // ==========================================================
        // --- 🔥 PERUBAHAN UTAMA: VALIDASI YANG DIKUNCI 🔥 ---
        // ==========================================================
        // Kita HANYA memvalidasi data yang BISA DIUBAH oleh siswa.
        // Data 'name', 'email', 'class', dan 'major' kita hapus dari validasi.
        $request->validate([
            'phone_number' => ['nullable', 'string', 'max:15', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        // --- AKHIR PERUBAHAN VALIDASI ---

        // Logika upload foto (Ini sudah benar dan akan berjalan sekarang!)
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        // ==========================================================
        // --- 🔥 PERUBAHAN UTAMA: LOGIKA PENYIMPANAN 🔥 ---
        // ==========================================================
        // Kita HANYA menyimpan data yang BISA DIUBAH.
        
        // $user->name = $request->name;       // <-- HAPUS
        // $user->email = $request->email;     // <-- HAPUS
        $user->phone_number = $request->phone_number; // <-- TETAP SIMPAN

        // if ($user->role === 'siswa') {      // <-- HAPUS BLOK INI
        //     $user->class = $request->class;
        //     $user->major = $request->major;
        // }
        
        // Logika simpan password (Ini sudah benar)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        // --- AKHIR PERUBAHAN PENYIMPANAN ---

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
    }
}