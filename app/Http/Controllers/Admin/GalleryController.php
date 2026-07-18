<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Galeri/Index', [
            'galleries' => Gallery::with(['user', 'images'])->latest()->paginate(12)->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);
        $validated['user_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('galleries', 'public');
        }

        Gallery::create($validated);

        return back()->with('success', 'Galeri berhasil ditambahkan.');
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);

        if ($request->hasFile('thumbnail')) {
            if ($gallery->thumbnail) {
                Storage::disk('public')->delete($gallery->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('galleries', 'public');
        }

        $gallery->update($validated);

        return back()->with('success', 'Galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        foreach ($gallery->images as $image) {
            Storage::disk('public')->delete($image->file);
        }
        if ($gallery->thumbnail) {
            Storage::disk('public')->delete($gallery->thumbnail);
        }
        $gallery->delete();

        return back()->with('success', 'Galeri berhasil dihapus.');
    }

    public function storeImage(Request $request, Gallery $gallery): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:2048'],
            'judul' => ['nullable', 'string', 'max:200'],
        ]);

        $gallery->images()->create([
            'file' => $request->file('file')->store('galleries', 'public'),
            'judul' => $request->judul,
            'urutan' => $gallery->images()->count(),
        ]);

        return back()->with('success', 'Foto berhasil ditambahkan.');
    }

    public function destroyImage(Gallery $gallery, GalleryImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->file);
        $image->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function videoIndex(): Response
    {
        return Inertia::render('Admin/Galeri/Video', [
            'videos' => Video::latest()->get(),
        ]);
    }

    public function videoStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'url' => ['required', 'string', 'max:500'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Video::create($validated);

        return back()->with('success', 'Video berhasil ditambahkan.');
    }

    public function videoUpdate(Request $request, Video $video): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'url' => ['required', 'string', 'max:500'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $video->update($validated);

        return back()->with('success', 'Video berhasil diperbarui.');
    }

    public function videoDestroy(Video $video): RedirectResponse
    {
        $video->delete();

        return back()->with('success', 'Video berhasil dihapus.');
    }
}
