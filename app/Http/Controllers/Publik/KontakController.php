<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KontakController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Publik/Kontak');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'pesan' => ['required', 'string', 'max:5000'],
        ]);

        Contact::create($validated);

        return back()->with('success', 'Pesan berhasil dikirim! Kami akan menghubungi Anda segera.');
    }
}
