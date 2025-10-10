<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    /**
     * Mendapatkan semua record peminjaman milik user ini.
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * ==========================================================
     * PERBAIKAN: Tambahkan relasi baru ke LearningMaterial
     * Satu User (Guru) bisa memiliki banyak materi pembelajaran.
     * ==========================================================
     */
    public function learningMaterials()
    {
        return $this->hasMany(LearningMaterial::class);
    }

    /**
     * Kolom yang boleh diisi mass assignment
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'account_status',
        'profile_photo',
        'student_card_photo',
        'nis',
        'class',
        'major',
        'phone_number',
        'subject',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

