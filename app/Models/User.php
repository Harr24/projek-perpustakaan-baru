<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable; 
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory; 
  
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
     * Mendapatkan semua notifikasi milik user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest(); 
    }

    /**
     * Accessor untuk mendapatkan URL foto profil.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return Storage::url($this->profile_photo);
        }

        return 'https://placehold.co/500x500/d9534f/ffffff?text=' . strtoupper(substr($this->name, 0, 2));
    }

    /**
     * =========================================================================
     * 🔥 TAMBAHAN BARU: AKSES PINTAR UNTUK KELAS / MAPEL 🔥
     * =========================================================================
     * Fungsi ini otomatis mengecek:
     * 1. Jika Guru -> Ambil Mapel
     * 2. Jika Siswa Lama -> Ambil class_name
     * 3. Jika Siswa Baru -> Ambil gabungan class + major
     * Panggil di View dengan: $user->class_info
     */
    public function getClassInfoAttribute()
    {
        // 1. Jika Role-nya GURU, kembalikan Mata Pelajaran
        if ($this->role === 'guru') {
            return $this->subject ?? '-';
        }

        // 2. Jika Role-nya SISWA
        
        // Cek apakah kolom 'class_name' (format lama) ada isinya di database?
        if (!empty($this->class_name)) {
            return $this->class_name;
        }

        // Jika tidak, cek gabungan kolom 'class' dan 'major' (format baru)
        if (!empty($this->class) || !empty($this->major)) {
            return trim(($this->class ?? '') . ' ' . ($this->major ?? ''));
        }

        // Jika kosong semua
        return '-';
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
        // 'class_name', // Biarkan ini tetap dikomen agar input baru masuk ke 'class' & 'major'
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