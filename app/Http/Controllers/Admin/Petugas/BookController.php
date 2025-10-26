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
// Kita tidak lagi butuh use Spatie\SimpleExcel\SimpleExcelReader;

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

        $query = Book::with('genre')
            ->withCount([
                'copies as copies_count' => function ($query) {
                    $query->where('status', '!=', 'hilang');
                },
                'copies as borrowed_copies_count' => function ($query) {
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.petugas.books.create', compact('genres'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y')),
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
            $bookData = [
                'title' => $validated['title'],
                'author' => $validated['author'],
                'publication_year' => $validated['publication_year'] ?? null,
                'synopsis' => $validated['synopsis'] ?? null,
                'genre_id' => $validated['genre_id'],
                'is_textbook' => $request->has('is_textbook'),
                'stock' => $validated['stock'], // Sertakan stock di sini juga jika diperlukan DB
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
        $book->load('genre', 'copies');
        return view('admin.petugas.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $genres = Genre::orderBy('name')->get();
        $book->load('copies');
        return view('admin.petugas.books.edit', compact('book', 'genres'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y')),
            'synopsis' => 'nullable|string',
            'genre_id' => 'required|exists:genres,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_textbook' => 'nullable|boolean',
            'add_stock' => 'nullable|integer|min:1|max:100',
        ]);

        DB::transaction(function () use ($request, $validated, $book) {
            $updateData = $validated;
            $addStockAmount = $updateData['add_stock'] ?? 0; // Simpan nilai add_stock
            unset($updateData['add_stock']); // Hapus add_stock dari data update buku utama

            if ($request->hasFile('cover_image')) {
                if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                    Storage::disk('public')->delete($book->cover_image);
                }
                $path = $request->file('cover_image')->store('covers', 'public');
                $updateData['cover_image'] = $path;
            } else {
                 unset($updateData['cover_image']);
            }

            $updateData['is_textbook'] = $request->has('is_textbook');

            // Update stok di tabel books jika ada penambahan
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

                for ($i = 1; $i <= $addStockAmount; $i++) { // Gunakan $addStockAmount
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
        if ($book->copies()->whereIn('status', ['dipinjam', 'pending'])->exists()) {
            return redirect()->route('admin.petugas.books.index')->with('error', 'Buku tidak dapat dihapus karena masih ada salinan yang dipinjam/pending.');
        }

        DB::transaction(function () use ($book) {
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $book->copies()->delete(); // Hapus semua salinan terkait
            $book->delete(); // Hapus buku utama
        });

        return redirect()->route('admin.petugas.books.index')->with('success', 'Buku dan semua salinannya berhasil dihapus.');
    }

     /**
     * Remove the specified book copy from storage.
     */
    public function destroyCopy(BookCopy $copy)
    {
        if (in_array($copy->status, ['dipinjam', 'pending', 'overdue'])) {
            return redirect()->route('admin.petugas.books.edit', $copy->book_id)
                ->with('error', "Eksemplar {$copy->book_code} tidak dapat dihapus (status: {$copy->status}).");
        }

        // Update stok di tabel books sebelum menghapus copy
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

    // ==========================================================
    // METODE UNTUK FORM TAMBAH BUKU MULTI-BARIS (BULK)
    // ==========================================================

    /**
     * Menampilkan halaman form tambah buku multi-baris.
     */
    public function showCreateBulkForm()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.petugas.books.create-bulk', compact('genres'));
    }

    /**
     * Menyimpan data buku dari form multi-baris.
     */
    public function storeBulkForm(Request $request)
    {
        // 1. Validasi Input Array
        $validated = $request->validate([
            'books' => 'required|array|min:1',
            'books.*.title' => 'required|string|max:255',
            'books.*.author' => 'required|string|max:255',
            'books.*.genre_id' => 'required|exists:genres,id',
            'books.*.initial_code' => 'required|string|max:10|alpha_num',
            'books.*.stock' => 'required|integer|min:1|max:100',
            'books.*.publication_year' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),
            'books.*.synopsis' => 'nullable|string', // Validasi Sinopsis
            // 'is_textbook' divalidasi sebagai ada/tidaknya field
        ], [
            // Pesan error kustom (sama seperti sebelumnya)
            'books.*.title.required' => 'Judul buku di baris :index wajib diisi.',
            'books.*.author.required' => 'Penulis di baris :index wajib diisi.',
            'books.*.genre_id.required' => 'Genre di baris :index wajib dipilih.',
            'books.*.genre_id.exists' => 'Genre yang dipilih di baris :index tidak valid.',
            'books.*.initial_code.required' => 'Kode Awal di baris :index wajib diisi.',
            'books.*.initial_code.max' => 'Kode Awal di baris :index maksimal 10 karakter.',
            'books.*.initial_code.alpha_num' => 'Kode Awal di baris :index hanya boleh huruf dan angka.',
            'books.*.stock.required' => 'Stok di baris :index wajib diisi.',
            'books.*.stock.integer' => 'Stok di baris :index harus berupa angka.',
            'books.*.stock.min' => 'Stok di baris :index minimal 1.',
            'books.*.stock.max' => 'Stok di baris :index maksimal 100.',
            'books.*.publication_year.digits' => 'Tahun terbit di baris :index harus 4 digit.',
            'books.*.publication_year.integer' => 'Tahun terbit di baris :index harus angka.',
            'books.*.publication_year.min' => 'Tahun terbit di baris :index minimal 1900.',
            'books.*.publication_year.max' => 'Tahun terbit di baris :index maksimal tahun ini.',
            'books.*.synopsis.string' => 'Sinopsis di baris :index harus berupa teks.',
        ]);

        $allBooksData = $validated['books'];
        $errors = [];
        $genres = Genre::find(collect($allBooksData)->pluck('genre_id')->unique())->keyBy('id');

        // 2. Validasi Logika Bisnis (Duplikasi Kode Awal + Genre)
        // (Kode validasi duplikasi tetap sama)
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

        // 3. Simpan ke Database
        try {
            DB::transaction(function () use ($allBooksData, $genres, $prefixesInForm) {
                foreach ($allBooksData as $index => $bookData) {
                    $prefix = $prefixesInForm[$index] ?? null;
                     if (!$prefix) {
                         $genre = $genres->get($bookData['genre_id']);
                         $prefix = ($genre ? $genre->genre_code : 'GEN') . '-' . Str::upper($bookData['initial_code']) . '-';
                     }

                    // ===============================================
                    // PERBAIKAN: Tambahkan 'stock' ke Book::create()
                    // ===============================================
                    $newBook = Book::create([
                        'title' => $bookData['title'],
                        'author' => $bookData['author'],
                        'publication_year' => $bookData['publication_year'] ?? null,
                        'synopsis' => $bookData['synopsis'] ?? null,
                        'genre_id' => $bookData['genre_id'],
                        'is_textbook' => isset($bookData['is_textbook']) && $bookData['is_textbook'] == '1',
                        'stock' => $bookData['stock'], // <-- TAMBAHKAN INI
                        // Cover image tidak dihandle di bulk form
                    ]);
                    // ===============================================

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

} // End of BookController class

