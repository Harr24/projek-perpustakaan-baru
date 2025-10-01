<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi mass assignment
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'account_status',
        'profile_photo', // <-- Ditambahkan
        'student_card_photo', // <-- Ditambahkan
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