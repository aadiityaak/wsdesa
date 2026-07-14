<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Berita/Index', [
            'posts' => Post::with(['category', 'user'])
                ->latest()
                ->paginate(15),
            'categories' => PostCategory::orderBy('nama')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Berita/Form', [
            'categories' => PostCategory::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'post_category_id' => ['required', 'exists:post_categories,id'],
            'ringkasan' => ['nullable', 'string'],
            'konten' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:draft,publish,archive'],
        ]);

        $validated['slug'] = Str::slug($validated['judul']);
        $validated['user_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }

        if ($validated['status'] === 'publish' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        Post::create($validated);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(Post $post): Response
    {
        return Inertia::render('Admin/Berita/Form', [
            'post' => $post,
            'categories' => PostCategory::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'post_category_id' => ['required', 'exists:post_categories,id'],
            'ringkasan' => ['nullable', 'string'],
            'konten' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:draft,publish,archive'],
        ]);

        $validated['slug'] = Str::slug($validated['judul']);

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        } else {
            unset($validated['thumbnail']);
        }

        if ($validated['status'] === 'publish' && ! $post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }
        $post->delete();

        return back()->with('success', 'Berita berhasil dihapus.');
    }
}
