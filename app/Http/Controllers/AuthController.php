<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
        // ==========================================================
        // PERUBAHAN 1: Tambahkan 'nis' ke dalam validasi
        // ==========================================================
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:20', 'unique:users,nis'], // <-- ATURAN BARU
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'class_name' => ['required', 'string', 'max:50'],
            'student_card_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Simpan file foto ke disk 'public' di dalam folder 'student_cards'
        $path = $request->file('student_card_photo')->store('student_cards', 'public');

        // ==========================================================
        // PERUBAHAN 2: Tambahkan 'nis' saat membuat user baru
        // ==========================================================
        User::create([
            'name' => $request->name,
            'nis' => $request->nis, // <-- DATA BARU DISIMPAN
            'email' => $request->email,
            'class_name' => $request->class_name,
            'password' => Hash::make($request->password),
            'student_card_photo' => $path,
            'role' => 'siswa',
            'account_status' => 'pending',
        ]);

        // Arahkan ke halaman sukses
        return redirect()->route('register.success');
    }

    /**
     * Menampilkan halaman sukses setelah registrasi.
     */
    public function registrationSuccess()
    {
        return view('auth.registration-success');
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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            
            if (Auth::user()->account_status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->with('error', 'Akun Anda belum aktif. Mohon tunggu verifikasi dari petugas.');
            }

            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

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