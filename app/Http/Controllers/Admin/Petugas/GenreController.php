<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- TAMBAHKAN INI

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::oldest('genre_code')->get(); // Diurutkan berdasarkan kode
        return view('admin.petugas.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.petugas.genres.create');
    }

    public function store(Request $request)
    {
        // --- MODIFIKASI: Tambahkan validasi untuk genre_code ---
        $request->validate([
            'name' => 'required|string|max:255|unique:genres,name',
            'genre_code' => 'required|string|max:5|unique:genres,genre_code', // Validasi baru
        ]);

        // --- MODIFIKASI: Hapus logika kode otomatis ---
        // $latestGenre = Genre::latest('id')->first();
        // $newCodeNumber = $latestGenre ? ((int) $latestGenre->genre_code) + 1 : 1;
        // $newGenreCode = str_pad($newCodeNumber, 2, '0', STR_PAD_LEFT);

        // --- MODIFIKASI: Simpan dari $request ---
        Genre::create([
            'name' => $request->name,
            'genre_code' => $request->genre_code, // Ambil dari input
        ]);

        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre baru berhasil ditambahkan.');
    }

    public function edit(Genre $genre)
    {
        return view('admin.petugas.genres.edit', compact('genre'));
    }

    public function update(Request $request, Genre $genre)
    {
        // --- MODIFIKASI: Tambahkan validasi untuk genre_code saat update ---
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('genres')->ignore($genre->id), // Validasi unik yg benar
            ],
            'genre_code' => [
                'required', 'string', 'max:5',
                Rule::unique('genres')->ignore($genre->id), // Validasi unik yg benar
            ],
        ]);

        // --- MODIFIKASI: Update kedua field ---
        $genre->update([
            'name' => $request->name,
            'genre_code' => $request->genre_code,
        ]);

        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre berhasil diperbarui.');
    }

    public function destroy(Genre $genre)
    {
        // Cek relasi buku (PENTING!)
        if ($genre->books()->exists()) { // Asumsi Anda punya relasi 'books()' di model Genre
             return redirect()->route('admin.petugas.genres.index')
                              ->with('error', 'Gagal! Genre ini masih digunakan oleh beberapa buku.');
        }

        $genre->delete();
        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre berhasil dihapus.');
    }
}