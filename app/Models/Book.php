<?php

namespace App\Models;

use App\Models\BookCopy;
use App\Models\Genre;
use App\Models\Borrowing; // <-- 1. TAMBAHKAN BARIS INI
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
        'is_textbook',
        'publication_year',
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
     * 2. TAMBAHKAN FUNGSI BARU INI
     * ==========================================================
     * Relasi untuk menghitung semua peminjaman melalui book copies.
     */
    public function borrowings()
    {
        return $this->hasManyThrough(Borrowing::class, BookCopy::class);
    }
}