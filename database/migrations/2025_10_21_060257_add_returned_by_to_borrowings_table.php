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
            // Menambahkan kolom untuk menyimpan ID petugas yang memproses pengembalian
            $table->foreignId('returned_by')->nullable()->constrained('users')->after('returned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['returned_by']);
            // Hapus kolomnya
            $table->dropColumn('returned_by');
        });
    }
};