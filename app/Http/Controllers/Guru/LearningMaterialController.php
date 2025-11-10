<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\LearningMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningMaterialController extends Controller
{
    // Menampilkan daftar materi yang dibuat oleh guru yang sedang login
    public function index()
    {
        $materials = LearningMaterial::where('user_id', Auth::id())->latest()->paginate(10);
        return view('guru.materials.index', compact('materials'));
    }

    // Menampilkan form untuk membuat materi baru
    public function create()
    {
        return view('guru.materials.create');
    }

    // Menyimpan materi baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link_url' => 'required|url',
            'description' => 'nullable|string',
        ]);

        Auth::user()->learningMaterials()->create($request->all());

        return redirect()->route('guru.materials.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    // Fungsi 'show' yang hilang (untuk perbaikan error 500)
    public function show(LearningMaterial $material)
    {
        // ==========================================================
        // 🔥 PERBAIKAN 403: Diubah dari !== menjadi !=
        // ==========================================================
        abort_if($material->user_id != Auth::id(), 403);
        
        // Langsung arahkan ke halaman edit materi tersebut
        return redirect()->route('guru.materials.edit', $material);
    }

    // Menampilkan form untuk mengedit materi
    public function edit(LearningMaterial $material)
    {
        // ==========================================================
        // 🔥 PERBAIKAN 403: Diubah dari !== menjadi !=
        // ==========================================================
        abort_if($material->user_id != Auth::id(), 403);
        return view('guru.materials.edit', compact('material'));
    }

    // Mengupdate materi di database
    public function update(Request $request, LearningMaterial $material)
    {
        // ==========================================================
        // 🔥 PERBAIKAN 403: Diubah dari !== menjadi !=
        // ==========================================================
        abort_if($material->user_id != Auth::id(), 403);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'link_url' => 'required|url',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->all();
        // Handle checkbox 'is_active'
        $data['is_active'] = $request->has('is_active');
        
        $material->update($data);

        return redirect()->route('guru.materials.index')->with('success', 'Materi berhasil diperbarui.');
    }

    // Menghapus materi
    public function destroy(LearningMaterial $material)
    {
        // ==========================================================
        // 🔥 PERBAIKAN 403: Diubah dari !== menjadi !=
        // ==========================================================
        abort_if($material->user_id != Auth::id(), 403);
        $material->delete();
        return redirect()->route('guru.materials.index')->with('success', 'Materi berhasil dihapus.');
    }
}