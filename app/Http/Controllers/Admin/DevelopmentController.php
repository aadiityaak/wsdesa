<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Development;
use App\Models\DevelopmentCategory;
use App\Models\DevelopmentImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DevelopmentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Pembangunan/Index', [
            'developments' => Development::with(['category', 'images'])->latest()->get(),
            'categories' => DevelopmentCategory::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'development_category_id' => ['nullable', 'exists:development_categories,id'],
            'nama' => ['required', 'string', 'max:200'],
            'lokasi' => ['nullable', 'string', 'max:200'],
            'deskripsi' => ['required', 'string'],
            'anggaran' => ['nullable', 'numeric'],
            'sumber_dana' => ['nullable', 'string', 'max:200'],
            'tahun' => ['nullable', 'integer'],
            'status' => ['required', 'in:rencana,berjalan,selesai'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        Development::create($validated);

        return back()->with('success', 'Pembangunan berhasil ditambahkan.');
    }

    public function update(Request $request, Development $development): RedirectResponse
    {
        $validated = $request->validate([
            'development_category_id' => ['nullable', 'exists:development_categories,id'],
            'nama' => ['required', 'string', 'max:200'],
            'lokasi' => ['nullable', 'string', 'max:200'],
            'deskripsi' => ['required', 'string'],
            'anggaran' => ['nullable', 'numeric'],
            'sumber_dana' => ['nullable', 'string', 'max:200'],
            'tahun' => ['nullable', 'integer'],
            'status' => ['required', 'in:rencana,berjalan,selesai'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        $development->update($validated);

        return back()->with('success', 'Pembangunan berhasil diperbarui.');
    }

    public function destroy(Development $development): RedirectResponse
    {
        foreach ($development->images as $image) {
            Storage::disk('public')->delete($image->file);
        }
        $development->delete();

        return back()->with('success', 'Pembangunan berhasil dihapus.');
    }

    public function storeImage(Request $request, Development $development): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:2048'],
            'keterangan' => ['nullable', 'string', 'max:200'],
        ]);

        $development->images()->create([
            'file' => $request->file('file')->store('developments', 'public'),
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Foto berhasil ditambahkan.');
    }

    public function destroyImage(Development $development, DevelopmentImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->file);
        $image->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
