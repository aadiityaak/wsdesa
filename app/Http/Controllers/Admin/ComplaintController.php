<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['category', 'replies'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Pengaduan/Index', [
            'complaints' => $complaints,
        ]);
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['category', 'replies.user']);

        return Inertia::render('Admin/Pengaduan/Show', [
            'complaint' => $complaint,
        ]);
    }

    public function updateStatus(Request $httpRequest, Complaint $complaint)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:baru,diproses,selesai,ditolak',
        ]);

        $complaint->update(['status' => $validated['status']]);

        return back()->with('success', 'Status pengaduan berhasil diperbarui.');
    }

    public function storeReply(Request $httpRequest, Complaint $complaint)
    {
        $validated = $httpRequest->validate([
            'isi' => 'required|string',
        ]);

        $complaint->replies()->create([
            'user_id' => $httpRequest->user()->id,
            'isi' => $validated['isi'],
        ]);

        return back()->with('success', 'Balasan berhasil dikirim.');
    }
}
