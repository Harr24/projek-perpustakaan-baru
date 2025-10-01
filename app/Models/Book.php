<?php

namespace App\Models;

// WAJIB ADA: Import model-model yang berelasi
use App\Models\Genre;
use App\Models\BookCopy;
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
        'book_code',
        'stock',
    ];

    /**
     * Get the genre that owns the book.
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * WAJIB ADA: Fungsi ini yang hilang dari kode Anda.
     * Get all of the copies for the Book.
     */
    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }
}