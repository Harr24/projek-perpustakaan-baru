<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form registrasi.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Memproses data dari form registrasi.
     */
    public function register(Request $request)
    {
        // 1. Validasi data input, termasuk file foto
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'student_card_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Wajib gambar, maks 2MB
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Simpan file foto ke storage
        $path = $request->file('student_card_photo')->store('public/student_cards');

        // 3. Buat user baru dengan status PENDING
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'student_card_photo' => $path, // Simpan path foto
            'account_status' => 'pending', // Set status awal
        ]);

        // 4. Arahkan ke halaman login dengan pesan status
        return redirect()->route('login')
                         ->with('status', 'Pendaftaran berhasil! Akun Anda sedang menunggu verifikasi oleh petugas.');
    }

    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses data dari form login.
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba lakukan autentikasi
        if (Auth::attempt($credentials)) {
            
            // Tambahan: Cek Status Akun
            if (Auth::user()->account_status !== 'active') {
                // Jika akun tidak aktif, paksa logout lagi
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Kembalikan ke login dengan pesan error spesifik
                return back()->with('error', 'Akun Anda belum aktif. Mohon tunggu verifikasi dari petugas.');
            }

            // Jika berhasil dan aktif, regenerate session dan arahkan ke dashboard
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // Jika email/password salah
        return back()->with('error', 'Login gagal! Email atau password salah.');
    }

    /**
     * Memproses logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}