<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Development;
use Inertia\Inertia;
use Inertia\Response;

class PembangunanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Publik/Pembangunan', [
            'developments' => Development::with(['category', 'images'])->latest()->get(),
        ]);
    }
}
