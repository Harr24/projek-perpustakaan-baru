<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Menambah kolom baru setelah 'fine_amount'
            // Ini akan menyimpan total yang sudah dibayar
            $table->integer('fine_paid')->default(0)->after('fine_amount');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn('fine_paid');
        });
    }
};