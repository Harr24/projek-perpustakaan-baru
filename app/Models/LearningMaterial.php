<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // <-- 1. TAMBAHKAN IMPORT INI

class LearningMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'link_url',
        'is_active',
    ];

    /**
     * Mendapatkan data guru yang membuat materi ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==========================================================
    // --- TAMBAHAN BARU: Accessor untuk Thumbnail ---
    // ==========================================================
    /**
     * Membuat atribut 'thumbnail_url' secara otomatis.
     * Kita bisa memanggilnya di view dengan: $material->thumbnail_url
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $url = $this->link_url;
                $videoId = null;

                // 1. Cek jika ini link youtube.com
                if (str_contains($url, 'youtube.com/watch?v=')) {
                    parse_str(parse_url($url, PHP_URL_QUERY), $query);
                    $videoId = $query['v'] ?? null;
                
                // 2. Cek jika ini link youtu.be (link pendek)
                } elseif (str_contains($url, 'youtu.be/')) {
                    // Ambil path (cth: /VIDEO_ID) dan hapus '/'
                    $videoId = trim(parse_url($url, PHP_URL_PATH), '/');
                }

                // 3. Jika kita dapat ID, buat URL thumbnail
                if ($videoId) {
                    // mqdefault.jpg (320x180)
                    // hqdefault.jpg (480x360) - Kualitas tinggi
                    // maxresdefault.jpg (1920x1080) - Kualitas terbaik (tapi kadang tidak ada)
                    return 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg';
                }

                // 4. Jika bukan link YouTube, tampilkan gambar placeholder
                // PENTING: Pastikan Anda punya gambar ini di public/images/
                return asset('images/default-material.png'); 
            },
        );
    }
}