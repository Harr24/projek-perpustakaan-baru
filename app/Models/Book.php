<?php

namespace App\Models;

use App\Models\BookCopy;
use App\Models\Genre;
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
        'genre_id',
        'cover_image',
        'stock',
        'is_textbook', // <-- PASTIKAN BARIS INI ADA
    ];

    /**
     * Get the genre that owns the book.
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * Get all of the copies for the Book.
     */
    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }
}