<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Download;
use Inertia\Inertia;
use Inertia\Response;

class DownloadController extends Controller
{
    public function index(): Response
    {
        $downloads = Download::with('category')->latest()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'kategori' => $item->category?->nama ?? 'Umum',
                'file_url' => $item->file ? asset('storage/'.$item->file) : '#',
                'downloads' => $item->counter ?? 0,
            ];
        });

        return Inertia::render('Publik/Download', [
            'downloads' => $downloads,
        ]);
    }
}
