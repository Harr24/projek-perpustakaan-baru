<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class SuperadminPetugasController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['petugas', 'superadmin'])
                      ->where('id', '!=', Auth::id())
                      ->latest()->get();

        return view('admin.superadmin.petugas.index', ['petugas' => $users]);
    }

    public function create()
    {
        return view('admin.superadmin.petugas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', Rule::in(['petugas', 'superadmin'])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => $request->role,
            'account_status' => 'active',
        ]);

        return redirect()->route('admin.superadmin.petugas.index')
                         ->with('success', 'Akun berhasil dibuat.');
    }

    public function edit($id)
    {
        $petugas = User::findOrFail($id);
        return view('admin.superadmin.petugas.edit', compact('petugas'));
    }

    public function update(Request $request, $id)
    {
        $petugas = User::findOrFail($id);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $petugas->id],
            'role'     => ['required', 'string', Rule::in(['petugas', 'superadmin'])],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $petugas->name  = $request->name;
        $petugas->email = $request->email;
        $petugas->role  = $request->role;

        if ($request->filled('password')) {
            $petugas->password = Hash::make($request->password);
        }

        $petugas->save();

        return redirect()->route('admin.superadmin.petugas.index')
                         ->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $petugas = User::findOrFail($id);
        $petugas->delete();

        return redirect()->route('admin.superadmin.petugas.index')
                         ->with('success', 'Akun berhasil dihapus.');
    }

    // --- METHOD BARU UNTUK PROFIL ---

    /**
     * Menampilkan form untuk mengedit profil superadmin yang sedang login.
     */
    public function showProfileForm()
    {
        return view('admin.superadmin.profile.edit', ['superadmin' => Auth::user()]);
    }

    /**
     * Mengupdate data profil superadmin yang sedang login.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Handle upload foto profil
        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            // Simpan foto baru
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}