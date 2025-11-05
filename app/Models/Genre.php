<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    // PASTIKAN BARIS INI ADA
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'genre_code',
    ];

    // ==========================================================
    // --- FUNGSI INI YANG HILANG (PENYEBAB ERROR HAPUS) ---
    // ==========================================================
    /**
     * Mendefinisikan relasi 'hasMany' (satu-ke-banyak) ke model Book.
     * Satu Genre bisa memiliki banyak Buku.
     */
    public function books()
    {
        // Ini akan menghubungkan Genre ke Book melalui 'genre_id'
        return $this->hasMany(Book::class);
    }
}