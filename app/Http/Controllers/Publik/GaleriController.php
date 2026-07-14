<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\DownloadCategory;
use App\Models\Gallery;
use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class GaleriController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Publik/Galeri/Index', [
            'galleries' => Gallery::with('images')->latest()->get(),
        ]);
    }

    public function show(Gallery $gallery): Response
    {
        return Inertia::render('Publik/Galeri/Show', [
            'gallery' => $gallery->load('images'),
        ]);
    }

    public function page(string $slug): Response
    {
        $page = Page::where('slug', $slug)->where('status', 'publish')->firstOrFail();

        return Inertia::render('Publik/Halaman/Show', [
            'page' => $page,
        ]);
    }

    public function downloads(): Response
    {
        return Inertia::render('Publik/Download/Index', [
            'downloads' => Download::with('category')->latest()->get(),
            'categories' => DownloadCategory::orderBy('nama')->get(),
        ]);
    }
}
