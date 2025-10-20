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
     * ==========================================================
     * METHOD BARU: Menampilkan halaman profil statis (hanya lihat)
     * ==========================================================
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Menampilkan halaman form edit profil untuk pengguna.
     * (Fungsi ini tidak diubah)
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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:15', Rule::unique('users')->ignore($user->id)],
            'class_name' => [Rule::requiredIf($user->role === 'siswa'), 'nullable', 'string', 'max:50'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;

        if ($user->role === 'siswa') {
            $user->class_name = $request->class_name;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // ==========================================================
        // PERUBAHAN UTAMA: Redirect kembali ke halaman LIHAT PROFIL
        // ==========================================================
        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
    }
}

