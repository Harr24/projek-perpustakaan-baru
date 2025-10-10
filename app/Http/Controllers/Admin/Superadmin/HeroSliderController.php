<?php

namespace App\Http\Controllers\Admin\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Tambahkan ini

class HeroSliderController extends Controller
{
    /**
     * Menampilkan daftar semua slider.
     */
    public function index()
    {
        $sliders = HeroSlider::latest()->get();
        return view('admin.superadmin.sliders.index', compact('sliders'));
    }

    /**
     * Menampilkan form untuk menambah slider baru.
     */
    public function create()
    {
        return view('admin.superadmin.sliders.create');
    }

    /**
     * Menyimpan slider baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url',
        ]);

        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('sliders', 'public');
            $validated['image_path'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        HeroSlider::create($validated);

        return redirect()->route('admin.superadmin.sliders.index')
                         ->with('success', 'Slider baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit slider.
     * Menggunakan Route-Model Binding untuk otomatis mengambil data slider.
     */
    public function edit(HeroSlider $slider)
    {
        return view('admin.superadmin.sliders.edit', compact('slider'));
    }

    /**
     * Memperbarui data slider di database.
     */
    public function update(Request $request, HeroSlider $slider)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Nullable karena gambar tidak wajib diubah
            'link_url' => 'nullable|url',
        ]);

        if ($request->hasFile('image_path')) {
            // Hapus gambar lama sebelum upload yang baru
            if ($slider->image_path) {
                Storage::disk('public')->delete($slider->image_path);
            }
            // Simpan gambar baru
            $path = $request->file('image_path')->store('sliders', 'public');
            $validated['image_path'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');
        $slider->update($validated);

        return redirect()->route('admin.superadmin.sliders.index')
                         ->with('success', 'Slider berhasil diperbarui.');
    }

    /**
     * Menghapus slider dari database.
     */
    public function destroy(HeroSlider $slider)
    {
        // Hapus file gambar dari storage
        if ($slider->image_path) {
            Storage::disk('public')->delete($slider->image_path);
        }

        // Hapus data dari database
        $slider->delete();

        return redirect()->route('admin.superadmin.sliders.index')
                         ->with('success', 'Slider berhasil dihapus.');
    }
}