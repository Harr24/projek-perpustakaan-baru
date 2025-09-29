<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::all();
        return view('admin.petugas.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.petugas.genres.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:genres|max:255']);
        Genre::create($request->all());
        return redirect()->route('admin.petugas.genres.index')->with('success', 'Genre berhasil ditambahkan.');
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
        $request->validate(['name' => 'required|string|max:255|unique:genres,name,' . $genre->id]);
        $genre->update($request->all());
        return redirect()->route('admin.petugas.genres.index')->with('success', 'Genre berhasil diperbarui.');
    }

    /**
     * Menghapus genre dari database.
     */
    public function destroy(Genre $genre)
    {
        $genre->delete();
        return redirect()->route('admin.petugas.genres.index')->with('success', 'Genre berhasil dihapus.');
    }
}