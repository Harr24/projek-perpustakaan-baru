<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('genre')->withCount('copies')->latest()->get();
        return view('admin.petugas.books.index', compact('books'));
    }

    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.petugas.books.create', compact('genres'));
    }

    /**
     * PERUBAHAN UTAMA ADA DI SINI
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'initial_code' => 'required|string|max:10|alpha_num',
            'stock' => 'required|integer|min:1|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('public/covers');
            $validated['cover_image'] = $path;
        }

        // 2. Simpan data buku utama
        $book = Book::create($validated);

        // ==========================================================
        // LOGIKA BARU UNTUK MEMBUAT KODE UNIK
        // ==========================================================
        
        // 3. Ambil data genre untuk mendapatkan 'genre_code'
        $genre = Genre::find($validated['genre_id']);
        $genreCode = $genre->genre_code; // Contoh: "01"
        $initialCode = Str::upper($validated['initial_code']); // Contoh: "NN"

        // 4. Loop untuk membuat data eksemplar/kopi buku
        for ($i = 1; $i <= $validated['stock']; $i++) {
            $copyNumber = str_pad($i, 3, '0', STR_PAD_LEFT); // Contoh: "001"

            // Gabungkan menjadi kode unik final, format: Genre-Inisial-Nomor
            $uniqueBookCode = $genreCode . '-' . $initialCode . '-' . $copyNumber; // Hasil: "01-NN-001"
            
            BookCopy::create([
                'book_id' => $book->id,
                'book_code' => $uniqueBookCode,
                'status' => 'tersedia',
            ]);
        }
        
        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Buku "' . $book->title . '" dan ' . $validated['stock'] . ' salinannya berhasil ditambahkan.');
    }
    
    // Method show(), edit(), update(), destroy() tidak perlu diubah
    public function show(Book $book)
    {
        $book->load('genre', 'copies');
        return view('admin.petugas.books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.petugas.books.edit', compact('book', 'genres'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::delete($book->cover_image);
            }
            $path = $request->file('cover_image')->store('public/covers');
            $validated['cover_image'] = $path;
        }

        $book->update($validated);

        return redirect()->route('admin.petugas.books.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::delete($book->cover_image);
        }
        $book->delete();
        return redirect()->route('admin.petugas.books.index')->with('success', 'Buku dan semua salinannya berhasil dihapus.');
    }
}