<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Inertia\Inertia;
use Inertia\Response;

class ProfilController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Publik/Profil', [
            'profile' => Profile::first(),
        ]);
    }
}
