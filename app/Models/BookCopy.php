<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'book_code',
        'status',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}