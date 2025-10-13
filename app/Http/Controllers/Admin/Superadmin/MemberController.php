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
     * Menampilkan form untuk mengedit data anggota.
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
            
            // ==========================================================
            // PERUBAHAN 1: Validasi untuk data siswa disempurnakan
            // ==========================================================
            'nis' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($member->id), Rule::requiredIf($request->role === 'siswa')],
            'class_name' => ['nullable', 'string', 'max:50', Rule::requiredIf($request->role === 'siswa')], // Diubah dari 'class'
            
            // Validasi data guru
            'phone_number' => 'nullable|string|max:15',
            'subject' => 'nullable|string|max:100',
        ]);

        // Update data utama
        $member->name = $request->name;
        $member->email = $request->email;
        $member->role = $request->role;
        $member->account_status = $request->account_status;

        // ==========================================================
        // PERUBAHAN 2: Logika penyimpanan data spesifik role diperbaiki
        // ==========================================================
        if ($request->role == 'siswa') {
            $member->nis = $request->nis;
            $member->class_name = $request->class_name; // Diubah dari 'class'
            $member->phone_number = $request->phone_number;
            // Kosongkan data guru
            $member->subject = null;
        } elseif ($request->role == 'guru') {
            $member->subject = $request->subject;
            $member->phone_number = $request->phone_number;
            // Kosongkan data siswa
            $member->nis = null;
            $member->class_name = null; // Diubah dari 'class'
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $member->password = Hash::make($request->password);
        }

        $member->save();

        return redirect()->route('admin.superadmin.members.index')->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Menghapus data anggota dari database.
     */
    public function destroy(User $member)
    {
        $member->delete();
        return redirect()->route('admin.superadmin.members.index')->with('success', 'Anggota berhasil dihapus.');
    }
}