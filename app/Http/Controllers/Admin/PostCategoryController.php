<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PostCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Kategori/Index', [
            'categories' => PostCategory::orderBy('nama')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Kategori/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200', 'unique:post_categories,nama'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);

        PostCategory::create($validated);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(PostCategory $postCategory): Response
    {
        return Inertia::render('Admin/Kategori/Form', [
            'category' => $postCategory,
        ]);
    }

    public function update(Request $request, PostCategory $postCategory): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200', 'unique:post_categories,nama,'.$postCategory->id],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['nama']);

        $postCategory->update($validated);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(PostCategory $postCategory): RedirectResponse
    {
        $postCategory->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
