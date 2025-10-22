<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // <-- Import Paginator

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ==========================================================
        // PENAMBAHAN: Mengatur Paginator default ke Bootstrap 5
        // ==========================================================
        Paginator::useBootstrapFive();
        // Anda juga bisa menggunakan useBootstrapFour() jika menggunakan BS4
        // ==========================================================
    }
}
