<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\InstitutionMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class InstitutionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Pemerintahan/LembagaIndex', [
            'institutions' => Institution::with('members')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'singkatan' => ['nullable', 'string', 'max:50'],
            'jenis' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('institutions', 'public');
        }

        Institution::create($validated);

        return back()->with('success', 'Lembaga berhasil ditambahkan.');
    }

    public function update(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'singkatan' => ['nullable', 'string', 'max:50'],
            'jenis' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($institution->foto) {
                Storage::disk('public')->delete($institution->foto);
            }
            $validated['foto'] = $request->file('foto')->store('institutions', 'public');
        }

        $institution->update($validated);

        return back()->with('success', 'Lembaga berhasil diperbarui.');
    }

    public function destroy(Institution $institution): RedirectResponse
    {
        if ($institution->foto) {
            Storage::disk('public')->delete($institution->foto);
        }
        $institution->delete();

        return back()->with('success', 'Lembaga berhasil dihapus.');
    }

    public function storeMember(Request $request, Institution $institution): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:200'],
            'jabatan' => ['required', 'string', 'max:200'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'urutan' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('institution-members', 'public');
        }

        $institution->members()->create($validated);

        return back()->with('success', 'Anggota lembaga berhasil ditambahkan.');
    }

    public function destroyMember(Institution $institution, InstitutionMember $member): RedirectResponse
    {
        if ($member->foto) {
            Storage::disk('public')->delete($member->foto);
        }
        $member->delete();

        return back()->with('success', 'Anggota lembaga berhasil dihapus.');
    }
}
