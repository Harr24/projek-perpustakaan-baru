<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Mengubah kolom enum untuk menambahkan 'pending'
            $table->enum('status', ['pending', 'borrowed', 'returned', 'overdue'])
                  ->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Mengembalikan ke state semula jika di-rollback
            $table->enum('status', ['borrowed', 'returned', 'overdue'])
                  ->default('borrowed')->change();
        });
    }
};