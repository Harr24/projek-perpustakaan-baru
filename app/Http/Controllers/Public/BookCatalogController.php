<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Genre;
use App\Models\HeroSlider;
use App\Models\Borrowing;
use App\Models\LearningMaterial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookCatalogController extends Controller
{
    public function index(Request $request)
    {
        // ... (Fungsi ini tidak diubah)
        $heroSliders = HeroSlider::where('is_active', true)
                                      ->latest()
                                      ->get();
        $genres = Genre::take(6)->get();
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
        $learningMaterials = LearningMaterial::where('is_active', true)
                                               ->with('user')
                                               ->latest()
                                               ->limit(4)
                                               ->get();
        return view('public.catalog.index', compact('heroSliders', 'genres', 'favoriteBooks', 'latestBooks', 'topBorrowers', 'learningMaterials'));
    }

    public function allBooks(Request $request)
    {
        // ... (Fungsi ini tidak diubah)
        $genres = Genre::orderBy('name')->get();
        $search = $request->input('search');
        $selectedGenreName = $request->input('genre');
        $sort = $request->input('sort', 'latest');
        $booksQuery = Book::query()->withCount([
            'copies',
            'copies as available_copies_count' => fn($q) => $q->where('status', 'tersedia')
        ]);
        $booksQuery->when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('author', 'LIKE', '%' . $search . '%');
            });
        });
        $booksQuery->when($selectedGenreName, function ($query, $genreName) {
            return $query->whereHas('genre', function ($q) use ($genreName) {
                $q->where('name', $genreName);
            });
        });
        if ($sort === 'popular') {
            $booksQuery->orderByDesc('available_copies_count');
        } else {
            $booksQuery->latest();
        }
        $books = $booksQuery->paginate(12)->withQueryString();
        return view('public.catalog.all_books', compact('books', 'genres'));
    }
    
    public function show(Book $book)
    {
        // ... (Fungsi ini tidak diubah)
        $book->load('genre', 'copies');
        $book->loadCount(['copies as available_copies_count' => function ($query) {
            $query->where('status', 'tersedia');
        }]);
        return view('public.catalog.show', compact('book'));
    }

    public function showCover(Book $book)
    {
        // ... (Fungsi ini tidak diubah)
        $path = $book->cover_image;
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Gambar tidak ditemukan.');
        }
        $file = Storage::disk('public')->get($path);
        $type = Storage::disk('public')->mimeType($path);
        return response($file)->header('Content-Type', $type);
    }
    
    public function showLibrarians()
    {
        // ... (Fungsi ini tidak diubah)
        $staff = User::whereIn('role', ['petugas', 'guru'])
                       ->orderBy('name', 'asc')
                       ->get();
        return view('public.librarians', compact('staff'));
    }

    // ==========================================================
    // METHOD YANG DIPERBARUI: Ditambahkan logika filter & pencarian
    // ==========================================================
    public function allMaterials(Request $request)
    {
        // Ambil query dasar untuk materi belajar
        $query = LearningMaterial::where('is_active', true)
                                 ->with('user')
                                 ->latest();

        // Terapkan filter pencarian judul jika ada
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Terapkan filter guru jika ada
        if ($request->filled('teacher')) {
            $query->where('user_id', $request->teacher);
        }

        // Ambil hasil query dengan paginasi
        $materials = $query->paginate(10)->withQueryString();

        // Ambil daftar guru yang pernah mengunggah materi (untuk dropdown filter)
        $teachers = User::where('role', 'guru')
                        ->whereHas('learningMaterials') 
                        ->orderBy('name')
                        ->get();

        // Kirim semua data yang dibutuhkan ke view
        return view('public.catalog.all_materials', compact('materials', 'teachers'));
    }
}

