<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException; // <-- Pastikan ini ada

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'initial_code' => 'required|string|max:10|alpha_num',
            'stock' => 'required|integer|min:1|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // ==========================================================
        // FITUR BARU: Validasi Keunikan Kode Buku
        // ==========================================================
        $genre = Genre::find($validated['genre_id']);
        $prefix = $genre->genre_code . '-' . Str::upper($validated['initial_code']) . '-';

        // Cek apakah awalan kode ini sudah pernah digunakan
        $prefixExists = BookCopy::where('book_code', 'LIKE', $prefix . '%')->exists();

        if ($prefixExists) {
            // Jika sudah ada, kirim notifikasi error dan hentikan proses
            throw ValidationException::withMessages([
               'initial_code' => 'Kombinasi Kode Awal dan Genre ini sudah digunakan. Silakan gunakan Kode Awal yang lain.',
            ]);
        }
        // ==========================================================

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        $book = Book::create($validated);
        
        // Karena kita sudah pastikan unik, looping bisa dimulai dari 1
        for ($i = 1; $i <= $validated['stock']; $i++) {
            $copyNumber = str_pad($i, 3, '0', STR_PAD_LEFT);
            $uniqueBookCode = $prefix . $copyNumber;
            
            BookCopy::create([
                'book_id' => $book->id,
                'book_code' => $uniqueBookCode,
                'status' => 'tersedia',
            ]);
        }
        
        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Buku "' . $book->title . '" berhasil ditambahkan.');
    }
    
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
                Storage::disk('public')->delete($book->cover_image);
            }
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        $book->update($validated);
        return redirect()->route('admin.petugas.books.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        $book->delete();
        return redirect()->route('admin.petugas.books.index')->with('success', 'Buku dan semua salinannya berhasil dihapus.');
    }
}