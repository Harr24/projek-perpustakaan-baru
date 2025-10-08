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
    /**
     * Menampilkan halaman utama katalog buku, dengan logika pencarian dan filter.
     */
    public function index(Request $request)
    {
        // Query dasar untuk buku
        $query = Book::with(['genre', 'copies' => function ($q) {
            $q->where('status', 'tersedia');
        }]);

        // Logika untuk memproses pencarian
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('author', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // ==========================================================
        // TAMBAHAN: Logika untuk memproses filter genre
        // ==========================================================
        if ($request->filled('genre')) {
            $genreSlug = $request->genre;
            // Mencari buku yang memiliki relasi genre dengan slug yang cocok
            $query->whereHas('genre', function($q) use ($genreSlug) {
                $q->where('name', $genreSlug); // Asumsi kita filter berdasarkan nama genre
            });
        }

        $books = $query->latest()->paginate(12);
        
        $genres = Genre::take(6)->get();
        $topBorrowers = Borrowing::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->select('user_id', DB::raw('count(*) as loans_count'))
                                 ->groupBy('user_id')
                                 ->orderBy('loans_count', 'desc')
                                 ->limit(3)
                                 ->with('user')
                                 ->get();

        return view('public.catalog.index', compact('books', 'genres', 'topBorrowers'));
    }

    /**
     * Menampilkan halaman detail satu buku.
     */
    public function show(Book $book)
    {
        $book->load('genre', 'copies');
        $availableCopiesCount = $book->copies->where('status', 'tersedia')->count();
        $firstAvailableCopy = $book->copies->where('status', 'tersedia')->first();
        return view('public.catalog.show', compact('book', 'availableCopiesCount', 'firstAvailableCopy'));
    }

    /**
     * Menampilkan gambar sampul buku.
     */
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