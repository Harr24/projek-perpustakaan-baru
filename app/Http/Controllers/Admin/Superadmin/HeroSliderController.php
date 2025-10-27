<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // DIUBAH: Mengurutkan berdasarkan 'order' dulu, baru tanggal
        $sliders = HeroSlider::orderBy('order')->orderBy('created_at', 'desc')->get();
        return view('admin.superadmin.sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.superadmin.sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ==========================================================
        // DIUBAH: Validasi ditambahkan untuk field baru
        // ==========================================================
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string', // <-- BARU
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|url',
            'is_active' => 'required|boolean', // <-- DIUBAH (lebih baik)
            'order' => 'required|integer',     // <-- BARU
        ]);
        // ==========================================================

        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('sliders', 'public');
            $validatedData['image_path'] = $path;
        }
        
        // $validatedData sudah mencakup semua field
        HeroSlider::create($validatedData);

        return redirect()->route('admin.superadmin.sliders.index')
                         ->with('success', 'Hero Slider berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HeroSlider $slider)
    {
        // Biasanya tidak digunakan, redirect ke edit
        return redirect()->route('admin.superadmin.sliders.edit', $slider);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HeroSlider $slider)
    {
        return view('admin.superadmin.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HeroSlider $slider)
    {
        // ==========================================================
        // DIUBAH: Validasi ditambahkan untuk field baru
        // ==========================================================
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string', // <-- BARU
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Opsional saat update
            'link_url' => 'nullable|url',
            'is_active' => 'required|boolean', // <-- DIUBAH (lebih baik)
            'order' => 'required|integer',     // <-- BARU
        ]);
        // ==========================================================
        
        if ($request->hasFile('image_path')) {
            // Hapus gambar lama jika ada
            if ($slider->image_path) {
                Storage::disk('public')->delete($slider->image_path);
            }
            // Simpan gambar baru
            $path = $request->file('image_path')->store('sliders', 'public');
            $validatedData['image_path'] = $path;
        }

        $slider->update($validatedData);

        return redirect()->route('admin.superadmin.sliders.index')
                         ->with('success', 'Hero Slider berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HeroSlider $slider)
    {
        // Hapus gambar dari storage
        if ($slider->image_path) {
            Storage::disk('public')->delete($slider->image_path);
        }

        $slider->delete();

        return redirect()->route('admin.superadmin.sliders.index')
                         ->with('success', 'Hero Slider berhasil dihapus.');
    }
}