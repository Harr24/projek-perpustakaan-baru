<?php

namespace App\Models;

use App\Models\BookCopy;
use App\Models\Genre;
use App\Models\Borrowing; // Ini sudah benar, tetap ada
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// <-- TAMBAHAN: Kita import Shelf di sini, meskipun tidak wajib,
// tapi ini praktik yang baik untuk melihat relasinya.
use App\Models\Shelf;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'synopsis',
        'genre_id',
        'shelf_id', // <-- TAMBAHAN (1 dari 2): Tambahkan shelf_id ke fillable
        'cover_image',
        'stock',
        'book_type',
        'publication_year',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'book_type' => 'reguler',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'is_textbook' => 'boolean', // Komentari atau hapus jika sudah tidak ada
    ];

    /**
     * Get the genre that owns the book.
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * Get the copies for the book.
     */
    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    /**
     * Relasi untuk menghitung semua peminjaman melalui book copies.
     */
    public function borrowings()
    {
        return $this->hasManyThrough(Borrowing::class, BookCopy::class);
    }

    /**
     * ==========================================================
     * --- 🔥 TAMBAHAN BARU (2 dari 2) 🔥 ---
     * ==========================================================
     * Mendefinisikan relasi many-to-one:
     * Satu Buku (Book) hanya dimiliki oleh satu Rak (Shelf).
     */
    public function shelf()
    {
        // 'return $this->belongsTo(Shelf::class);' berarti:
        // "Model ini (Book) DIMILIKI OLEH (belongsTo) satu Model Shelf"
        return $this->belongsTo(Shelf::class);
    }
}