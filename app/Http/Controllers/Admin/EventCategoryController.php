<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class EventCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/KategoriAgenda/Index', [
            'categories' => EventCategory::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200', 'unique:event_categories,nama'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);

        EventCategory::create($validated);

        return back()->with('success', 'Kategori agenda berhasil ditambahkan.');
    }

    public function update(Request $request, EventCategory $eventCategory): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200', 'unique:event_categories,nama,'.$eventCategory->id],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);

        $eventCategory->update($validated);

        return back()->with('success', 'Kategori agenda berhasil diperbarui.');
    }

    public function destroy(EventCategory $eventCategory): RedirectResponse
    {
        $eventCategory->delete();

        return back()->with('success', 'Kategori agenda berhasil dihapus.');
    }
}
