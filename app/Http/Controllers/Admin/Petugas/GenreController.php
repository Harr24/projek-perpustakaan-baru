<?php

namespace App\Http\Controllers\Admin\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // <--- WAJIB: Untuk hapus/cek gambar

class GenreController extends Controller
{
    public function index()
    {
        // Diurutkan berdasarkan kode genre sesuai request Anda
        $genres = Genre::oldest('genre_code')->get(); 
        return view('admin.petugas.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.petugas.genres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:genres,name',
            // Validasi kode manual (max 5 karakter)
            'genre_code' => 'required|string|max:5|unique:genres,genre_code', 
            // Validasi gambar icon (opsional)
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048', 
        ]);

        $data = [
            'name' => $request->name,
            'genre_code' => $request->genre_code,
        ];

        // ==========================================================
        // --- LOGIKA UPLOAD ICON (BARU) ---
        // ==========================================================
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('genre-icons', 'public');
            $data['icon'] = $path;
        }
        // ==========================================================

        Genre::create($data);

        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre baru berhasil ditambahkan.');
    }

    public function edit(Genre $genre)
    {
        return view('admin.petugas.genres.edit', compact('genre'));
    }

    public function update(Request $request, Genre $genre)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('genres')->ignore($genre->id),
            ],
            'genre_code' => [
                'required', 'string', 'max:5',
                Rule::unique('genres')->ignore($genre->id),
            ],
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'genre_code' => $request->genre_code,
        ];

        // ==========================================================
        // --- LOGIKA GANTI ICON (BARU) ---
        // ==========================================================
        if ($request->hasFile('icon')) {
            // 1. Hapus icon lama jika ada
            if ($genre->icon && Storage::disk('public')->exists($genre->icon)) {
                Storage::disk('public')->delete($genre->icon);
            }
            // 2. Upload icon baru
            $path = $request->file('icon')->store('genre-icons', 'public');
            $data['icon'] = $path;
        }
        // ==========================================================

        $genre->update($data);

        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre berhasil diperbarui.');
    }

    public function destroy(Genre $genre)
    {
        // 1. Cek relasi buku (Sesuai request Anda)
        if ($genre->books()->exists()) {
             return redirect()->route('admin.petugas.genres.index')
                              ->with('error', 'Gagal! Genre ini masih digunakan oleh beberapa buku.');
        }

        // 2. Hapus file icon dari penyimpanan sebelum hapus data
        if ($genre->icon && Storage::disk('public')->exists($genre->icon)) {
            Storage::disk('public')->delete($genre->icon);
        }

        // 3. Hapus data dari database
        $genre->delete();
        
        return redirect()->route('admin.petugas.genres.index')
                         ->with('success', 'Genre berhasil dihapus.');
    }
}