<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Post;
use App\Models\Slider;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    public function index(): Response
    {
        $sliders = Slider::active()->get()->map(function ($slider) {
            return [
                'id' => $slider->id,
                'judul' => $slider->judul,
                'deskripsi' => $slider->deskripsi,
                'image_url' => $slider->image ? asset('storage/'.$slider->image) : null,
                'link' => $slider->link,
            ];
        });

        $latestPosts = Post::with('category')
            ->where('status', 'publish')
            ->latest('published_at')
            ->take(4)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'judul' => $post->judul,
                    'slug' => $post->slug,
                    'ringkasan' => $post->ringkasan,
                    'gambar' => $post->thumbnail_url,
                    'published_at' => $post->published_at,
                    'category' => $post->category ? [
                        'nama' => $post->category->nama,
                        'slug' => $post->category->slug,
                    ] : null,
                ];
            });

        $upcomingEvents = Event::where('status', 'publish')
            ->where('tanggal_mulai', '>=', now())
            ->orderBy('tanggal_mulai')
            ->take(4)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'judul' => $event->judul,
                    'slug' => $event->slug,
                    'deskripsi' => $event->deskripsi,
                    'lokasi' => $event->lokasi,
                    'tanggal_mulai' => $event->tanggal_mulai,
                    'tanggal_selesai' => $event->tanggal_selesai,
                ];
            });

        return Inertia::render('Welcome', [
            'sliders' => $sliders,
            'latestPosts' => $latestPosts,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }
}
