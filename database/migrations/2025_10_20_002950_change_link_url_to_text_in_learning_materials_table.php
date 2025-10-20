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
        Schema::table('learning_materials', function (Blueprint $table) {
            // Mengubah tipe kolom link_url menjadi TEXT
            $table->text('link_url')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('learning_materials', function (Blueprint $table) {
            // Mengembalikan tipe kolom menjadi VARCHAR(255) jika migrasi di-rollback
            $table->string('link_url', 255)->change();
        });
    }
};