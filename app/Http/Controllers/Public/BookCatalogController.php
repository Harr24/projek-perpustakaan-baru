<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Genre;
use App\Models\HeroSlider;
use App\Models\Borrowing;
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
                                     ->latest()
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

        // Ambil Data Materi Pembelajaran
        $learningMaterials = LearningMaterial::where('is_active', true)
                                            ->with('user')
                                            ->latest()
                                            ->limit(4)
                                            ->get();

        // Kirim semua data ke view
        return view('public.catalog.index', compact('heroSliders', 'genres', 'favoriteBooks', 'latestBooks', 'topBorrowers', 'learningMaterials'));
    }

    /**
     * ==========================================================
     * PERUBAHAN DI SINI: Method allBooks diperbarui
     * ==========================================================
     */
    public function allBooks(Request $request)
    {
        // 1. Ambil semua genre untuk ditampilkan sebagai filter di view
        $genres = Genre::orderBy('name')->get();

        // Ambil input dari URL
        $search = $request->input('search');
        $selectedGenreName = $request->input('genre');
        $sort = $request->input('sort', 'latest');

        // 2. Mulai query builder
        $booksQuery = Book::query()->withCount([
            'copies',
            'copies as available_copies_count' => fn($q) => $q->where('status', 'tersedia')
        ]);

        // 3. Terapkan filter PENCARIAN jika ada
        $booksQuery->when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('author', 'LIKE', '%' . $search . '%');
            });
        });

        // 4. Terapkan filter GENRE jika ada
        $booksQuery->when($selectedGenreName, function ($query, $genreName) {
            return $query->whereHas('genre', function ($q) use ($genreName) {
                // Menggunakan pencocokan nama yang pasti, bukan 'like'
                $q->where('name', $genreName);
            });
        });

        // 5. Terapkan PENGURUTAN
        if ($sort === 'popular') {
            $booksQuery->orderByDesc('available_copies_count');
        } else {
            $booksQuery->latest(); // Default adalah buku terbaru
        }

        // 6. Ambil data dengan PAGINASI
        $books = $booksQuery->paginate(12)->withQueryString();
        
        // 7. Kirim data buku dan daftar genre ke view
        return view('public.catalog.all_books', compact('books', 'genres'));
    }
    
    public function show(Book $book)
    {
        // Memuat relasi genre dan semua salinan buku
        $book->load('genre', 'copies');

        // Menghitung dan menambahkan atribut 'available_copies_count'
        $book->loadCount(['copies as available_copies_count' => function ($query) {
            $query->where('status', 'tersedia');
        }]);

        return view('public.catalog.show', compact('book'));
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