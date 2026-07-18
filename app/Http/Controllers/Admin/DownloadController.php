<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\DownloadCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DownloadController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Download/Index', [
            'downloads' => Download::with('category')->latest()->get(),
            'categories' => DownloadCategory::orderBy('nama')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Download/Form', [
            'categories' => DownloadCategory::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'download_category_id' => ['nullable', 'exists:download_categories,id'],
            'nama' => ['required', 'string', 'max:200'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $validated['file'] = $request->file('file')->store('downloads', 'public');
        Download::create($validated);

        return back()->with('success', 'File berhasil diunggah.');
    }

    public function edit(Download $download): Response
    {
        return Inertia::render('Admin/Download/Form', [
            'download' => $download->load('category'),
            'categories' => DownloadCategory::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Download $download): RedirectResponse
    {
        $validated = $request->validate([
            'download_category_id' => ['nullable', 'exists:download_categories,id'],
            'nama' => ['required', 'string', 'max:200'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($download->file);
            $validated['file'] = $request->file('file')->store('downloads', 'public');
        } else {
            unset($validated['file']);
        }

        $download->update($validated);

        return back()->with('success', 'File berhasil diperbarui.');
    }

    public function destroy(Download $download): RedirectResponse
    {
        Storage::disk('public')->delete($download->file);
        $download->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }
}
