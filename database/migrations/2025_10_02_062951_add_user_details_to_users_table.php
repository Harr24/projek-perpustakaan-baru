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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan semua kolom yang ada di model tapi belum ada di database
            // Dibuat nullable() karena tidak semua user (misal: superadmin) punya data ini.
            $table->string('nis')->nullable();
            $table->string('class')->nullable();
            $table->string('major')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('subject')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Perintah untuk menghapus semua kolom ini jika migrasi di-rollback
            $table->dropColumn([
                'nis', 
                'class', 
                'major', 
                'phone_number', 
                'subject'
            ]);
        });
    }
};