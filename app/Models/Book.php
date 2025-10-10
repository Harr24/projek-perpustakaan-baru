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
        'synopsis', // Ditambahkan
        'genre_id',
        'cover_image',
        'stock',
        'is_textbook',
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
}