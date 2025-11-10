<?php

namespace App\Observers;

use App\Models\Borrowing;

class BorrowingObserver
{
    /**
     * Handle the Borrowing "created" event.
     */
    public function created(Borrowing $borrowing): void
    {
        //
    }

    /**
     * Handle the Borrowing "updated" event.
     */
    public function updated(Borrowing $borrowing): void
    {
        //
    }

    /**
     * Handle the Borrowing "deleted" event.
     * Ini akan otomatis berjalan SETIAP KALI data borrowing dihapus.
     */
    public function deleted(Borrowing $borrowing): void
    {
        // Cek apakah buku ini memiliki relasi bookCopy dan belum dikembalikan
        // Kita tidak ingin mengubah status buku yang sudah 'returned'
        if ($borrowing->status !== 'returned' && $borrowing->bookCopy) {
            
            // Jika data peminjaman dihapus,
            // ubah status salinan bukunya kembali jadi 'tersedia'
            $borrowing->bookCopy->status = 'tersedia';
            $borrowing->bookCopy->save();
        }
    }

    /**
     * Handle the Borrowing "restored" event.
     */
    public function restored(Borrowing $borrowing): void
    {
        //
    }

    /**
     * Handle the Borrowing "force deleted" event.
     */
    public function forceDeleted(Borrowing $borrowing): void
    {
        //
    }
}