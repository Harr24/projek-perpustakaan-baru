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
            // Tambahkan kolom untuk mencatat waktu persetujuan
            $table->timestamp('approved_at')->nullable()->after('status');
            
            // Tambahkan kolom untuk mencatat siapa petugas yang menyetujui
            // onDelete('set null') berarti jika petugas dihapus, catatan ini tidak ikut terhapus
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu sebelum menghapus kolom
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_at', 'approved_by']);
        });
    }
};
