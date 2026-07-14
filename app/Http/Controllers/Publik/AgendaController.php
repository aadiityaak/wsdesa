<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;

class AgendaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Publik/Agenda/Index', [
            'events' => Event::with('category')
                ->orderBy('tanggal_mulai')
                ->paginate(12),
        ]);
    }

    public function show(int $id): Response
    {
        $event = Event::with('category')->findOrFail($id);

        return Inertia::render('Publik/Agenda/Show', [
            'event' => $event,
        ]);
    }
}
