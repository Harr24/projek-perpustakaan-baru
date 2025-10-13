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
        return view('profile.edit', compact('user'));
    }

    /**
     * Memperbarui data profil pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // ==========================================================
        // PERUBAHAN 1: Tambahkan validasi untuk 'phone_number'
        // ==========================================================
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // Aturan baru: nomor telepon harus unik, kecuali untuk user ini sendiri
            'phone_number' => ['nullable', 'string', 'max:15', Rule::unique('users')->ignore($user->id)],
            'class_name' => [Rule::requiredIf($user->role === 'siswa'), 'nullable', 'string', 'max:50'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Proses foto jika ada
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        // ==========================================================
        // PERUBAHAN 2: Simpan data dasar, termasuk 'phone_number'
        // ==========================================================
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number; // <-- SIMPAN NOMOR TELEPON

        // Simpan data kelas HANYA jika pengguna adalah siswa
        if ($user->role === 'siswa') {
            $user->class_name = $request->class_name;
        }

        // Proses password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Simpan semua perubahan
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}