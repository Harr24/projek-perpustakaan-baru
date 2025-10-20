<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage; // 1. PASTIKAN INI DI-IMPORT

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
     * Relasi baru ke LearningMaterial
     */
    public function learningMaterials()
    {
        return $this->hasMany(LearningMaterial::class);
    }

    /**
     * ==========================================================
     * 2. FUNGSI BARU DITAMBAHKAN DI SINI
     * ==========================================================
     * Accessor untuk mendapatkan URL foto profil.
     * Akan dipanggil otomatis saat kita mengakses $user->profile_photo_url
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            // Jika user punya foto profil, kembalikan URL-nya dari storage
            return Storage::url($this->profile_photo);
        }

        // Jika tidak, kembalikan URL ke gambar default/placeholder
        // Menggunakan inisial nama untuk gambar default
        return 'https://placehold.co/500x500/d9534f/ffffff?text=' . strtoupper(substr($this->name, 0, 2));
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
        'class_name',
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

