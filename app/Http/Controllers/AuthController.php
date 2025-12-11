<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Major; 

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form registrasi.
     */
    public function showRegisterForm()
    {
        $majors = Major::orderBy('name', 'asc')->get();
        return view('auth.register', compact('majors'));
    }

    /**
     * Memproses data dari form registrasi.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            
            // ==========================================================
            // --- UPDATE VALIDASI NISN (Hanya Angka) ---
            // ==========================================================
            // 'numeric': Wajib angka
            // 'digits_between': Minimal 5 digit, Maksimal 20 digit (sesuaikan kebutuhan)
            'nis' => ['required', 'numeric', 'digits_between:5,20', 'unique:users,nis'],
            // ==========================================================

            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            'class' => ['required', 'in:X,XI,XII'], 
            'major' => ['required', 'string', 'max:255', 'exists:majors,name'], 

            // ==========================================================
            // --- UPDATE VALIDASI WHATSAPP (Hanya Angka) ---
            // ==========================================================
            // 'numeric': Wajib angka
            // 'digits_between': Minimal 10 digit (standar nomor HP), Maksimal 15
            'phone_number' => ['required', 'numeric', 'digits_between:10,15', 'unique:users,phone_number'],
            // ==========================================================

            'student_card_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            // Custom Error Message (Opsional, agar pesan lebih jelas)
            'nis.numeric' => 'NISN harus berupa angka.',
            'phone_number.numeric' => 'Nomor WhatsApp harus berupa angka.',
            'digits_between' => 'Panjang :attribute harus antara :min sampai :max digit.',
        ]);


        $path = $request->file('student_card_photo')->store('student-cards', 'public');

        User::create([
            'name' => $request->name,
            'nis' => $request->nis,
            'email' => $request->email,
            'class' => $request->class,
            'major' => $request->major,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'student_card_photo' => $path,
            'role' => 'siswa',
            'account_status' => 'pending',
        ]);

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