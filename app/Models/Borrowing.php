<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_copy_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status', 
        'due_date', // Sebaiknya tambahkan juga due_date jika bisa diisi
        'fine_amount', // dan field lain yang relevan
        'fine_status',
    ];
    
    // ==========================================================
    // TAMBAHAN: Properti $casts untuk mengubah string jadi Objek Tanggal
    // Ini akan memperbaiki error "Call to a member function format() on string"
    // ==========================================================
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'due_date' => 'date', // 'due_date' dari SQL kamu adalah 'date'
    ];

    // Relasi ke User (peminjam)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke salinan buku yang dipinjam
    public function bookCopy()
    {
        // Pastikan nama foreign key 'book_copy_id' sudah benar
        return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi untuk mengambil data user (petugas) yang memproses pengembalian.
     */
    public function returnedBy()
    {
        // Jika kamu punya kolom 'returned_by'
        return $this->belongsTo(User::class, 'returned_by');
    }

    // ==========================================================
    // --- TAMBAHAN BARU: Relasi ke Riwayat Pembayaran Denda ---
    // ==========================================================
    /**
     * Dapatkan semua riwayat pembayaran untuk denda ini.
     */
    public function finePayments()
    {
        return $this->hasMany(FinePayment::class);
    }
    // ==========================================================

}