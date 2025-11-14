<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Kita hanya perlu 'name'
    ];

    /**
     * Mendefinisikan relasi one-to-many:
     * Satu Rak (Shelf) bisa memiliki banyak Buku (Book).
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}