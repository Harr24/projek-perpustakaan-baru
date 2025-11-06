<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibrarySchedule extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'day_of_week',
    ];

    /**
     * Relasi ke model User.
     */
    public function user()
    {
        // Mendefinisikan bahwa 1 jadwal dimiliki oleh 1 user
        return $this->belongsTo(User::class);
    }
}