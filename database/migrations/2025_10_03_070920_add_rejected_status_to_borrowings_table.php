<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('borrowings', function (Blueprint $table) {
            // Ganti enum untuk menambahkan 'rejected'
            $table->enum('status', ['pending', 'borrowed', 'returned', 'overdue', 'rejected'])->default('pending')->change();
        });
    }
    public function down(): void { /* Biarkan kosong */ }
};