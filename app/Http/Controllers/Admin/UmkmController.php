<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\UmkmCategory;
use App\Models\UmkmImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UmkmController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Umkm/Index', [
            'umkms' => Umkm::with(['category', 'images'])->latest()->get(),
            'categories' => UmkmCategory::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'umkm_category_id' => ['nullable', 'exists:umkm_categories,id'],
            'nama_usaha' => ['required', 'string', 'max:200'],
            'pemilik' => ['required', 'string', 'max:200'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $validated['slug'] = Str::slug($validated['nama_usaha']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('umkm', 'public');
        }

        Umkm::create($validated);

        return back()->with('success', 'UMKM berhasil ditambahkan.');
    }

    public function update(Request $request, Umkm $umkm): RedirectResponse
    {
        $validated = $request->validate([
            'umkm_category_id' => ['nullable', 'exists:umkm_categories,id'],
            'nama_usaha' => ['required', 'string', 'max:200'],
            'pemilik' => ['required', 'string', 'max:200'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $validated['slug'] = Str::slug($validated['nama_usaha']);

        if ($request->hasFile('thumbnail')) {
            if ($umkm->thumbnail) {
                Storage::disk('public')->delete($umkm->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('umkm', 'public');
        }

        $umkm->update($validated);

        return back()->with('success', 'UMKM berhasil diperbarui.');
    }

    public function destroy(Umkm $umkm): RedirectResponse
    {
        foreach ($umkm->images as $image) {
            Storage::disk('public')->delete($image->file);
        }
        if ($umkm->thumbnail) {
            Storage::disk('public')->delete($umkm->thumbnail);
        }
        $umkm->delete();

        return back()->with('success', 'UMKM berhasil dihapus.');
    }

    public function storeImage(Request $request, Umkm $umkm): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:2048'],
        ]);

        $umkm->images()->create([
            'file' => $request->file('file')->store('umkm', 'public'),
            'urutan' => $umkm->images()->count(),
        ]);

        return back()->with('success', 'Foto berhasil ditambahkan.');
    }

    public function destroyImage(Umkm $umkm, UmkmImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->file);
        $image->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function categoryIndex(): Response
    {
        return Inertia::render('Admin/Umkm/Kategori', [
            'categories' => UmkmCategory::orderBy('nama')->get(),
        ]);
    }

    public function categoryStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        UmkmCategory::create($validated);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function categoryUpdate(Request $request, UmkmCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        $category->update($validated);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function categoryDestroy(UmkmCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
