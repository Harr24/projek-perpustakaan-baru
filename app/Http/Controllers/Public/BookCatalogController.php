<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['genre', 'copies' => function ($q) {
            $q->where('status', 'tersedia');
        }]);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('author', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('genre')) {
            $genreSlug = $request->genre;
            $query->whereHas('genre', function($q) use ($genreSlug) {
                $q->where('name', 'like', '%' . $genreSlug . '%');
            });
        }

        $books = $query->latest()->paginate(12);
        
        $genres = Genre::take(6)->get();

        // ==========================================================
        // PERUBAHAN DI SINI: Hanya mencari peminjaman oleh 'siswa'
        // ==========================================================
        $topBorrowers = Borrowing::whereHas('user', function ($query) {
                                    $query->where('role', 'siswa');
                                 })
                                 ->whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->select('user_id', DB::raw('count(*) as loans_count'))
                                 ->groupBy('user_id')
                                 ->orderBy('loans_count', 'desc')
                                 ->limit(3)
                                 ->with('user')
                                 ->get();

        return view('public.catalog.index', compact('books', 'genres', 'topBorrowers'));
    }

    // ... sisa method (show, showCover) tidak perlu diubah ...
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
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Gambar tidak ditemukan.');
        }
        $file = Storage::disk('public')->get($path);
        $type = Storage::disk('public')->mimeType($path);
        return response($file)->header('Content-Type', $type);
    }
}