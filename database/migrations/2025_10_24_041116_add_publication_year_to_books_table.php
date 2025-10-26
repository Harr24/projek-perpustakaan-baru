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
        Schema::table('books', function (Blueprint $table) {
            // Tambahkan kolom 'publication_year' setelah kolom 'genre_id'
            // Tipe data integer, 4 digit, boleh null (kosong)
            $table->year('publication_year')->nullable()->after('genre_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Hapus kolom jika migration di-rollback
            $table->dropColumn('publication_year');
        });
    }
};
