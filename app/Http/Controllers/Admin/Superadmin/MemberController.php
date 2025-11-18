<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Major;
use App\Models\Borrowing; // ✅ WAJIB IMPORT MODEL INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Menampilkan daftar anggota dengan Filter, Pencarian, dan Pagination.
     */
    public function index(Request $request)
    {
        // 1. Query Dasar (Ambil Siswa, Guru, Petugas)
        $query = User::whereIn('role', ['siswa', 'guru', 'petugas']);

        // 2. Logika Filter (Dropdown)
        if ($request->filled('filter_role')) {
            $filter = $request->filter_role;

            if ($filter == 'siswa_lulus') {
                $query->where('role', 'siswa')->where('class', 'Lulus');
            } elseif ($filter == 'siswa_aktif') {
                $query->where('role', 'siswa')->where('class', '!=', 'Lulus');
            } else {
                $query->where('role', $filter);
            }
        }

        // 3. Logika Pencarian (Search)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('nis', 'like', '%' . $searchTerm . '%'); // Tambahan cari NIS
            });
        }

        // 4. Ambil Data dengan Pagination
        $members = $query->orderBy('name', 'asc')->paginate(15);
        
        // Pastikan parameter filter tetap ada saat pindah halaman (pagination)
        $members->appends($request->all());

        // 5. Hitung Siswa Lulus (Untuk tombol Hapus Masal)
        $graduatedCount = User::where('role', 'siswa')->where('class', 'Lulus')->count();
        
        return view('admin.superadmin.members.index', compact('members', 'graduatedCount'));
    }

    /**
     * 🔥 FITUR BARU: Menampilkan Detail Anggota & Riwayat Peminjaman
     */
    public function show(User $member)
    {
        // Ambil riwayat peminjaman user ini
        // Menggunakan 'with' untuk Eager Loading agar performa cepat (relasi ke buku)
        $borrowings = Borrowing::with(['bookCopy.book', 'bookCopy'])
                                ->where('user_id', $member->id)
                                ->orderBy('created_at', 'desc') // Yang terbaru di atas
                                ->get();

        // Hitung ringkasan kecil
        $activeLoans = $borrowings->whereIn('status', ['pending', 'dipinjam'])->count();
        $totalLoans = $borrowings->count();

        return view('admin.superadmin.members.show', compact('member', 'borrowings', 'activeLoans', 'totalLoans'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(User $member)
    {
        // Ambil data Jurusan untuk dropdown di view
        $majors = Major::orderBy('name', 'asc')->get();
        
        return view('admin.superadmin.members.edit', compact('member', 'majors'));
    }

    /**
     * Update data anggota dengan Validasi Kuat.
     */
    public function update(Request $request, User $member)
    {
        // --- 1. PERSIAPAN DATA SEBELUM VALIDASI ---
        
        if ($request->role == 'siswa') {
            // Jika Siswa Lulus, dan input class kosong (karena disabled di form),
            // kita set manual agar validasi tidak gagal.
            if ($member->class == 'Lulus' && empty($request->class)) {
                $request->merge(['class' => 'Lulus']);
            }

            // Kosongkan data guru
            $request->merge(['subject' => null]);
        } 
        elseif ($request->role == 'guru') {
            // Kosongkan data siswa
            $request->merge([
                'nis' => null,
                'class' => null,
                'major' => null,
            ]);
        }

        // --- 2. ATURAN VALIDASI ---
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($member->id)],
            'phone_number' => 'nullable|string|max:20',
            'role' => 'required|in:siswa,guru,petugas',
            'account_status' => 'required|in:pending,active,rejected,suspended',
            'password' => 'nullable|min:8|confirmed',
            
            // Validasi SISWA (Wajib jika role = siswa)
            'nis' => ['nullable', Rule::requiredIf($request->role == 'siswa'), 'max:20'],
            'class' => ['nullable', Rule::requiredIf($request->role == 'siswa'), 'in:X,XI,XII,Lulus'],
            'major' => ['nullable', Rule::requiredIf($request->role == 'siswa'), 'exists:majors,name'],
            
            // Validasi GURU (Wajib jika role = guru)
            'subject' => ['nullable', Rule::requiredIf($request->role == 'guru'), 'max:100'],
        ];

        $validatedData = $request->validate($rules);

        // --- 3. SIMPAN DATA ---
        $member->name = $validatedData['name'];
        $member->email = $validatedData['email'];
        $member->phone_number = $validatedData['phone_number'];
        $member->role = $validatedData['role'];
        $member->account_status = $validatedData['account_status'];

        // Simpan data spesifik role (otomatis null jika tidak relevan berkat merge di atas)
        $member->nis = $request->nis;
        $member->class = $request->class;
        $member->major = $request->major;
        $member->subject = $request->subject;
        
        // Bersihkan kolom legacy
        $member->class_name = null;

        // Update password jika diisi
        if (!empty($validatedData['password'])) {
            $member->password = Hash::make($validatedData['password']);
        }

        $member->save();

        return redirect()->route('admin.superadmin.members.index')
                         ->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * 🔥 UPDATE: Hapus satu anggota dengan pengecekan pinjaman
     */
    public function destroy(User $member)
    {
        // 1. Cek apakah masih ada pinjaman aktif (pending atau dipinjam)
        $hasActiveLoans = Borrowing::where('user_id', $member->id)
                                   ->whereIn('status', ['pending', 'dipinjam'])
                                   ->exists();

        if ($hasActiveLoans) {
            return redirect()->back()->with('error', 'GAGAL MENGHAPUS: Anggota ini masih memiliki pinjaman buku yang belum dikembalikan!');
        }

        // 2. Jika aman, hapus
        $member->delete();
        return redirect()->route('admin.superadmin.members.index')->with('success', 'Anggota berhasil dihapus.');
    }

    /**
     * 🔥 UPDATE: Hapus Massal dengan pengecekan pinjaman (SKIP yang masih pinjam)
     */
    public function destroyGraduated()
    {
        $graduatedStudents = User::where('role', 'siswa')->where('class', 'Lulus')->get();

        if ($graduatedStudents->isEmpty()) {
            return redirect()->route('admin.superadmin.members.index')->with('error', 'Tidak ada data siswa lulus.');
        }

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($graduatedStudents as $student) {
            // Cek pinjaman aktif per siswa
            $hasActiveLoans = Borrowing::where('user_id', $student->id)
                                       ->whereIn('status', ['pending', 'dipinjam'])
                                       ->exists();

            if ($hasActiveLoans) {
                // Lewati siswa ini
                $skippedCount++;
            } else {
                // Hapus siswa ini
                $student->delete();
                $deletedCount++;
            }
        }

        // Pesan umpan balik yang informatif
        if ($deletedCount > 0) {
            $message = "Berhasil membersihkan $deletedCount akun siswa lulus.";
            if ($skippedCount > 0) {
                $message .= " ($skippedCount akun DILEWATI karena masih meminjam buku).";
            }
            return redirect()->route('admin.superadmin.members.index')->with('success', $message);
        } else {
            // Jika semua dilewati
            return redirect()->route('admin.superadmin.members.index')
                             ->with('error', "Gagal menghapus masal. Semua $skippedCount siswa lulus masih memiliki tanggungan buku!");
        }
    }
}