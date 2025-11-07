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
     * Dapatkan data petugas yang memproses pembayaran.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    /**
     * Dapatkan data peminjaman (denda) terkait.
     */
    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }
}