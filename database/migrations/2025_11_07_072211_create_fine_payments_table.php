<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fine_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained('borrowings')->onDelete('cascade');
            $table->foreignId('processed_by_user_id')->constrained('users')->onDelete('cascade'); // ID Petugas
            $table->integer('amount_paid'); // Jumlah yang dibayar di transaksi ini
            $table->timestamps(); // Ini akan menjadi 'Tanggal Bayar'
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fine_payments');
    }
};