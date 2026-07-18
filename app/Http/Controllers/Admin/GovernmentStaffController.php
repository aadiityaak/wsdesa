<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GovernmentStaff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class GovernmentStaffController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Pemerintahan/StaffIndex', [
            'staff' => GovernmentStaff::orderBy('urutan')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Pemerintahan/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'nip' => ['nullable', 'string', 'max:30'],
            'jabatan' => ['required', 'string', 'max:200'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:50'],
            'tanggal_mulai_jabatan' => ['nullable', 'date'],
            'tanggal_akhir_jabatan' => ['nullable', 'date'],
            'urutan' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('government-staff', 'public');
        }

        GovernmentStaff::create($validated);

        return back()->with('success', 'Perangkat desa berhasil ditambahkan.');
    }

    public function edit(GovernmentStaff $staff): Response
    {
        return Inertia::render('Admin/Pemerintahan/Form', [
            'staff' => $staff,
        ]);
    }

    public function update(Request $request, GovernmentStaff $staff): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'nip' => ['nullable', 'string', 'max:30'],
            'jabatan' => ['required', 'string', 'max:200'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:50'],
            'tanggal_mulai_jabatan' => ['nullable', 'date'],
            'tanggal_akhir_jabatan' => ['nullable', 'date'],
            'urutan' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('foto')) {
            if ($staff->foto) {
                Storage::disk('public')->delete($staff->foto);
            }
            $validated['foto'] = $request->file('foto')->store('government-staff', 'public');
        }

        $staff->update($validated);

        return back()->with('success', 'Perangkat desa berhasil diperbarui.');
    }

    public function destroy(GovernmentStaff $staff): RedirectResponse
    {
        if ($staff->foto) {
            Storage::disk('public')->delete($staff->foto);
        }
        $staff->delete();

        return back()->with('success', 'Perangkat desa berhasil dihapus.');
    }
}
