<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_copy_id')->constrained('book_copies')->onDelete('cascade');
            
            // Kolom ini bagus, kita pertahankan ->useCurrent()
            $table->timestamp('borrowed_at')->useCurrent();
            
            // ===============================================
            // PERUBAHAN DI SINI: tambahkan ->nullable()
            // ===============================================
            $table->timestamp('due_at')->nullable();
            
            // Tanggal aktual pengembalian, boleh null karena awalnya belum dikembalikan
            $table->timestamp('returned_at')->nullable();
            
            // Kolom status untuk melacak kondisi peminjaman
            $table->enum('status', ['borrowed', 'returned', 'overdue'])->default('borrowed');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};