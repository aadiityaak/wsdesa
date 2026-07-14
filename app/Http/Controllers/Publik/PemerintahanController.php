<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\GovernmentStaff;
use App\Models\Institution;
use Inertia\Inertia;
use Inertia\Response;

class PemerintahanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Publik/Pemerintahan', [
            'staff' => GovernmentStaff::orderBy('urutan')->get(),
            'institutions' => Institution::with('members')->get(),
        ]);
    }
}
