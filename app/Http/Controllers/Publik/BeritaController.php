<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BeritaController extends Controller
{
    public function index(Request $request, ?string $category = null): Response
    {
        $categorySlug = $category ?? $request->query('category');

        $posts = Post::with(['category', 'user'])
            ->where('status', 'publish')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                        ->orWhere('ringkasan', 'like', "%{$search}%");
                });
            })
            ->when($categorySlug, function ($query, $slug) {
                $query->whereHas('category', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            })
            ->latest('published_at')
            ->paginate(12);

        $categories = PostCategory::withCount(['posts' => function ($query) {
            $query->where('status', 'publish');
        }])->orderBy('nama')->get();

        return Inertia::render('Publik/Berita/Index', [
            'posts' => $posts,
            'categories' => $categories,
            'search' => $request->search,
            'categorySlug' => $categorySlug,
        ]);
    }

    public function show(string $slug): Response
    {
        $post = Post::with(['category', 'user', 'comments' => function ($query) {
            $query->where('status', 'approved')->latest();
        }])->where('slug', $slug)->firstOrFail();

        $post->increment('views');

        return Inertia::render('Publik/Berita/Show', [
            'post' => $post,
            'comments' => $post->comments,
        ]);
    }

    public function storeComment(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'email' => ['nullable', 'email', 'max:200'],
            'konten' => ['required', 'string'],
        ]);

        $post->comments()->create($validated);

        return back()->with('success', 'Komentar berhasil dikirim. Menunggu moderasi.');
    }
}
