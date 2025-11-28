<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrowing_id',
        'processed_by_user_id',
        'amount_paid',
    ];

    /**
     * Relasi ke Peminjaman (Borrowing)
     */
    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    /**
     * Relasi ke Petugas (Versi 1: Sesuai Controller lama/View lama)
     * INI YANG DICARI OLEH ERROR ANDA
     */
    public function processor()
    {
        // Kita beri tahu Laravel bahwa foreign key-nya adalah 'processed_by_user_id'
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    /**
     * Relasi ke Petugas (Versi 2: Nama yang lebih rapi)
     * Biarkan ini tetap ada untuk kompatibilitas
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
}