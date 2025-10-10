<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}