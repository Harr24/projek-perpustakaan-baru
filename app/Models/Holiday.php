<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'holidays';

    /**
     * Kolom yang bisa diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'holiday_date',
        'description',
    ];

    /**
     * Tipe data cast untuk kolom tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'holiday_date' => 'date', // Otomatis cast ke objek Carbon
    ];
}