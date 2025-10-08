<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::oldest()->get(); 
        return view('admin.petugas.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.petugas.genres.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:genres,name']);

        $latestGenre = Genre::latest('id')->first();
        $newCodeNumber = $latestGenre ? ((int) $latestGenre->genre_code) + 1 : 1;
        $newGenreCode = str_pad($newCodeNumber, 2, '0', STR_PAD_LEFT);

        Genre::create([
            'name' => $request->name,
            'genre_code' => $newGenreCode,
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
        $request->validate(['name' => 'required|string|max:255|unique:genres,name,' . $genre->id]);
        $genre->update($request->only('name'));
        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre berhasil diperbarui.');
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre berhasil dihapus.');
    }
}