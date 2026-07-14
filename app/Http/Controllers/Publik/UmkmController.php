<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use Inertia\Inertia;
use Inertia\Response;

class UmkmController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Publik/Umkm/Index', [
            'umkms' => Umkm::with(['category', 'images'])->latest()->get(),
        ]);
    }

    public function show(Umkm $umkm): Response
    {
        return Inertia::render('Publik/Umkm/Show', [
            'umkm' => $umkm->load(['category', 'images']),
        ]);
    }
}
