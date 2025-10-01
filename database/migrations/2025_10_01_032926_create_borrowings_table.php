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
            $table->timestamp('borrowed_at')->useCurrent();
            
            // PERUBAHAN DI SINI: Izinkan kolom ini untuk kosong (nullable)
            // Ini akan kita isi nanti saat proses peminjaman.
            $table->timestamp('due_at')->nullable();
            
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    // ... method down() ...


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
