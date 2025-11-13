<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Major; // Ini sudah benar Anda tambahkan

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form registrasi.
     */
    public function showRegisterForm()
    {
        // ==========================================================
        // --- PERUBAHAN 1: Mengambil data Jurusan dari DB ---
        // ==========================================================
        // Ambil semua jurusan dari database, urutkan berdasarkan nama
        $majors = Major::orderBy('name', 'asc')->get();

        // Kirim data $majors ke view menggunakan 'compact'
        return view('auth.register', compact('majors'));
        // ==========================================================
    }

    /**
     * Memproses data dari form registrasi.
     */
    public function register(Request $request)
    {
        // ==========================================================
        // --- PERUBAHAN 2: Memperbarui Aturan Validasi ---
        // ==========================================================
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:20', 'unique:users,nis'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            // 'class_name' => ['required', 'string', 'max:50'], // <-- HAPUS ATURAN LAMA

            // ATURAN BARU UNTUK KELAS DAN JURUSAN
            'class' => ['required', 'in:X,XI,XII'], // Pastikan hanya X, XI, atau XII
            'major' => ['required', 'string', 'max:255', 'exists:majors,name'], // Pastikan jurusan ada di tabel majors

            'phone_number' => ['required', 'string', 'max:15', 'unique:users,phone_number'],
            'student_card_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        // ==========================================================


        // Simpan file foto ke disk 'public' di dalam folder 'student_cards'
        $path = $request->file('student_card_photo')->store('student-cards', 'public');


        // ==========================================================
        // --- PERUBAHAN 3: Memperbarui Data yang Disimpan ---
        // ==========================================================
        User::create([
            'name' => $request->name,
            'nis' => $request->nis,
            'email' => $request->email,

            // 'class_name' => $request->class_name, // <-- HAPUS DATA LAMA

            // DATA BARU YANG DISIMPAN
            'class' => $request->class,
            'major' => $request->major,

            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'student_card_photo' => $path,
            'role' => 'siswa',
            'account_status' => 'pending',
        ]);
        // ==========================================================

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

            // Logika ini sudah bagus, akun 'pending' tidak bisa login
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