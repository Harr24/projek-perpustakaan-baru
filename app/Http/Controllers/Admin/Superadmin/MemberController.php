<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Menampilkan daftar semua anggota (siswa & guru) dengan fitur pencarian dan pagination.
     */
    public function index(Request $request)
    {
        // Query dasar untuk mengambil user dengan role siswa atau guru
        $query = User::whereIn('role', ['siswa', 'guru']);

        // Jika ada input pencarian, tambahkan kondisi where
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Ambil data dengan urutan nama dan pagination
        $members = $query->orderBy('name', 'asc')->paginate(15);
        
        // Kembalikan view dengan data anggota
        return view('admin.superadmin.members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     * (Tidak digunakan sesuai konfigurasi route kita)
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * (Tidak digunakan sesuai konfigurasi route kita)
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Menampilkan form untuk mengedit data anggota.
     * Menggunakan Route Model Binding untuk mengambil user secara otomatis.
     */
    public function edit(User $member)
    {
        return view('admin.superadmin.members.edit', compact('member'));
    }

    /**
     * Memperbarui data anggota di database.
     */
    public function update(Request $request, User $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($member->id)],
            'role' => 'required|in:siswa,guru',
            'account_status' => 'required|in:pending,active,rejected,suspended',
            'password' => 'nullable|string|min:8|confirmed',
            
            // Validasi untuk data tambahan
            'nis' => 'nullable|string|max:20',
            'class' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:15',
            'subject' => 'nullable|string|max:100',
        ]);

        // Update data utama
        $member->fill($request->only([
            'name', 'email', 'role', 'account_status'
        ]));

        // Update data spesifik role
        if ($request->role == 'siswa') {
            $member->fill($request->only(['nis', 'class', 'major', 'phone_number']));
            $member->subject = null; // Kosongkan data guru jika role diubah ke siswa
        } elseif ($request->role == 'guru') {
            $member->fill($request->only(['subject', 'phone_number']));
            // Kosongkan data siswa jika role diubah ke guru
            $member->nis = null;
            $member->class = null;
            $member->major = null;
        }

        if ($request->filled('password')) {
            $member->password = Hash::make($request->password);
        }

        $member->save();

        return redirect()->route('admin.superadmin.members.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Menghapus data anggota dari database.
     * Menggunakan Route Model Binding untuk mengambil user secara otomatis.
     */
    public function destroy(User $member)
    {
        // Opsi: Tambahkan logika untuk menghapus file terkait (foto, dll) jika perlu
        // Storage::delete($member->student_card_photo);
        
        $member->delete();

        return redirect()->route('admin.superadmin.members.index')->with('success', 'Anggota berhasil dihapus.');
    }
}