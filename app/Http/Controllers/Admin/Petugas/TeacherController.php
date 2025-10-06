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
    public function index()
    {
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
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
}