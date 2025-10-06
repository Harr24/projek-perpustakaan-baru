<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy; // <-- WAJIB DITAMBAHKAN
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // <-- WAJIB DITAMBAHKAN

class BookController extends Controller
{
    public function index()
    {
        // Menambahkan eager loading untuk relasi genre dan menghitung total salinan
        $books = Book::with('genre')->withCount('copies')->latest()->get();
        return view('admin.petugas.books.index', compact('books'));
    }

    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.petugas.books.create', compact('genres'));
    }

    /**
     * ==========================================================
     * PERUBAHAN UTAMA DI METHOD STORE INI
     * ==========================================================
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form, termasuk input baru kita
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'initial_code' => 'required|string|max:10|alpha_num', // Validasi untuk kode awal
            'stock' => 'required|integer|min:1|max:100', // Batasi max stock per input
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // 2. Handle upload gambar cover
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('public/covers');
            $validated['cover_image'] = $path;
        }

        // 3. Simpan data buku utama ke tabel 'books'
        $book = Book::create($validated);

        // 4. Loop untuk membuat data eksemplar/kopi buku
        $initialCode = Str::upper($validated['initial_code']);
        for ($i = 1; $i <= $validated['stock']; $i++) {
            // Membuat nomor urut dengan 3 digit (001, 002, ..., 010, dst.)
            $copyNumber = str_pad($i, 3, '0', STR_PAD_LEFT);

            // Gabungkan menjadi kode unik final, contoh: AGAMA-001
            $uniqueBookCode = $initialCode . '-' . $copyNumber;
            
            BookCopy::create([
                'book_id' => $book->id,
                'book_code' => $uniqueBookCode, // Menggunakan nama kolom dari migrasi Anda
                'status' => 'tersedia',      // Menggunakan status dari migrasi Anda
            ]);
        }
        
        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Buku "' . $book->title . '" dan ' . $validated['stock'] . ' salinannya berhasil ditambahkan.');
    }
    
    public function show(Book $book)
    {
        // Eager load relasi untuk efisiensi
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
        // Note: Logika update hanya untuk data master buku, tidak untuk stok.
        // Manajemen stok (tambah/kurang) adalah fitur terpisah.
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

        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::delete($book->cover_image);
        }

        // Karena ada onDelete('cascade') di migrasi, semua book_copies terkait akan terhapus otomatis.
        $book->delete();

        return redirect()->route('admin.petugas.books.index')
                         ->with('success', 'Buku dan semua salinannya berhasil dihapus.');
    }
}