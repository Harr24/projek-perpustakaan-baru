<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profil untuk pengguna yang sedang login.
     */
    public function edit()
    {
        $user = Auth::user();
        
        // Mengarahkan ke view profil yang lebih umum, bukan spesifik superadmin
        return view('profile.edit', compact('user'));
    }

    /**
     * Memperbarui data profil pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. VALIDASI: Menambahkan validasi untuk 'class_name'
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // 'class_name' hanya wajib diisi jika role pengguna adalah 'siswa'
            'class_name' => [Rule::requiredIf($user->role === 'siswa'), 'nullable', 'string', 'max:50'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. PROSES DATA: Memperbarui data dasar
        $user->name = $request->name;
        $user->email = $request->email;

        // Tambahkan logika untuk menyimpan kelas HANYA jika pengguna adalah siswa
        if ($user->role === 'siswa') {
            $user->class_name = $request->class_name;
        }

        // Proses password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 3. PROSES FOTO: Logika upload foto yang lebih baik
        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            // Simpan foto baru di 'storage/app/public/profile-photos'
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        // 4. SIMPAN SEMUA PERUBAHAN
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}