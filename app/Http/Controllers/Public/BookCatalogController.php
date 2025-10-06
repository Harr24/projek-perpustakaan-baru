<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookCatalogController extends Controller
{
    public function index()
    {
        // ==========================================================
        // PERUBAHAN DI SINI: Memuat relasi 'copies' yang statusnya 'tersedia'
        // ==========================================================
        $books = Book::with(['genre', 'copies' => function ($query) {
            // Kita hanya ambil data salinan yang statusnya 'tersedia'
            $query->where('status', 'tersedia');
        }])->latest()->paginate(12);
        
        return view('public.catalog.index', compact('books'));
    }

    public function show(Book $book)
    {
        $book->load('genre', 'copies');
        $availableCopiesCount = $book->copies->where('status', 'tersedia')->count();
        $firstAvailableCopy = $book->copies->where('status', 'tersedia')->first();
        return view('public.catalog.show', compact('book', 'availableCopiesCount', 'firstAvailableCopy'));
    }

    public function showCover(Book $book)
    {
        $path = $book->cover_image;
        if (!$path || !Storage::exists($path)) {
            abort(404, 'Gambar tidak ditemukan.');
        }
        $file = Storage::get($path);
        $type = Storage::mimeType($path);
        return response($file)->header('Content-Type', $type);
    }
}