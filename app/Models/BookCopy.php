<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    use HasFactory;
    // ... di dalam class BookCopy
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'book_id',
        'book_code',
        'status', // Sebaiknya sertakan status juga
    ];

    /**
     * Get the book that owns the copy.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Relasi untuk mengambil data peminjaman yang sedang AKTIF (belum dikembalikan)
    public function activeBorrowing()
    {
        return $this->hasOne(Borrowing::class, 'book_copy_id')
                    ->whereNull('returned_at') // Hanya ambil yang belum kembali
                    ->latest(); // Ambil yang paling baru
    }

    
}
