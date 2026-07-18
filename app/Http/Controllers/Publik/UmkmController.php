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
        return Inertia::render('Publik/Umkm', [
            'umkms' => Umkm::with(['category', 'images'])->latest()->get(),
        ]);
    }

    public function show(Umkm $umkm): Response
    {
        return Inertia::render('Publik/UmkmShow', [
            'umkm' => $umkm->load(['category', 'images']),
        ]);
    }
}
