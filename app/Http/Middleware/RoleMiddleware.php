<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Loop melalui setiap role yang diizinkan (misal: 'petugas', 'superadmin')
        foreach ($roles as $role) {
            // Jika role user cocok dengan role yang diizinkan, lanjutkan request
            if ($user->role == $role) {
                return $next($request);
            }
        }

        // Jika tidak ada role yang cocok, tolak akses
        abort(403, 'AKSES DITOLAK. ANDA TIDAK MEMILIKI HAK AKSES.');
    }
}