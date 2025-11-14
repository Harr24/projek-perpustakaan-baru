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
        // VERSI SEDERHANA: Kita hanya butuh nama rak.
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Rak (Contoh: "Rak Fiksi", "Rak Komputer")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shelves');
    }
};