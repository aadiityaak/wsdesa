<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Potential;
use Inertia\Inertia;
use Inertia\Response;

class PotensiController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Publik/Potensi', [
            'potentials' => Potential::with('category')->latest()->get(),
        ]);
    }
}
