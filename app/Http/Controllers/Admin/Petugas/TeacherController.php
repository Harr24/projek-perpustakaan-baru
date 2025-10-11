<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TeacherController extends Controller
{
    /**
     * Menampilkan daftar guru yang sudah dibuat.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $teachers = User::where('role', 'guru')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.petugas.teachers.index', compact('teachers'));
    }
    
    /**
     * Menampilkan form untuk membuat akun guru baru.
     */
    public function create()
    {
        return view('admin.petugas.teachers.create');
    }

    /**
     * Menyimpan data akun guru baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi data input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'subject' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // 2. Buat user baru
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'password' => Hash::make($request->password),
            'role' => 'guru', // Langsung set role sebagai 'guru'
            'account_status' => 'active', // Langsung set status 'active'
        ]);

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('admin.petugas.teachers.index')->with('success', 'Akun guru berhasil dibuat!');
    }

    /**
     * Menampilkan form untuk mengedit akun guru.
     */
    public function edit(User $teacher)
    {
        // Pastikan user yang diakses adalah guru, jika bukan, tampilkan error 404
        abort_if($teacher->role !== 'guru', 404);

        return view('admin.petugas.teachers.edit', compact('teacher'));
    }

    /**
     * Memperbarui data akun guru di database.
     */
    public function update(Request $request, User $teacher)
    {
        // Pastikan user yang diakses adalah guru
        abort_if($teacher->role !== 'guru', 404);

        // 1. Validasi data input
        $request->validate([
            'name' => 'required|string|max:255',
            // Rule::unique('users')->ignore($teacher->id)
            // -> Memastikan email unik, tapi mengabaikan email milik guru yang sedang diedit
            'email' => 'required|string|email|max:255|unique:users,email,' . $teacher->id,
            'subject' => 'required|string|max:255',
            // 'nullable' -> Boleh kosong jika tidak ingin ganti password
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        // 2. Update data guru
        $teacher->name = $request->name;
        $teacher->email = $request->email;
        $teacher->subject = $request->subject;

        // 3. Cek jika password diisi, maka update password
        if ($request->filled('password')) {
            $teacher->password = Hash::make($request->password);
        }

        // 4. Simpan perubahan
        $teacher->save();

        // 5. Redirect kembali dengan pesan sukses
        return redirect()->route('admin.petugas.teachers.index')->with('success', 'Data guru berhasil diperbarui!');
    }
}