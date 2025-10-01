<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * Menampilkan daftar semua genre.
     */
    public function index()
    {
        // PERUBAHAN DI SINI: dari latest() menjadi oldest()
        $genres = Genre::oldest()->get(); 
        return view('admin.petugas.genres.index', compact('genres'));
    }

    /**
     * Menampilkan form untuk membuat genre baru.
     */
    public function create()
    {
        return view('admin.petugas.genres.create');
    }

    /**
     * Menyimpan genre baru dengan kode otomatis.
     */
    public function store(Request $request)
    {
        // 1. Validasi nama genre dari input user
        $request->validate([
            'name' => 'required|string|max:255|unique:genres,name',
        ]);

        // 2. Logika untuk membuat kode genre otomatis
        $latestGenre = Genre::latest('id')->first();
        $newCodeNumber = 1; // Default jika ini genre pertama

        if ($latestGenre) {
            $lastCode = (int) $latestGenre->genre_code;
            $newCodeNumber = $lastCode + 1;
        }

        // 3. Format angka menjadi 2 digit dengan awalan nol (01, 02, ..., 10, 11)
        $newGenreCode = str_pad($newCodeNumber, 2, '0', STR_PAD_LEFT);

        // 4. Simpan genre baru dengan nama dari user dan kode yang dibuat otomatis
        Genre::create([
            'name' => $request->name,
            'genre_code' => $newGenreCode,
        ]);

        // 5. Kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit genre.
     */
    public function edit(Genre $genre)
    {
        return view('admin.petugas.genres.edit', compact('genre'));
    }

    /**
     * Mengupdate data genre di database.
     */
    public function update(Request $request, Genre $genre)
    {
        // Kode update hanya mengubah nama, tidak mengubah kode genre
        $request->validate([
            'name' => 'required|string|max:255|unique:genres,name,' . $genre->id
        ]);
        
        $genre->update($request->only('name'));

        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre berhasil diperbarui.');
    }

    /**
     * Menghapus genre dari database.
     */
    public function destroy(Genre $genre)
    {
        $genre->delete();
        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre berhasil dihapus.');
    }
}

