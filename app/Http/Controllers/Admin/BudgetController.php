<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BudgetController extends Controller
{
    public function index(): Response
    {
        $budgets = Budget::with('category')
            ->latest()
            ->get()
            ->groupBy('tahun');

        return Inertia::render('Admin/Apbdes/Index', [
            'budgets' => $budgets,
            'categories' => BudgetCategory::orderBy('nama')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Apbdes/Form', [
            'categories' => BudgetCategory::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'budget_category_id' => ['required', 'exists:budget_categories,id'],
            'tahun' => ['required', 'integer', 'min:2000'],
            'anggaran' => ['required', 'numeric'],
            'realisasi' => ['nullable', 'numeric'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $validated['realisasi'] = $validated['realisasi'] ?? 0;
        Budget::create($validated);

        return back()->with('success', 'APBDes berhasil ditambahkan.');
    }

    public function edit(Budget $budget): Response
    {
        return Inertia::render('Admin/Apbdes/Form', [
            'budget' => $budget->load('category'),
            'categories' => BudgetCategory::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        $validated = $request->validate([
            'budget_category_id' => ['required', 'exists:budget_categories,id'],
            'tahun' => ['required', 'integer', 'min:2000'],
            'anggaran' => ['required', 'numeric'],
            'realisasi' => ['nullable', 'numeric'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $validated['realisasi'] = $validated['realisasi'] ?? 0;
        $budget->update($validated);

        return back()->with('success', 'APBDes berhasil diperbarui.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $budget->delete();

        return back()->with('success', 'APBDes berhasil dihapus.');
    }
}
