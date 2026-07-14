<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Family;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FamilyController extends Controller
{
    public function index(): Response
    {
        $families = Family::withCount('residents')
            ->with(['residents' => function ($query) {
                $query->where('hubungan_keluarga', 'Kepala Keluarga');
            }])
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Kependudukan/KKIndex', [
            'families' => $families,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'no_kk' => ['required', 'string', 'max:16', 'unique:families,no_kk'],
            'alamat' => ['required', 'string'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'dusun' => ['nullable', 'string', 'max:100'],
        ]);

        Family::create($validated);

        return redirect()->back()->with('success', 'Kartu Keluarga berhasil ditambahkan.');
    }

    public function update(Request $request, Family $family): RedirectResponse
    {
        $validated = $request->validate([
            'no_kk' => ['required', 'string', 'max:16', 'unique:families,no_kk,'.$family->id],
            'alamat' => ['required', 'string'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'dusun' => ['nullable', 'string', 'max:100'],
        ]);

        $family->update($validated);

        return redirect()->back()->with('success', 'Kartu Keluarga berhasil diperbarui.');
    }

    public function show(Family $family): Response
    {
        $family->load('residents');

        return Inertia::render('Admin/Kependudukan/KKShow', [
            'family' => $family,
        ]);
    }

    public function destroy(Family $family): RedirectResponse
    {
        $family->delete();

        return redirect()->route('admin.kependudukan.kk.index')->with('success', 'Kartu Keluarga berhasil dihapus.');
    }
}
