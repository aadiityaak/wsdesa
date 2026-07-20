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
            ->orderByDesc('tahun')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'tahun' => $b->tahun,
                'anggaran' => (float) $b->anggaran,
                'realisasi' => (float) $b->realisasi,
                'keterangan' => $b->keterangan,
                'kategori' => $b->category?->nama ?? '—',
                'tipe' => $b->category?->tipe ?? null,
            ])
            ->values();

        // Ringkasan per tahun untuk chart tren
        $tahunan = $budgets->groupBy('tahun')->map(fn ($items, $tahun) => [
            'tahun' => (int) $tahun,
            'anggaran' => $items->sum('anggaran'),
            'realisasi' => $items->sum('realisasi'),
        ])->sortBy('tahun')->values();

        return Inertia::render('Publik/Apbdes', [
            'budgets' => $budgets,
            'tahunan' => $tahunan,
        ]);
    }
}
