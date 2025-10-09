<?php

// ==========================================================
// KESALAHAN ADA DI SINI SEBELUMNYA. SEKARANG SUDAH BENAR!
namespace App\Models;
// ==========================================================

use App\Models\BookCopy;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'genre_id',
        'cover_image',
        'stock',
        'is_textbook',
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    /**
     * Relasi ini sekarang sudah benar dan akan otomatis mencari
     * foreign key 'book_id' di tabel book_copies.
     */
    public function copies()
    {
        return $this->hasMany(BookCopy::class); 
    }
}