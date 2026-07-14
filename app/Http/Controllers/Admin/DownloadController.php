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

    public function destroy(Download $download): RedirectResponse
    {
        Storage::disk('public')->delete($download->file);
        $download->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }
}
