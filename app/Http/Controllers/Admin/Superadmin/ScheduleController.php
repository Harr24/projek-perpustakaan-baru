<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LibrarySchedule; 
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 

class ScheduleController extends Controller
{
    /**
     * Array helper untuk mapping angka ke nama hari.
     */
    protected $days = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
    ];

    /**
     * Menampilkan halaman utama (index) manajemen jadwal.
     */
    public function index()
    {
        $schedules = LibrarySchedule::with('user')
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('day_of_week');
            
        return view('admin.superadmin.schedules.index', [
            'schedulesByDay' => $schedules,
            'days' => $this->days
        ]);
    }

    /**
     * Menampilkan form untuk menambah jadwal baru.
     */
    public function create()
    {
        // ==========================================================
        // --- REVISI: Mengambil user dengan role 'petugas' ---
        // ==========================================================
        $staff = User::where('role', 'petugas') 
                     ->where('account_status', 'active')
                     ->orderBy('name')
                     ->get();
                     
        return view('admin.superadmin.schedules.create', [
            'staff' => $staff,
            'days' => $this->days
        ]);
    }

    /**
     * Menyimpan data jadwal baru dari form.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('library_schedules')->where(function ($query) use ($request) {
                    return $query->where('day_of_week', $request->day_of_week);
                }),
            ],
            'day_of_week' => 'required|integer|between:1,5',
        ], [
            'user_id.unique' => 'User ini sudah terjadwal di hari tersebut.'
        ]);

        LibrarySchedule::create($validated);

        return redirect()->route('admin.superadmin.schedules.index')
                         ->with('success', 'Jadwal petugas berhasil ditambahkan.');
    }

    /**
     * Menghapus data jadwal.
     */
    public function destroy(LibrarySchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.superadmin.schedules.index')
                         ->with('success', 'Jadwal petugas berhasil dihapus.');
    }
}