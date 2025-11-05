<?php

namespace App\Models;

use App\Models\BookCopy;
use App\Models\Genre;
use App\Models\Borrowing; // Ini sudah benar, tetap ada
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'cover_image',
        'stock', 
        // 'is_textbook', // <-- DIHAPUS DARI FILLABLE
        'book_type',     // <-- DITAMBAHKAN
        'publication_year',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'book_type' => 'reguler', // <-- TAMBAHAN: Atur 'reguler' sebagai default
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Kita biarkan cast ini untuk menangani data lama di database
        'is_textbook' => 'boolean', 
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
     * ==========================================================
     * FUNGSI LAMA ANDA - INI TETAP ADA
     * ==========================================================
     * Relasi untuk menghitung semua peminjaman melalui book copies.
     */
    public function borrowings()
    {
        return $this->hasManyThrough(Borrowing::class, BookCopy::class);
    }
}
