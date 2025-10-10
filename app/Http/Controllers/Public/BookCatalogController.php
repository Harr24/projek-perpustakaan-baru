<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Genre;
use App\Models\HeroSlider;
use App\Models\Borrowing;
// ==========================================================
// 1. IMPORT MODEL BARU ANDA
// ==========================================================
use App\Models\LearningMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookCatalogController extends Controller
{
    public function index(Request $request)
    {
        // Ambil Data Hero Slider
        $heroSliders = HeroSlider::where('is_active', true)
                                 ->latest() // Urutkan berdasarkan yang terbaru
                                 ->get();

        // Ambil Data Genre
        $genres = Genre::take(6)->get();

        // Ambil Data Buku Favorit & Terbaru
        $nonTextbookQuery = Book::where('is_textbook', 0);

        $favoriteBooks = (clone $nonTextbookQuery)
            ->withCount([
                'copies',
                'copies as available_copies_count' => fn($q) => $q->where('status', 'tersedia')
            ])
            ->orderByDesc('available_copies_count')
            ->limit(10)
            ->get();
        
        $latestBooks = (clone $nonTextbookQuery)
            ->withCount([
                'copies',
                'copies as available_copies_count' => fn($q) => $q->where('status', 'tersedia')
            ])
            ->latest()
            ->limit(10)
            ->get();

        // Ambil Data Peminjam Teratas
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

        // ==========================================================
        // 2. AMBIL DATA MATERI PEMBELAJARAN
        // Mengambil 4 materi terbaru yang aktif dan data gurunya.
        // ==========================================================
        $learningMaterials = LearningMaterial::where('is_active', true)
                                ->with('user') // Eager load data guru
                                ->latest()
                                ->limit(4) // Ambil 4 materi terbaru
                                ->get();

        // 3. Kirim semua data ke view
        return view('public.catalog.index', compact('heroSliders', 'genres', 'favoriteBooks', 'latestBooks', 'topBorrowers', 'learningMaterials'));
    }

    public function allBooks(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('author', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('genre')) {
            $genreName = $request->genre;
            $query->whereHas('genre', function($q) use ($genreName) {
                $q->where('name', 'like', '%' . $genreName . '%');
            });
        }
        
        $query->withCount([
            'copies',
            'copies as available_copies_count' => function ($query) {
                $query->where('status', 'tersedia');
            }
        ]);

        $sort = $request->input('sort', 'latest');
        if ($sort === 'popular') {
            $query->orderByDesc('available_copies_count');
        } else {
            $query->latest();
        }

        $books = $query->paginate(12)->withQueryString();
        
        return view('public.catalog.all_books', compact('books'));
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
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Gambar tidak ditemukan.');
        }
        $file = Storage::disk('public')->get($path);
        $type = Storage::disk('public')->mimeType($path);
        return response($file)->header('Content-Type', $type);
    }
}

