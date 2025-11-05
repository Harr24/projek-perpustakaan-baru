<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule; // <-- TAMBAHAN PENTING

class HolidayController extends Controller
{
    /**
     * Menampilkan halaman manajemen tanggal merah.
     */
    public function index(Request $request)
    {
        // Tentukan tahun yang dipilih, default-nya tahun ini
        $selectedYear = $request->input('year', Carbon::now()->year);

        // Ambil daftar tahun unik dari tabel holidays untuk filter dropdown
        $years = Holiday::selectRaw('YEAR(holiday_date) as year')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year');

        // Jika tidak ada data sama sekali, tambahkan tahun ini ke dropdown
        if ($years->isEmpty() || !$years->contains(Carbon::now()->year)) {
            $years->push(Carbon::now()->year);
            $years = $years->sortDesc();
        }

        // Ambil data tanggal merah berdasarkan tahun yang dipilih
        $holidays = Holiday::whereYear('holiday_date', $selectedYear)
                            ->orderBy('holiday_date', 'asc')
                            ->get();

        return view('admin.superadmin.holidays.index', compact('holidays', 'years', 'selectedYear'));
    }

    /**
     * Menyimpan tanggal merah baru.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'holiday_date' => 'required|date|unique:holidays,holiday_date',
            'description' => 'required|string|max:255',
        ], [
            'holiday_date.required' => 'Tanggal tidak boleh kosong.',
            'holiday_date.date' => 'Format tanggal tidak valid.',
            'holiday_date.unique' => 'Tanggal ini sudah terdaftar sebagai hari libur.',
            'description.required' => 'Keterangan tidak boleh kosong.',
        ]);

        // Buat data baru
        Holiday::create([
            'holiday_date' => $request->holiday_date,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Tanggal merah berhasil ditambahkan.');
    }

    // ==========================================================
    // --- METHOD BARU: Mengambil data untuk modal edit ---
    // ==========================================================
    /**
     * Mengambil data holiday untuk ditampilkan di modal edit.
     * Kita akan merespons dengan JSON.
     */
    public function edit(Holiday $holiday)
    {
        // Kembalikan data sebagai JSON
        return response()->json([
            'id' => $holiday->id,
            'holiday_date' => $holiday->holiday_date->format('Y-m-d'), // Format Y-m-d agar mudah dibaca input[type=date]
            'description' => $holiday->description
        ]);
    }

    // ==========================================================
    // --- METHOD BARU: Menyimpan perubahan dari modal edit ---
    // ==========================================================
    /**
     * Update data tanggal merah.
     */
    public function update(Request $request, Holiday $holiday)
    {
        // Validasi input
        $request->validate([
            'edit_holiday_date' => [
                'required',
                'date',
                // Pastikan tanggal unik, KECUALI untuk data ini sendiri
                Rule::unique('holidays', 'holiday_date')->ignore($holiday->id),
            ],
            'edit_description' => 'required|string|max:255',
        ], [
            'edit_holiday_date.required' => 'Tanggal tidak boleh kosong.',
            'edit_holiday_date.date' => 'Format tanggal tidak valid.',
            'edit_holiday_date.unique' => 'Tanggal ini sudah terdaftar sebagai hari libur.',
            'edit_description.required' => 'Keterangan tidak boleh kosong.',
        ]);

        // Update data
        $holiday->update([
            'holiday_date' => $request->edit_holiday_date,
            'description' => $request->edit_description,
        ]);

        return redirect()->back()->with('success', 'Tanggal merah berhasil diperbarui.');
    }


    /**
     * Menghapus tanggal merah.
     */
    public function destroy(Holiday $holiday)
    {
        // Hapus data
        $holiday->delete();

        return redirect()->back()->with('success', 'Tanggal merah berhasil dihapus.');
    }
}