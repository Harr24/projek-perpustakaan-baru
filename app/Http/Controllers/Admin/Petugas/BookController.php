<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('genre')->latest()->get();
        return view('admin.petugas.books.index', compact('books'));
    }

    public function create()
    {
        $genres = Genre::all();
        return view('admin.petugas.books.create', compact('genres'));
    }

    public function store(Request $request)
    {
        // PERBAIKAN 1: Validasi disederhanakan dan disesuaikan
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'book_code' => 'required|string|max:255', // Menggunakan 'book_code'
            'stock' => 'required|integer|min:1',
        ]);

        // Handle upload gambar sampul jika ada
        if ($request->hasFile('cover_image')) {
            // Menghapus path 'public/' agar bisa diakses via Storage::url()
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        // PERBAIKAN 2: Simpan semua data tervalidasi ke buku utama
        $book = Book::create($validated);

        // PERBAIKAN 3: Logika pembuatan kode unik disederhanakan
        for ($i = 1; $i <= $validated['stock']; $i++) {
            // Buat kode unik untuk setiap salinan, misal: M001-HTR-1, M001-HTR-2, dst.
            $uniqueCode = $validated['book_code'] . '-' . $i;

            BookCopy::create([
                'book_id' => $book->id,
                'book_code' => $uniqueCode, // Menggunakan 'book_code' yang benar
            ]);
        }
        
        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Buku dan semua salinannya berhasil ditambahkan.');
    }

    public function show(Book $book)
    {
        $book->load('copies');
        return view('admin.petugas.books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $genres = Genre::all();
        return view('admin.petugas.books.edit', compact('book', 'genres'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        $book->update($validated);

        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Buku berhasil dihapus.');
    }
}