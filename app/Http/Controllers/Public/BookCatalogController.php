<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Genre;
use App\Models\HeroSlider;
use App\Models\Borrowing;
use App\Models\LearningMaterial;
use App\Models\User;
use App\Models\LibrarySchedule;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; 

class BookCatalogController extends Controller
{
    public function index(Request $request)
    {
        $heroSliders = HeroSlider::where('is_active', true)->latest()->get();
        $genres = Genre::take(6)->get();
        
        $nonTextbookQuery = Book::where('book_type', 'reguler');

        // 1. Buku Favorit (Populer)
        $favoriteBooks = (clone $nonTextbookQuery)
            ->withCount([
                'copies as available_copies_count' => function ($query) {
                    $query->where('status', 'tersedia');
                },
                'copies as copies_count',
                'borrowings' => function ($query) {
                    $query->where('borrowings.status', '!=', 'pending')
                            ->where('borrowings.status', '!=', 'ditolak');
                }
            ])
            ->orderByDesc('borrowings_count')
            ->limit(10)
            ->get();

        // 2. Buku Terbaru
        $latestBooks = (clone $nonTextbookQuery)
            ->withCount([
                'copies as available_copies_count' => fn($q) => $q->where('status', 'tersedia'),
                'copies as copies_count'
            ])
            ->latest()
            ->limit(10)
            ->get();


        // ==========================================================
        // --- 🔥 FITUR BARU: Top Readers (Juara Membaca) 🔥 ---
        // ==========================================================
        $now = now();
        $currentYear = $now->year;
        
        // Tentukan Semester (Jan-Jun atau Jul-Des)
        if ($now->month >= 1 && $now->month <= 6) {
            $startDate = Carbon::create($currentYear, 1, 1)->startOfDay();
            $endDate = Carbon::create($currentYear, 6, 30)->endOfDay();
            $semesterTitle = "Semester Ini (Jan - Jun)";
        } else {
            $startDate = Carbon::create($currentYear, 7, 1)->startOfDay();
            $endDate = Carbon::create($currentYear, 12, 31)->endOfDay();
            $semesterTitle = "Semester Ini (Jul - Des)";
        }

        // Query Mencari 3 Siswa Terrajin
        $topBorrowers = User::where('role', 'siswa') // Hanya siswa
            ->withCount(['borrowings' => function($q) use ($startDate, $endDate) {
                // Hanya hitung peminjaman di semester ini & status valid
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->whereIn('status', ['dipinjam', 'returned']);
            }])
            ->orderBy('borrowings_count', 'desc') // Urutkan dari terbanyak
            ->take(3) // Ambil 3 Juara
            ->get();

        // ==========================================================
        // --- AKHIR FITUR BARU ---
        // ==========================================================


        // 3. Materi Pembelajaran
        $learningMaterials = LearningMaterial::where('is_active', true)
            ->with('user')
            ->latest()
            ->limit(4)
            ->get();

        // 4. Jadwal Perpustakaan
        $schedulesByDay = LibrarySchedule::with('user')
            ->whereIn('day_of_week', [1, 2, 3, 4, 5])
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('day_of_week');
        
        $todayDayOfWeek = now()->dayOfWeekIso; 
        
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
        ];

        // Kirim semua variabel ke View
        return view('public.catalog.index', compact(
            'heroSliders', 
            'genres', 
            'favoriteBooks', 
            'latestBooks', 
            'topBorrowers', // <-- Variabel Juara Membaca
            'learningMaterials',
            'semesterTitle',
            'schedulesByDay',
            'todayDayOfWeek',
            'days'
        ));
    }

    public function allBooks(Request $request)
    {
        $genres = Genre::orderBy('name')->get();
        $search = $request->input('search');
        $selectedGenreName = $request->input('genre');
        $sort = $request->input('sort', 'latest');

        $booksQuery = Book::query()->withCount([
            'copies as copies_count',
            'copies as available_copies_count' => fn($q) => $q->where('status', 'tersedia'),
            'borrowings' => function ($query) {
                $query->where('borrowings.status', '!=', 'pending')
                        ->where('borrowings.status', '!=', 'ditolak');
            }
        ]);

        $booksQuery->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
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
            $booksQuery->orderByDesc('borrowings_count'); 
        } else {
            $booksQuery->latest();
        }

        $books = $booksQuery->paginate(12)->withQueryString();
        return view('public.catalog.all_books', compact('books', 'genres'));
    }

    public function show(Book $book)
    {
       $book->load(['shelf', 'genre', 'copies']);
       $book->loadCount([
           'copies as copies_count',
           'copies as available_copies_count' => function ($query) {
               $query->where('status', 'tersedia');
           }
       ]);
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

    public function showLibrarians()
    {
        $staff = User::whereIn('role', ['petugas', 'guru'])
            ->orderBy('name', 'asc')
            ->get();
        return view('public.librarians', compact('staff'));
    }

    public function allMaterials(Request $request)
    {
        $query = LearningMaterial::where('is_active', true)
            ->with('user')
            ->latest();
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('teacher')) {
            $query->where('user_id', $request->teacher);
        }
        $materials = $query->paginate(10)->withQueryString();
        $teachers = User::where('role', 'guru')
            ->whereHas('learningMaterials')
            ->orderBy('name')
            ->get();
        return view('public.catalog.all_materials',  compact('materials', 'teachers'));
    }
}