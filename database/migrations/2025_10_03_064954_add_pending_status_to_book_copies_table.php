<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('book_copies', function (Blueprint $table) {
            // Mengubah kolom enum untuk menambahkan status 'pending'
            $table->enum('status', ['tersedia', 'pending', 'dipinjam', 'hilang'])
                  ->default('tersedia')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_copies', function (Blueprint $table) {
            // Mengembalikan ke state semula jika di-rollback
            $table->enum('status', ['tersedia', 'dipinjam', 'hilang'])
                  ->default('tersedia')->change();
        });
    }
};