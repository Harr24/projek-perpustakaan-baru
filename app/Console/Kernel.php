<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // ==========================================================
        // --- TAMBAHKAN JADWALMU DI SINI ---
        // ==========================================================
        
        // Jalankan command 'app:promote-students'
        // ->yearlyOn(7, 1, '01:00');
        // Artinya: Setiap tahun (yearly)
        //          pada bulan ke-7 (Juli),
        //          tanggal 1,
        //          jam 1:00 pagi (Waktu Server).
        
        $schedule->command('app:promote-students')->yearlyOn(7, 1, '01:00');
        
        // ==========================================================
        // --- BATAS TAMBAHAN ---
        // ==========================================================
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}