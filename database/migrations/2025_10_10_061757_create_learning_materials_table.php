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
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID Guru yang membuat
            $table->string('title'); // Judul materi, misal: "Video Pembahasan Bab 1"
            $table->text('description')->nullable(); // Deskripsi singkat
            $table->string('link_url'); // URL ke materi (YouTube, Google Drive, dll.)
            $table->boolean('is_active')->default(true); // Untuk menampilkan/menyembunyikan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_materials');
    }
};