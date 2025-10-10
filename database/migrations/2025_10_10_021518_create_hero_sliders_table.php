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
        Schema::create('hero_sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();         // Judul/alt text untuk gambar
            $table->string('image_path');               // Path untuk menyimpan lokasi gambar
            $table->string('link_url')->nullable();     // Jika slider ingin bisa diklik
            $table->boolean('is_active')->default(true); // Status untuk menampilkan/menyembunyikan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_sliders');
    }
};