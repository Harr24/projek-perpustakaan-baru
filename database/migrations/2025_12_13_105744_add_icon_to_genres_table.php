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
        Schema::table('genres', function (Blueprint $table) {
            // Tambahkan kolom 'icon' bertipe string
            // ->nullable() artinya boleh kosong (tidak wajib upload gambar)
            // ->after('name') artinya kolom ini ditaruh urutannya setelah kolom 'name'
            $table->string('icon')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genres', function (Blueprint $table) {
            // Perintah untuk menghapus kolom icon jika migrasi di-rollback
            $table->dropColumn('icon');
        });
    }
};