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
            // Tambahkan kolom untuk menyimpan tanggal jatuh tempo
            // diletakkan setelah kolom 'approved_by' untuk kerapian
            $table->date('due_date')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn('due_date');
        });
    }
};

