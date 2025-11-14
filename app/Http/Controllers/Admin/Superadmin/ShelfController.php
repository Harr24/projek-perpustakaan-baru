<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- Kita masih butuh ini untuk validasi 'unique'

class ShelfController extends Controller
{
    /**
     * Menampilkan daftar semua rak.
     */
    public function index()
    {
        // Ambil data rak, 10 per halaman
        $shelves = Shelf::latest()->paginate(10);
        
        // Kirim data $shelves ke view
        return view('admin.superadmin.shelves.index', compact('shelves'));
    }

    /**
     * Menampilkan form untuk membuat rak baru.
     */
    public function create()
    {
        return view('admin.superadmin.shelves.create');
    }

    /**
     * Menyimpan rak baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi data (HANYA 'name')
        $validatedData = $request->validate([
            // 'name' harus diisi, unik (tidak boleh ada yang sama), dan maks 255 karakter
            'name' => 'required|string|max:255|unique:shelves,name',
        ]);

        // 2. Buat data baru di tabel 'shelves'
        Shelf::create($validatedData);

        // 3. Alihkan kembali dengan pesan sukses
        return redirect()->route('admin.superadmin.shelves.index')
                         ->with('success', 'Rak baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit rak.
     */
    public function edit(Shelf $shelf)
    {
        // Kirim data rak ($shelf) yang mau diedit ke view
        return view('admin.superadmin.shelves.edit', compact('shelf'));
    }

    /**
     * Memperbarui data rak di database.
     */
    public function update(Request $request, Shelf $shelf)
    {
        // 1. Validasi data (HANYA 'name')
        $validatedData = $request->validate([
            // 'name' harus unik, TAPI kita 'ignore' (abaikan) ID-nya sendiri
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shelves', 'name')->ignore($shelf->id),
            ],
        ]);

        // 2. Update data rak
        $shelf->update($validatedData);

        // 3. Alihkan kembali dengan pesan sukses
        return redirect()->route('admin.superadmin.shelves.index')
                         ->with('success', 'Data rak berhasil diperbarui.');
    }

    /**
     * Menghapus rak dari database.
     */
    public function destroy(Shelf $shelf)
    {
        // Fitur Keamanan: Cek apakah rak masih memiliki buku
        if ($shelf->books()->count() > 0) {
            // JIKA IYA, JANGAN HAPUS!
            return redirect()->route('admin.superadmin.shelves.index')
                             ->with('error', 'Gagal menghapus! Rak ini masih terhubung dengan beberapa buku.');
        }

        // Jika rak sudah kosong, baru kita hapus
        $shelf->delete();

        // Alihkan kembali dengan pesan sukses
        return redirect()->route('admin.superadmin.shelves.index')
                         ->with('success', 'Rak berhasil dihapus.');
    }
}