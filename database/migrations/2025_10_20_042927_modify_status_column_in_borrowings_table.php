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
        Schema::table('borrowings', function (Blueprint $table) {
            // Mengubah kolom 'status' menjadi string yang lebih fleksibel
            // dan menetapkan default baru yaitu 'pending'
            $table->string('status', 20)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Kode untuk mengembalikan jika diperlukan (opsional)
            $table->enum('status', ['borrowed', 'returned', 'overdue'])->default('borrowed')->change();
        });
    }
};

