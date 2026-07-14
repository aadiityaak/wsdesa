<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\LetterRequest;
use App\Models\Post;
use App\Models\Resident;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_berita' => Post::count(),
                'total_penduduk' => Resident::count(),
                'total_permohonan_surat' => LetterRequest::count(),
                'total_pengaduan' => Complaint::count(),
            ],
        ]);
    }

    public function users(): Response
    {
        return Inertia::render('Admin/Pengguna/Index', [
            'users' => User::latest()->paginate(20),
        ]);
    }
}
