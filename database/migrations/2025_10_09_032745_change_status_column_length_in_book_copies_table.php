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
            // Ubah kolom 'status' menjadi varchar dengan panjang 20
            // Ini akan cukup untuk status 'tersedia', 'borrowed', 'lost', dll.
            $table->string('status', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_copies', function (Blueprint $table) {
            // Kembalikan ke panjang sebelumnya jika diperlukan (opsional)
            $table->string('status', 10)->change();
        });
    }
};