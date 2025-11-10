<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // <-- Ini kode Paginator Anda

// ===========================================
// --- Import Model & Observer Anda ---
// ===========================================
use App\Models\Borrowing;
use App\Observers\BorrowingObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // (Kosong itu normal)
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kode Paginator Anda
        Paginator::useBootstrapFive();
        
        // Kode Observer Anda
        Borrowing::observe(BorrowingObserver::class);
    }
}