<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- PASTIKAN INI ADA

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom baru 'book_type' dengan default 'reguler'
        Schema::table('books', function (Blueprint $table) {
            $table->string('book_type', 20)->default('reguler')->after('genre_id');
        });

        // 2. MIGRASI DATA:
        //    Temukan semua buku yang DULU-nya adalah 'is_textbook = true' (Buku Paket),
        //    dan ubah 'book_type' mereka menjadi 'paket_7_hari'.
        //    Buku yang 'is_textbook = false' sudah otomatis menjadi 'reguler' berkat 'default()' di atas.
        DB::table('books')->where('is_textbook', true)->update(['book_type' => 'paket_7_hari']);

        // 3. Hapus kolom 'is_textbook' yang lama
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('is_textbook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Tambahkan kembali kolom 'is_textbook'
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('is_textbook')->default(false)->after('genre_id');
        });

        // 2. MIGRASI DATA MUNDUR:
        //    Konversi 'paket_7_hari' DAN 'laporan' kembali menjadi 'is_textbook = true'
        DB::table('books')->where('book_type', 'paket_7_hari')->update(['is_textbook' => true]);
        DB::table('books')->where('book_type', 'laporan')->update(['is_textbook' => true]);
        // Buku 'reguler' akan otomatis menjadi 'is_textbook = false' berkat 'default()' di atas.

        // 3. Hapus kolom 'book_type'
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('book_type');
        });
    }
};

