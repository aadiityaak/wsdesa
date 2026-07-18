<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Inertia\Inertia;
use Inertia\Response;

class GaleriController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Publik/Galeri', [
            'galleries' => Gallery::with('images')->latest()->paginate(12),
        ]);
    }

    public function show(Gallery $gallery): Response
    {
        return Inertia::render('Publik/GaleriShow', [
            'gallery' => $gallery->load('images'),
        ]);
    }
}
