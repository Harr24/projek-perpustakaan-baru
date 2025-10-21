<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_copy_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status', // <-- TAMBAHKAN INI
    ];
    

    // Relasi ke User (peminjam)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke salinan buku yang dipinjam
    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi untuk mengambil data user (petugas) yang memproses pengembalian.
     */
    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }
}