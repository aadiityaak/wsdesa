<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Potential;
use App\Models\PotentialCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PotentialController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Potensi/Index', [
            'potentials' => Potential::with('category')->latest()->get(),
            'categories' => PotentialCategory::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'potential_category_id' => ['nullable', 'exists:potential_categories,id'],
            'nama' => ['required', 'string', 'max:200'],
            'deskripsi' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'lokasi' => ['nullable', 'string', 'max:200'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('potentials', 'public');
        }

        Potential::create($validated);

        return back()->with('success', 'Potensi berhasil ditambahkan.');
    }

    public function update(Request $request, Potential $potential): RedirectResponse
    {
        $validated = $request->validate([
            'potential_category_id' => ['nullable', 'exists:potential_categories,id'],
            'nama' => ['required', 'string', 'max:200'],
            'deskripsi' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'lokasi' => ['nullable', 'string', 'max:200'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);

        if ($request->hasFile('thumbnail')) {
            if ($potential->thumbnail) {
                Storage::disk('public')->delete($potential->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('potentials', 'public');
        }

        $potential->update($validated);

        return back()->with('success', 'Potensi berhasil diperbarui.');
    }

    public function destroy(Potential $potential): RedirectResponse
    {
        if ($potential->thumbnail) {
            Storage::disk('public')->delete($potential->thumbnail);
        }
        $potential->delete();

        return back()->with('success', 'Potensi berhasil dihapus.');
    }
}
