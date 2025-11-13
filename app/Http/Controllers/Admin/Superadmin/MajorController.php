<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Major; // 1. Import Model Major
use App\Models\User;  // 2. Import Model User (untuk pengecekan)
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // 3. Import Rule (untuk validasi unik)

class MajorController extends Controller
{
    /**
     * Menampilkan daftar semua jurusan.
     * (Halaman 'index' untuk CRUD)
     */
    public function index()
    {
        // Ambil semua jurusan, urutkan berdasarkan nama, dan buat paginasi
        $majors = Major::orderBy('name', 'asc')->paginate(15);

        // Kirim data ke view
        return view('admin.superadmin.majors.index', compact('majors'));
    }

    /**
     * Menampilkan formulir untuk membuat jurusan baru.
     * (Halaman 'create')
     */
    public function create()
    {
        // Langsung tampilkan view formulir
        return view('admin.superadmin.majors.create');
    }

    /**
     * Menyimpan data jurusan baru dari formulir 'create' ke database.
     * (Logika untuk 'store')
     */
    public function store(Request $request)
    {
        // Validasi data yang masuk
        // 'name' harus diisi, unik (tidak boleh sama di tabel 'majors'), dan maks 255 karakter
        $request->validate([
            'name' => 'required|string|max:255|unique:majors,name',
        ], [
            'name.required' => 'Nama jurusan tidak boleh kosong.',
            'name.unique' => 'Nama jurusan ini sudah ada.',
        ]);

        // Jika validasi lolos, buat data baru
        Major::create([
            'name' => $request->name,
        ]);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.superadmin.majors.index')
                         ->with('success', 'Jurusan baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir untuk mengedit data jurusan.
     * (Halaman 'edit')
     *
     * @param  \App\Models\Major  $major
     */
    public function edit(Major $major)
    {
        // Kita menggunakan Route Model Binding di sini.
        // Laravel otomatis mengambil data 'Major' berdasarkan ID di URL.
        // Kirim data $major yang mau diedit ke view
        return view('admin.superadmin.majors.edit', compact('major'));
    }

    /**
     * Menyimpan perubahan dari formulir 'edit' ke database.
     * (Logika untuk 'update')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Major  $major
     */
    public function update(Request $request, Major $major)
    {
        // Validasi data yang masuk
        // Mirip seperti 'store', tapi aturan 'unique' harus MENGABAIKAN ID
        // dari jurusan yang sedang kita edit.
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('majors')->ignore($major->id),
            ],
        ], [
            'name.required' => 'Nama jurusan tidak boleh kosong.',
            'name.unique' => 'Nama jurusan ini sudah ada.',
        ]);

        // Jika validasi lolos, update data
        $major->update([
            'name' => $request->name,
        ]);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.superadmin.majors.index')
                         ->with('success', 'Jurusan berhasil diperbarui.');
    }

    /**
     * Menghapus data jurusan dari database.
     * (Logika untuk 'destroy')
     *
     * @param  \App\Models\Major  $major
     */
    public function destroy(Major $major)
    {
        // ==========================================================
        // --- FITUR KEAMANAN PENTING ---
        // ==========================================================
        // Cek dulu di tabel 'users', apakah ada siswa yang masih
        // menggunakan nama jurusan ini.
        $studentCount = User::where('role', 'siswa')
                            ->where('major', $major->name)
                            ->count();

        // JIKA MASIH ADA (lebih dari 0), jangan biarkan dihapus!
        if ($studentCount > 0) {
            return redirect()->route('admin.superadmin.majors.index')
                             ->with('error', "Jurusan '$major->name' tidak bisa dihapus karena masih digunakan oleh $studentCount siswa.");
        }

        // Jika $studentCount adalah 0 (aman), lanjutkan penghapusan
        $major->delete();

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.superadmin.majors.index')
                         ->with('success', 'Jurusan berhasil dihapus.');
    }
}