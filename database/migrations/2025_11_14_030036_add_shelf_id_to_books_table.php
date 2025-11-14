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
        // Memodifikasi tabel 'books' yang sudah ada
        Schema::table('books', function (Blueprint $table) {
            
            // Menambahkan kolom 'shelf_id' sebagai foreign key
            // Dibuat 'nullable' agar buku-buku lama Anda tidak error
            // Ditempatkan 'after' 'genre_id' agar rapi di database
            $table->unsignedBigInteger('shelf_id')->nullable()->after('genre_id');

            // Menambahkan constraint foreign key
            // onDelete('set null') berarti: 
            // "Jika sebuah rak dihapus, jangan hapus bukunya,
            //  cukup set shelf_id buku ini menjadi NULL."
            // Ini jauh lebih aman daripada 'onDelete('cascade')'
            $table->foreign('shelf_id')
                  ->references('id')
                  ->on('shelves')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['shelf_id']);
            // Hapus kolomnya
            $table->dropColumn('shelf_id');
        });
    }
};