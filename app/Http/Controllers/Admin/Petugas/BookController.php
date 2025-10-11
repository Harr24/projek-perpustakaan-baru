<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    /**
     * ===================================================================
     * PERUBAHAN DI SINI:
     * - Menambahkan filter berdasarkan genre.
     * - Mengambil semua data genre untuk dikirim ke view.
     * - Mengubah paginasi dari 15 menjadi 10 per halaman.
     * - Memastikan filter dan pencarian tetap aktif saat paginasi.
     * ===================================================================
     */
    public function index(Request $request)
    {
        // 1. Ambil semua genre untuk ditampilkan di dropdown filter
        $genres = Genre::orderBy('name')->get();

        // 2. Ambil input dari form (pencarian dan filter genre)
        $search = $request->input('search');
        $genreId = $request->input('genre_id');

        // 3. Mulai query builder
        $query = Book::with('genre')->withCount('copies')->latest();

        // 4. Terapkan filter PENCARIAN jika ada
        $query->when($search, function ($q) use ($search) {
            // Mengelompokkan 'where' untuk memastikan logika OR tidak bentrok dengan filter lain
            return $q->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'LIKE', "%{$search}%")
                         ->orWhere('author', 'LIKE', "%{$search}%");
            });
        });

        // 5. Terapkan filter GENRE jika ada
        $query->when($genreId, function ($q) use ($genreId) {
            return $q->where('genre_id', $genreId);
        });

        // 6. Ambil data dengan PAGINASI 10 per halaman
        // withQueryString() memastikan filter tetap ada di URL saat pindah halaman
        $books = $query->paginate(10)->withQueryString();
        
        // 7. Kirim data buku dan daftar genre ke view
        return view('admin.petugas.books.index', compact('books', 'genres'));
    }

    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.petugas.books.create', compact('genres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|digits:4|integer|min:1900|max:'.(date('Y')),
            'synopsis' => 'nullable|string',
            'genre_id' => 'required|exists:genres,id',
            'initial_code' => 'required|string|max:10|alpha_num',
            'stock' => 'required|integer|min:1|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_textbook' => 'nullable|boolean',
        ]);

        $genre = Genre::find($validated['genre_id']);
        $prefix = $genre->genre_code . '-' . Str::upper($validated['initial_code']) . '-';

        if (BookCopy::where('book_code', 'LIKE', $prefix . '%')->exists()) {
            throw ValidationException::withMessages([
               'initial_code' => 'Kombinasi Kode Awal dan Genre ini sudah digunakan.',
            ]);
        }
        
        DB::transaction(function () use ($request, $validated, $prefix) {
            if ($request->hasFile('cover_image')) {
                $path = $request->file('cover_image')->store('covers', 'public');
                $validated['cover_image'] = $path;
            }

            $validated['is_textbook'] = $request->has('is_textbook');
            $book = Book::create($validated);
            
            for ($i = 1; $i <= $validated['stock']; $i++) {
                $copyNumber = str_pad($i, 3, '0', STR_PAD_LEFT);
                BookCopy::create([
                    'book_id' => $book->id,
                    'book_code' => $prefix . $copyNumber,
                    'status' => 'tersedia'
                ]);
            }
        });
        
        return redirect()->route('admin.petugas.books.index')->with('success', 'Buku berhasil ditambahkan.');
    }
    
    public function show(Book $book)
    {
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|digits:4|integer|min:1900|max:'.(date('Y')),
            'synopsis' => 'nullable|string',
            'genre_id' => 'required|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_textbook' => 'nullable|boolean',
            'add_stock' => 'nullable|integer|min:1|max:100',
        ]);

        DB::transaction(function () use ($request, $validated, $book) {
            if ($request->hasFile('cover_image')) {
                if ($book->cover_image) { Storage::disk('public')->delete($book->cover_image); }
                $path = $request->file('cover_image')->store('covers', 'public');
                $validated['cover_image'] = $path;
            }
            
            $validated['is_textbook'] = $request->has('is_textbook');
            $book->update($validated);

            if ($request->filled('add_stock')) {
                $lastCopy = $book->copies()->latest('id')->first();
                if (!$lastCopy) {
                    throw ValidationException::withMessages(['add_stock' => 'Buku ini tidak memiliki salinan awal. Tidak dapat menambah stok.']);
                }

                $parts = explode('-', $lastCopy->book_code);
                $lastNumber = (int)array_pop($parts);
                $prefix = implode('-', $parts) . '-';

                for ($i = 1; $i <= $validated['add_stock']; $i++) {
                    $newNumber = $lastNumber + $i;
                    $copyNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
                    BookCopy::create([
                        'book_id' => $book->id,
                        'book_code' => $prefix . $copyNumber,
                        'status' => 'tersedia'
                    ]);
                }
            }
        });

        return redirect()->route('admin.petugas.books.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        $book->delete();
        return redirect()->route('admin.petugas.books.index')->with('success', 'Buku dan semua salinannya berhasil dihapus.');
    }
}