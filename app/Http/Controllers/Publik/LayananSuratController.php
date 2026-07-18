<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\LetterCategory;
use App\Models\LetterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LayananSuratController extends Controller
{
    public function index()
    {
        $categories = LetterCategory::with('requirements')->get();

        return Inertia::render('Publik/LayananSurat', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $httpRequest)
    {
        $validated = $httpRequest->validate([
            'letter_category_id' => 'required|exists:letter_categories,id',
            'nama_pemohon' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string|max:100',
            'pekerjaan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'keperluan' => 'required|string',
            'no_hp' => 'required|string|max:20',
        ]);

        $validated['kode_tracking'] = Str::upper(Str::random(10));
        $validated['status'] = 'pending';

        if ($httpRequest->user()) {
            $validated['user_id'] = $httpRequest->user()->id;
        }

        $letterRequest = LetterRequest::create($validated);

        return back()->with('tracking_code', $letterRequest->kode_tracking);
    }

    public function cekStatus(Request $httpRequest)
    {
        $httpRequest->validate([
            'kode_tracking' => 'required|string|max:20',
        ]);

        $letterRequest = LetterRequest::with('category')
            ->where('kode_tracking', $httpRequest->kode_tracking)
            ->first();

        if (! $letterRequest) {
            return response()->json(['error' => 'Data tidak ditemukan. Periksa kembali kode tracking Anda.'], 404);
        }

        return response()->json(['letterRequest' => $letterRequest->load('category')]);
    }
}
