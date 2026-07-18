<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function show(string $slug): Response
    {
        $page = Page::where('slug', $slug)->where('status', 'publish')->firstOrFail();

        return Inertia::render('Publik/Halaman', [
            'page' => $page,
        ]);
    }
}
