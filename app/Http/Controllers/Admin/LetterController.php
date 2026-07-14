<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterCategory;
use App\Models\LetterRequest;
use App\Models\LetterTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LetterController extends Controller
{
    public function index()
    {
        $letterRequests = LetterRequest::with('category')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/LayananSurat/Index', [
            'letterRequests' => $letterRequests,
        ]);
    }

    public function show(LetterRequest $request)
    {
        $request->load('category');

        return Inertia::render('Admin/LayananSurat/Show', [
            'letterRequest' => $request,
        ]);
    }

    public function updateStatus(Request $httpRequest, LetterRequest $request)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:pending,diproses,selesai,ditolak',
            'catatan_admin' => 'nullable|string',
        ]);

        $request->status = $validated['status'];

        if (isset($validated['catatan_admin'])) {
            $request->catatan_admin = $validated['catatan_admin'];
        }

        if ($validated['status'] === 'selesai' && ! $request->nomor_surat) {
            $category = $request->category;
            $count = LetterRequest::where('letter_category_id', $request->letter_category_id)
                ->where('status', 'selesai')
                ->whereNotNull('nomor_surat')
                ->count() + 1;

            $request->nomor_surat = sprintf(
                '%03d/%s/%s/%s',
                $count,
                Str::upper(Str::slug($category->nama, '')),
                'DS',
                now()->format('m/Y')
            );
        }

        $request->save();

        return back()->with('success', 'Status permohonan berhasil diperbarui.');
    }

    public function cetak(LetterRequest $request)
    {
        $request->load('category.template');

        $template = $request->category->templates()->first();

        if (! $template) {
            return back()->with('error', 'Template surat belum tersedia.');
        }

        $konten = $template->konten;

        $placeholders = [
            '{{nama}}' => $request->nama_pemohon,
            '{{nik}}' => $request->nik,
            '{{tempat_lahir}}' => $request->tempat_lahir,
            '{{tanggal_lahir}}' => $request->tanggal_lahir->format('d-m-Y'),
            '{{jenis_kelamin}}' => $request->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            '{{agama}}' => $request->agama,
            '{{pekerjaan}}' => $request->pekerjaan,
            '{{alamat}}' => $request->alamat,
            '{{keperluan}}' => $request->keperluan,
            '{{nomor_surat}}' => $request->nomor_surat ?? '',
            '{{tanggal_surat}}' => now()->format('d-m-Y'),
        ];

        $konten = str_replace(array_keys($placeholders), array_values($placeholders), $konten);

        $pdf = Pdf::loadHTML($konten);

        return $pdf->download('surat-'.$request->kode_tracking.'.pdf');
    }

    public function categoryIndex()
    {
        $categories = LetterCategory::with('templates', 'requirements')->get();

        return Inertia::render('Admin/LayananSurat/Kategori', [
            'categories' => $categories,
        ]);
    }

    public function categoryStore(Request $httpRequest)
    {
        $validated = $httpRequest->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'nullable|integer|min:0',
        ]);

        LetterCategory::create([
            'nama' => $validated['nama'],
            'slug' => Str::slug($validated['nama']),
            'urutan' => $validated['urutan'] ?? 0,
        ]);

        return back()->with('success', 'Kategori surat berhasil ditambahkan.');
    }

    public function categoryUpdate(Request $httpRequest, LetterCategory $category)
    {
        $validated = $httpRequest->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $category->update([
            'nama' => $validated['nama'],
            'slug' => Str::slug($validated['nama']),
            'urutan' => $validated['urutan'] ?? $category->urutan,
        ]);

        return back()->with('success', 'Kategori surat berhasil diperbarui.');
    }

    public function categoryDestroy(LetterCategory $category)
    {
        $category->delete();

        return back()->with('success', 'Kategori surat berhasil dihapus.');
    }

    public function templateStore(Request $httpRequest)
    {
        $validated = $httpRequest->validate([
            'letter_category_id' => 'required|exists:letter_categories,id',
            'nama' => 'required|string|max:255',
            'konten' => 'required|string',
            'persyaratan' => 'required|string',
        ]);

        LetterTemplate::create($validated);

        return back()->with('success', 'Template surat berhasil ditambahkan.');
    }

    public function templateUpdate(Request $httpRequest, LetterTemplate $template)
    {
        $validated = $httpRequest->validate([
            'letter_category_id' => 'required|exists:letter_categories,id',
            'nama' => 'required|string|max:255',
            'konten' => 'required|string',
            'persyaratan' => 'required|string',
        ]);

        $template->update($validated);

        return back()->with('success', 'Template surat berhasil diperbarui.');
    }

    public function templateDestroy(LetterTemplate $template)
    {
        $template->delete();

        return back()->with('success', 'Template surat berhasil dihapus.');
    }
}
