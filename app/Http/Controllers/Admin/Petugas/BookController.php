<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Genre;
use App\Models\Shelf; // Import Model Shelf
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $genres = Genre::orderBy('name')->get();
        $search = $request->input('search');
        $genreId = $request->input('genre_id');

        // ==========================================================
        // --- 🔥 PERUBAHAN DI SINI (1 dari 2) 🔥 ---
        // --- Kita tambahkan 'shelf' ke eager loading ---
        // ==========================================================
        $query = Book::with('genre', 'shelf') // <-- TAMBAHAN 'shelf'
            ->withCount([
                'copies as copies_count' => function ($query) {
                    $query->where('status', '!=', 'hilang');
                },
                'copies as borrowed_copies_count' => function ($query) {
                    $query->whereIn('status', ['dipinjam', 'overdue']);
                }
            ])
            ->latest();
        // ==========================================================

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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // (Kode ini sudah benar dari langkah sebelumnya)
        $genres = Genre::orderBy('name')->get();
        $shelves = Shelf::orderBy('name')->get(); 
        return view('admin.petugas.books.create', compact('genres', 'shelves'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // (Kode ini sudah benar dari langkah sebelumnya)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y')),
            'synopsis' => 'nullable|string',
            'genre_id' => 'required|exists:genres,id',
            'shelf_id' => 'required|exists:shelves,id', 
            'initial_code' => 'required|string|max:10|alpha_num',
            'stock' => 'required|integer|min:1|max:100',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'book_type' => [
                'required',
                'string',
                Rule::in(['reguler', 'paket', 'laporan']),
            ],
        ]);

        $genre = Genre::find($validated['genre_id']);
        $prefix = ($genre ? $genre->genre_code : 'GEN') . '-' . Str::upper($validated['initial_code']) . '-';

        if (BookCopy::where('book_code', 'LIKE', $prefix . '%')->exists()) {
            throw ValidationException::withMessages([
                'initial_code' => 'Kombinasi Kode Awal dan Genre ini sudah digunakan.',
            ]);
        }

        DB::transaction(function () use ($request, $validated, $prefix) {
            $bookData = [
                'title' => $validated['title'],
                'author' => $validated['author'],
                'publication_year' => $validated['publication_year'] ?? null,
                'synopsis' => $validated['synopsis'] ?? null,
                'genre_id' => $validated['genre_id'],
                'shelf_id' => $validated['shelf_id'], 
                'book_type' => $validated['book_type'],
                'stock' => $validated['stock'],
            ];

            if ($request->hasFile('cover_image')) {
                $path = $request->file('cover_image')->store('covers', 'public');
                $bookData['cover_image'] = $path;
            }

            $book = Book::create($bookData);

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

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        // ==========================================================
        // --- 🔥 PERUBAHAN DI SINI (2 dari 2) 🔥 ---
        // --- Kita tambahkan 'shelf' ke relasi yang di-load ---
        // ==========================================================
        $book->load('genre', 'copies', 'shelf'); // <-- TAMBAHAN 'shelf'
        return view('admin.petugas.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        // (Kode ini sudah benar dari langkah sebelumnya)
        $genres = Genre::orderBy('name')->get();
        $shelves = Shelf::orderBy('name')->get();
        $book->load('copies');
        return view('admin.petugas.books.edit', compact('book', 'genres', 'shelves'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        // (Kode ini sudah benar dari langkah sebelumnya)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y')),
            'synopsis' => 'nullable|string',
            'genre_id' => 'required|exists:genres,id',
            'shelf_id' => 'required|exists:shelves,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'book_type' => [
                'required',
                'string',
                Rule::in(['reguler', 'paket', 'laporan']),
            ],
            'add_stock' => 'nullable|integer|min:1|max:100',
        ]);

        DB::transaction(function () use ($request, $validated, $book) {
            $updateData = $validated;
            $addStockAmount = $updateData['add_stock'] ?? 0;
            unset($updateData['add_stock']);

            if ($request->hasFile('cover_image')) {
                if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                    Storage::disk('public')->delete($book->cover_image);
                }
                $path = $request->file('cover_image')->store('covers', 'public');
                $updateData['cover_image'] = $path;
            } else {
                unset($updateData['cover_image']);
            }

            $updateData['book_type'] = $validated['book_type'];
            $updateData['shelf_id'] = $validated['shelf_id']; 

            if ($addStockAmount > 0) {
                $updateData['stock'] = $book->stock + $addStockAmount;
            }

            $book->update($updateData);

            if ($request->filled('add_stock')) {
                $lastCopy = $book->copies()->orderBy('book_code', 'desc')->first();
                $lastNumber = 0;
                $prefix = '';

                if ($lastCopy) {
                    $parts = explode('-', $lastCopy->book_code);
                    if (count($parts) > 1) {
                        $lastNumber = (int)end($parts);
                        array_pop($parts);
                        $prefix = implode('-', $parts) . '-';
                    } else {
                        throw ValidationException::withMessages(['add_stock' => 'Format kode buku terakhir tidak valid.']);
                    }
                } else {
                    $genre = Genre::find($book->genre_id);
                    $initialCode = Str::upper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $book->title), 0, 3));
                    $prefix = ($genre ? $genre->genre_code . '-' : 'GEN-') . $initialCode . '-';
                }

                if (empty(trim($prefix, '-'))) {
                    throw ValidationException::withMessages(['add_stock' => 'Gagal menentukan prefix kode buku.']);
                }

                for ($i = 1; $i <= $addStockAmount; $i++) {
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


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        // (Tidak ada perubahan di sini)
        if ($book->copies()->whereIn('status', ['dipinjam', 'pending'])->exists()) {
            return redirect()->route('admin.petugas.books.index')->with('error', 'Buku tidak dapat dihapus karena masih ada salinan yang dipinjam/pending.');
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

     /**
      * Remove the specified book copy from storage.
      */
    public function destroyCopy(BookCopy $copy)
    {
        // (Tidak ada perubahan di sini)
        if (in_array($copy->status, ['dipinjam', 'pending', 'overdue'])) {
            return redirect()->route('admin.petugas.books.edit', $copy->book_id)
                ->with('error', "Eksemplar {$copy->book_code} tidak dapat dihapus (status: {$copy->status}).");
        }

        $book = $copy->book;
        if ($book) {
            $book->decrement('stock');
        }

        $bookId = $copy->book_id;
        $bookCode = $copy->book_code;
        $copy->delete();

        return redirect()->route('admin.petugas.books.edit', $bookId)
            ->with('success', "Eksemplar buku {$bookCode} berhasil dihapus.");
    }

    /**
     * Set status 'hilang' copy menjadi 'tersedia'.
     */
    public function markCopyAsFound(BookCopy $copy)
    {
        // (Tidak ada perubahan di sini)
        if ($copy->status !== 'hilang') {
            return redirect()->route('admin.petugas.books.edit', $copy->book_id)
                ->with('error', "Eksemplar {$copy->book_code} tidak dalam status 'hilang'.");
        }

        DB::transaction(function () use ($copy) {
            $copy->status = 'tersedia';
            $copy->save();

            $book = $copy->book;
            if ($book) {
                $book->increment('stock');
            }
        });

        return redirect()->route('admin.petugas.books.edit', $copy->book_id)
            ->with('success', "Eksemplar {$copy->book_code} berhasil ditandai 'tersedia' dan stok telah dikembalikan.");
    }

    // ==========================================================
    // METODE UNTUK FORM TAMBAH BUKU MULTI-BARIS (BULK)
    // ==========================================================

    /**
     * Menampilkan halaman form tambah buku multi-baris.
     */
    public function showCreateBulkForm()
    {
        // (Kode ini sudah benar dari langkah sebelumnya)
        $genres = Genre::orderBy('name')->get();
        $shelves = Shelf::orderBy('name')->get();
        return view('admin.petugas.books.create-bulk', compact('genres', 'shelves'));
    }

    /**
     * Menyimpan data buku dari form multi-baris.
     */
    public function storeBulkForm(Request $request)
    {
        // (Kode ini sudah benar dari langkah sebelumnya)
        $validated = $request->validate([
            'books' => 'required|array|min:1',
            'books.*.title' => 'required|string|max:255',
            'books.*.author' => 'required|string|max:255',
            'books.*.genre_id' => 'required|exists:genres,id',
            'books.*.shelf_id' => 'required|exists:shelves,id',
            'books.*.initial_code' => 'required|string|max:10|alpha_num',
            'books.*.stock' => 'required|integer|min:1|max:100',
            'books.*.publication_year' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'books.*.synopsis' => 'nullable|string',
            'books.*.book_type' => [
                'required',
                'string',
                Rule::in(['reguler', 'paket', 'laporan']),
            ],
        ], [
            'books.*.title.required' => 'Judul buku di baris :index wajib diisi.',
            'books.*.author.required' => 'Penulis di baris :index wajib diisi.',
            'books.*.genre_id.required' => 'Genre di baris :index wajib dipilih.',
            'books.*.shelf_id.required' => 'Rak di baris :index wajib dipilih.',
            'books.*.book_type.required' => 'Tipe Buku di baris :index wajib diisi.',
            'books.*.book_type.in' => 'Tipe Buku di baris :index tidak valid.',
        ]);

        $allBooksData = $validated['books'];
        $errors = [];
        $genres = Genre::find(collect($allBooksData)->pluck('genre_id')->unique())->keyBy('id');

        $existingPrefixes = BookCopy::pluck('book_code')->map(function($code) {
             $parts = explode('-', $code);
             if (count($parts) > 1) {
                 array_pop($parts);
                 return implode('-', $parts) . '-';
             }
             return null;
        })->filter()->unique()->toArray();

        $prefixesInForm = [];

        foreach ($allBooksData as $index => $bookData) {
             $genre = $genres->get($bookData['genre_id']);
             if ($genre) {
                 $prefix = $genre->genre_code . '-' . Str::upper($bookData['initial_code']) . '-';
                 if (in_array($prefix, $existingPrefixes)) {
                     $errors["books.{$index}.initial_code"] = "Kombinasi Kode Awal + Genre di baris " . ($index + 1) . " sudah ada di database.";
                 }
                 elseif (in_array($prefix, $prefixesInForm)) {
                     $errors["books.{$index}.initial_code"] = "Kombinasi Kode Awal + Genre di baris " . ($index + 1) . " duplikat dengan baris lain di form ini.";
                 } else {
                     $prefixesInForm[] = $prefix;
                 }
             } else {
                 $errors["books.{$index}.genre_id"] = "Genre di baris " . ($index + 1) . " tidak ditemukan.";
             }
        }

        if (!empty($errors)) {
             return back()->withErrors($errors)->withInput();
        }

        try {
            DB::transaction(function () use ($allBooksData, $genres, $prefixesInForm) {
                foreach ($allBooksData as $index => $bookData) {
                    $prefix = $prefixesInForm[$index] ?? null;
                    if (!$prefix) {
                         $genre = $genres->get($bookData['genre_id']);
                         $prefix = ($genre ? $genre->genre_code : 'GEN') . '-' . Str::upper($bookData['initial_code']) . '-';
                    }

                    $newBook = Book::create([
                        'title' => $bookData['title'],
                        'author' => $bookData['author'],
                        'publication_year' => $bookData['publication_year'] ?? null,
                        'synopsis' => $bookData['synopsis'] ?? null,
                        'genre_id' => $bookData['genre_id'],
                        'shelf_id' => $bookData['shelf_id'],
                        'book_type' => $bookData['book_type'],
                        'stock' => $bookData['stock'],
                    ]);

                    for ($i = 1; $i <= $bookData['stock']; $i++) {
                        $copyNumber = str_pad($i, 3, '0', STR_PAD_LEFT);
                        BookCopy::create([
                            'book_id' => $newBook->id,
                            'book_code' => $prefix . $copyNumber,
                            'status' => 'tersedia'
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
             \Log::error('Error saving bulk books: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withErrors(['general' => 'Terjadi kesalahan sistem saat menyimpan data. Error: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.petugas.books.index')->with('success', count($allBooksData) . ' buku berhasil ditambahkan.');
    }
}