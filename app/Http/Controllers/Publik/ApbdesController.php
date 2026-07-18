<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Inertia\Inertia;
use Inertia\Response;

class ApbdesController extends Controller
{
    public function index(): Response
    {
        $budgets = Budget::with('category')
            ->latest()
            ->get()
            ->groupBy('tahun');

        return Inertia::render('Publik/Apbdes', [
            'budgets' => $budgets,
        ]);
    }
}
