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
    public function index(Request $request)
    {
        $genres = Genre::orderBy('name')->get();
        $search = $request->input('search');
        $genreId = $request->input('genre_id');

        // ==========================================================
        // PENAMBAHAN: withCount untuk menghitung buku yang dipinjam
        // ==========================================================
        $query = Book::with('genre')
                    ->withCount([
                        'copies', // Menghitung total eksemplar (copies_count)
                        'copies as borrowed_copies_count' => function ($query) {
                            // Hitung salinan yang statusnya 'dipinjam' ATAU 'overdue'
                            $query->whereIn('status', ['dipinjam', 'overdue']);
                        }
                    ])
                    ->latest();

        $query->when($search, function ($q) use ($search) {
            return $q->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'LIKE', "%{$search}%")
                         ->orWhere('author', 'LIKE', "%{$search}%");
            });
        });
        $query->when($genreId, function ($q) use ($genreId) {
            return $q->where('genre_id', $genreId);
        });
        $books = $query->paginate(10)->withQueryString();
        return view('admin.petugas.books.index', compact('books', 'genres'));
    }

    // ... (method create, store, show, edit, update, destroy tetap sama) ...

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
        $prefix = ($genre ? $genre->genre_code : 'GEN') . '-' . Str::upper($validated['initial_code']) . '-';


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
        $book->load('copies'); 
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
                if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                     Storage::disk('public')->delete($book->cover_image);
                 }
                $path = $request->file('cover_image')->store('covers', 'public');
                $validated['cover_image'] = $path;
            } else {
                 unset($validated['cover_image']);
            }
            
            $validated['is_textbook'] = $request->has('is_textbook');
            $book->update($validated);

            if ($request->filled('add_stock')) {
                $lastCopy = $book->copies()->orderBy('book_code', 'desc')->first(); 
                $lastNumber = 0;
                $prefix = '';

                if ($lastCopy) {
                    $parts = explode('-', $lastCopy->book_code);
                    if (count($parts) > 1) { // Pastikan ada pemisah '-'
                        $lastNumber = (int)end($parts); 
                        array_pop($parts); 
                        $prefix = implode('-', $parts) . '-'; 
                    } else {
                         // Handle jika format kode buku tidak sesuai (misal tidak ada '-')
                         throw ValidationException::withMessages(['add_stock' => 'Format kode buku terakhir tidak valid untuk penambahan otomatis.']);
                    }
                } else {
                    $genre = Genre::find($book->genre_id); 
                    $initialCode = Str::upper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $book->title), 0, 3)); 
                    $prefix = ($genre ? $genre->genre_code . '-' : 'GEN-') . $initialCode . '-';
                     if(!$genre){
                         \Log::warning("Genre not found for book ID: {$book->id} during stock addition.");
                     }
                }
                
                 if (empty(trim($prefix, '-'))) {
                     throw ValidationException::withMessages(['add_stock' => 'Gagal menentukan prefix kode buku. Pastikan buku memiliki genre atau format kode buku sebelumnya benar.']);
                 }

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

        return redirect()->route('admin.petugas.books.edit', $book)->with('success', 'Data buku berhasil diperbarui.'); 
    }

    public function destroy(Book $book)
    {
        if ($book->copies()->whereIn('status', ['dipinjam', 'pending'])->exists()) {
             return redirect()->route('admin.petugas.books.index')->with('error', 'Buku tidak dapat dihapus karena masih ada salinan yang sedang dipinjam atau dalam proses peminjaman.');
        }

        DB::transaction(function () use ($book) {
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
             $book->copies()->delete();
            $book->delete();
        });

        return redirect()->route('admin.petugas.books.index')->with('success', 'Buku dan semua salinannya berhasil dihapus.');
    }

}

