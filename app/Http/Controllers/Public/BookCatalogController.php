<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookCatalogController extends Controller
{
    /**
     * Menampilkan halaman utama katalog buku untuk publik.
     */
    public function index()
    {
        // Ambil semua buku beserta relasi genrenya untuk ditampilkan
        $books = Book::with('genre')->latest()->get();
        
        // Ganti 'catalog.index' dengan nama view yang sesuai jika berbeda
        return view('public.catalog.index', compact('books'));
    }

    /**
     * Menampilkan halaman detail satu buku untuk publik.
     */
    public function show(Book $book)
    {
        // Eager load relasi yang dibutuhkan
        $book->load('genre', 'copies');
        
        // Ganti 'catalog.show' dengan nama view yang sesuai jika berbeda
        return view('public.catalog.show', compact('book'));
    }
}
