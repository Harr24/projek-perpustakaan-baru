<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'genre_code',
        'icon', // <-- TAMBAHAN: Agar kolom icon bisa diisi
    ];

    /**
     * Mendefinisikan relasi 'hasMany' (satu-ke-banyak) ke model Book.
     * Satu Genre bisa memiliki banyak Buku.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}