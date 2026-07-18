<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Pengumuman/Index', [
            'announcements' => Announcement::latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Pengumuman/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'konten' => ['required', 'string'],
            'tipe' => ['required', 'in:info,warning,urgent'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ]);

        Announcement::create($validated);

        return back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Announcement $announcement): Response
    {
        return Inertia::render('Admin/Pengumuman/Form', [
            'announcement' => $announcement,
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'konten' => ['required', 'string'],
            'tipe' => ['required', 'in:info,warning,urgent'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ]);

        $announcement->update($validated);

        return back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
