<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // WAJIB DITAMBAHKAN

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

    /**
     * FUNGSI BARU: Untuk mengambil inisial dari judul buku.
     */
    private function getTitleInitials(string $title): string
    {
        $words = preg_split("/\s+/", trim($title)); // Memecah kata dengan lebih baik
        $initials = '';

        if (count($words) >= 2) {
            // Ambil huruf pertama dari dua kata pertama
            $initials = Str::upper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } elseif (count($words) === 1 && !empty($words[0])) {
            // Jika hanya satu kata, ambil dua huruf pertama
            $initials = Str::upper(substr($words[0], 0, 2));
        }

        return $initials;
    }

    /**
     * PERUBAHAN UTAMA DI SINI
     * Menyimpan buku baru dengan kode yang dibuat otomatis sesuai format.
     */
    public function store(Request $request)
    {
        // 1. Validasi (tanpa book_code)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer|min:1',
        ]);
        
        // Handle upload gambar
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        // 2. Simpan buku utama (tanpa kolom 'book_code' lagi)
        $book = Book::create($validated);

        // 3. Persiapan untuk membuat kode unik
        $genre = Genre::find($validated['genre_id']);
        $genreCode = $genre->genre_code; // Mengambil kode genre (misal: "01")
        $titleInitials = $this->getTitleInitials($validated['title']); // Mengambil inisial judul (misal: "AQ")
        
        // 4. Loop untuk membuat setiap salinan buku
        for ($i = 1; $i <= $validated['stock']; $i++) {
            // Format nomor urut menjadi 2 digit (01, 02, ..., 10)
            $copyNumber = str_pad($i, 2, '0', STR_PAD_LEFT);

            // Gabungkan semua bagian menjadi kode final: KodeGenre-InisialJudul-NoUrut
            $uniqueCode = $genreCode . $titleInitials . $copyNumber;
            
            BookCopy::create([
                'book_id' => $book->id,
                'book_code' => $uniqueCode,
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

