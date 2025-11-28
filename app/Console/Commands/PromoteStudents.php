<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PromoteStudents extends Command
{
    /**
     * Nama perintah artisan.
     */
    protected $signature = 'app:promote-students';

    /**
     * Deskripsi perintah.
     */
    protected $description = 'Menaikkan kelas siswa (X -> XI, XI -> XII, XII -> Lulus) secara otomatis';

    /**
     * Eksekusi logika command.
     */
    public function handle()
    {
        $this->info('Memulai proses kenaikan kelas untuk siswa...');

        // Kita gunakan Transaction agar jika ada error di tengah jalan, 
        // database tidak berantakan (semua batal atau semua sukses).
        DB::beginTransaction();

        try {
            // ---------------------------------------------------------
            // 1. PROSES KELAS XII -> LULUS (Prioritas Pertama)
            // ---------------------------------------------------------
            // Kita proses yang kelas XII dulu supaya tidak tertimpa oleh yang baru naik dari XI
            $lulusCount = User::where('role', 'siswa')
                ->where(function($q) {
                    $q->where('class', 'XII')
                      ->orWhere('class_name', 'LIKE', 'XII %'); // Support format lama
                })
                ->update([
                    'class' => 'Lulus',
                    'class_name' => 'LULUS', // Agar tampil "LULUS" di View
                    'major' => null,         // Kosongkan jurusan karena sudah lulus
                    // 'account_status' => 'graduated' // Uncomment jika Anda sudah punya status 'graduated'
                ]);

            $this->info(" -> $lulusCount siswa kelas XII berhasil diluluskan.");

            // ---------------------------------------------------------
            // 2. PROSES KELAS XI -> XII
            // ---------------------------------------------------------
            // Siswa kelas XI naik ke XII. Jurusan (major) TETAP, tidak diubah.
            $naikKe12Count = User::where('role', 'siswa')
                ->where('class', 'XI')
                ->update([
                    'class' => 'XII'
                ]);
            
            $this->info(" -> $naikKe12Count siswa kelas XI naik ke kelas XII.");

            // ---------------------------------------------------------
            // 3. PROSES KELAS X -> XI
            // ---------------------------------------------------------
            // Siswa kelas X naik ke XI. Jurusan (major) TETAP, tidak diubah.
            $naikKe11Count = User::where('role', 'siswa')
                ->where('class', 'X')
                ->update([
                    'class' => 'XI'
                ]);

            $this->info(" -> $naikKe11Count siswa kelas X naik ke kelas XI.");

            // Jika semua lancar, simpan perubahan
            DB::commit();
            
            $total = $lulusCount + $naikKe12Count + $naikKe11Count;
            $this->info("\nSUKSES! Total $total siswa telah diperbarui.");

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua perubahan
            DB::rollBack();
            $this->error("Terjadi kesalahan: " . $e->getMessage());
            return 1; // Kode error
        }

        return 0; // Kode sukses
    }
}