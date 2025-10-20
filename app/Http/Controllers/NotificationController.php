<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Menandai semua notifikasi yang belum dibaca milik pengguna sebagai sudah dibaca.
     */
    public function markAsRead()
    {
        // Pastikan pengguna sudah login
        if (Auth::check()) {
            Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    }
}