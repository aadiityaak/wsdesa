<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SliderController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Slider/Index', [
            'sliders' => Slider::orderBy('urutan')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'image' => ['required', 'image', 'max:2048'],
            'link' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $validated['image'] = $request->file('image')->store('sliders', 'public');
        $validated['urutan'] = Slider::max('urutan') + 1;

        Slider::create($validated);

        return back()->with('success', 'Slider berhasil ditambahkan.');
    }

    public function update(Request $request, Slider $slider): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'link' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slider->image);
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($validated);

        return back()->with('success', 'Slider berhasil diperbarui.');
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        Storage::disk('public')->delete($slider->image);
        $slider->delete();

        return back()->with('success', 'Slider berhasil dihapus.');
    }

    public function sort(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:sliders,id'],
        ]);

        foreach ($request->ids as $index => $id) {
            Slider::where('id', $id)->update(['urutan' => $index]);
        }

        return back()->with('success', 'Urutan slider berhasil diperbarui.');
    }
}
