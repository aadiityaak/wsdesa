<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResidentMutation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MutationController extends Controller
{
    public function index(): Response
    {
        $mutations = ResidentMutation::with('resident')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Kependudukan/MutasiIndex', [
            'mutations' => $mutations,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'resident_id' => ['required', 'exists:residents,id'],
            'jenis_mutasi' => ['required', 'in:Lahir,Mati,Pindah Masuk,Pindah Keluar'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['required', 'string'],
        ]);

        ResidentMutation::create($validated);

        return redirect()->back()->with('success', 'Data mutasi berhasil ditambahkan.');
    }
}
