<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Halaman/Index', [
            'pages' => Page::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'konten' => ['required', 'string'],
            'status' => ['required', 'in:publish,draft'],
        ]);

        $validated['slug'] = Str::slug($validated['judul']);
        Page::create($validated);

        return back()->with('success', 'Halaman berhasil ditambahkan.');
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'konten' => ['required', 'string'],
            'status' => ['required', 'in:publish,draft'],
        ]);

        $validated['slug'] = Str::slug($validated['judul']);
        $page->update($validated);

        return back()->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return back()->with('success', 'Halaman berhasil dihapus.');
    }
}
