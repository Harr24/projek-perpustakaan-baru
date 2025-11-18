<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // <-- 1. Import model User-mu
use Illuminate\Support\Facades\DB; // <-- 2. Import DB facade untuk raw query

class PromoteStudents extends Command
{
    /**
     * Nama dan signature dari Artisan command.
     * Inilah yang akan kita ketik di terminal.
     *
     * @var string
     */
    protected $signature = 'app:promote-students'; // <-- Nama perintahnya

    /**
     * Deskripsi dari Artisan command.
     *
     * @var string
     */
    protected $description = 'Menaikkan kelas siswa (X -> XI, XI -> XII, XII -> Lulus) secara otomatis';

    /**
     * Eksekusi logika command.
     */
    public function handle()
    {
        // 3. Tampilkan pesan info di terminal saat command dimulai
        $this->info('Memulai proses kenaikan kelas untuk siswa...');

        // 4. Kita ambil data dari database-mu (users)
        // Kita hanya ingin update user dengan role 'siswa'
        // dan yang kelasnya masih 'X', 'XI', atau 'XII'.
        // Kita tidak mau mengubah yang sudah 'Lulus' atau role 'guru'.
        
        $updatedCount = User::where('role', 'siswa')
            ->whereIn('class', ['X', 'XI', 'XII'])
            ->update([
                // 5. Ini adalah inti logikanya.
                // Kita pakai DB::raw() untuk menjalankan logic SQL "CASE"
                // Ini jauh lebih cepat daripada mengambil datanya satu per satu.
                'class' => DB::raw("CASE 
                                    WHEN class = 'XII' THEN 'Lulus'
                                    WHEN class = 'XI'  THEN 'XII'
                                    WHEN class = 'X'   THEN 'XI'
                                    ELSE class 
                                END"),
                
                // 6. (OPSIONAL)
                // Saat siswa sudah 'Lulus', kita mungkin ingin mengosongkan
                // data 'major' (jurusan) mereka.
                'major' => DB::raw("CASE 
                                    WHEN class = 'XII' THEN NULL 
                                    ELSE major 
                                END")
            ]);

        // 7. Beri laporan di terminal berapa user yang berhasil di-update.
        if ($updatedCount > 0) {
            $this->info("Proses kenaikan kelas selesai. Berhasil memperbarui $updatedCount siswa.");
        } else {
            $this->info('Tidak ada siswa yang perlu dinaikkan kelas saat ini.');
        }

        return 0; // 0 artinya command sukses
    }
}