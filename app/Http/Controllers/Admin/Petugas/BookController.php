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
        // ... (kode store dari langkah sebelumnya, tidak perlu diubah)
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'first_copy_code' => 'required|string|max:255|unique:book_copies,copy_code',
            'stock' => 'required|integer|min:1',
        ]);

        $path = null;
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('public/covers');
        }

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'genre_id' => $request->genre_id,
            'cover_image' => $path,
        ]);
        
        preg_match('/(.*?)(-|\/)?(\d*)$/', $request->first_copy_code, $matches);
        $prefix = $matches[1] ?? $request->first_copy_code;
        $separator = $matches[2] ?? '-';
        $startNumberStr = $matches[3] ?? '1';
        $startNumber = (int)$startNumberStr;
        $padding = strlen($startNumberStr);

        for ($i = 0; $i < $request->stock; $i++) {
            $currentNumber = $startNumber + $i;
            $copyCode = $prefix . $separator . str_pad($currentNumber, $padding, '0', STR_PAD_LEFT);
            
            BookCopy::create([
                'book_id' => $book->id,
                'copy_code' => $copyCode,
            ]);
        }
        
        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Buku dan semua salinannya berhasil ditambahkan.');
    }

    public function show(Book $book)
    {
        // Untuk sekarang tidak kita gunakan
    }

    public function edit(Book $book)
    {
        $genres = Genre::all(); // Ambil semua genre untuk dropdown
        return view('admin.petugas.books.edit', compact('book', 'genres'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $book->cover_image;
        if ($request->hasFile('cover_image')) {
            // Hapus gambar lama jika ada
            if ($path) {
                Storage::delete($path);
            }
            // Simpan gambar baru
            $path = $request->file('cover_image')->store('public/covers');
        }

        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'genre_id' => $request->genre_id,
            'cover_image' => $path,
        ]);

        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        // Hapus gambar dari storage jika ada
        if ($book->cover_image) {
            Storage::delete($book->cover_image);
        }

        // Hapus data buku dari database
        // (Semua book_copies yang terhubung akan ikut terhapus karena onDelete('cascade'))
        $book->delete();

        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Buku berhasil dihapus.');
    }
}