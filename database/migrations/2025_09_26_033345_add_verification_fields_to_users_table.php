<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom untuk menyimpan path file foto kartu pelajar
            $table->string('student_card_photo')->nullable()->after('remember_token');
            
            // Kolom untuk status akun
            $table->enum('account_status', ['pending', 'active', 'rejected'])->default('pending')->after('student_card_photo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kedua kolom jika migrasi di-rollback
            $table->dropColumn(['student_card_photo', 'account_status']);
        });
    }
};