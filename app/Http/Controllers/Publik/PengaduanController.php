<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PengaduanController extends Controller
{
    public function index()
    {
        return Inertia::render('Publik/Pengaduan', [
            'categories' => ComplaintCategory::all(),
        ]);
    }

    public function store(Request $httpRequest)
    {
        $validated = $httpRequest->validate([
            'complaint_category_id' => 'nullable|exists:complaint_categories,id',
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'isi' => 'required|string',
        ]);

        $validated['kode_tracking'] = Str::upper(Str::random(10));
        $validated['status'] = 'baru';

        if ($httpRequest->user()) {
            $validated['user_id'] = $httpRequest->user()->id;
        }

        $complaint = Complaint::create($validated);

        return back()->with('success', 'Pengaduan berhasil dikirim. Kode tracking Anda: '.$complaint->kode_tracking);
    }

    public function cekStatus()
    {
        return Inertia::render('Publik/Pengaduan/Status');
    }

    public function showStatus(Request $httpRequest)
    {
        $httpRequest->validate([
            'kode_tracking' => 'required|string|max:20',
        ]);

        $complaint = Complaint::with(['category', 'replies.user'])
            ->where('kode_tracking', $httpRequest->kode_tracking)
            ->first();

        if (! $complaint) {
            return back()->with('error', 'Data tidak ditemukan. Periksa kembali kode tracking Anda.');
        }

        return Inertia::render('Publik/Pengaduan/Status', [
            'complaint' => $complaint,
        ]);
    }
}
