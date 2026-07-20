<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $profile = Profile::first();
        if (! $profile) {
            $profile = Profile::create(['nama_desa' => 'Desa Digital']);
        }

        return Inertia::render('Admin/Profil/Edit', [
            'profile' => $profile,
            'provinsiList' => Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')
                ->orderBy('nama')->get(['kode', 'nama']),
            'kabupatenList' => Wilayah::whereRaw('CHAR_LENGTH(kode) = 5')
                ->orderBy('nama')->get(['kode', 'nama']),
            'kecamatanList' => Wilayah::whereRaw('CHAR_LENGTH(kode) = 8')
                ->orderBy('nama')->get(['kode', 'nama']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = Profile::firstOrFail();

        $validated = $request->validate([
            'nama_desa' => ['required', 'string', 'max:200'],
            'kode_desa' => ['nullable', 'string', 'max:20'],
            'kecamatan' => ['required', 'string', 'max:200'],
            'kabupaten' => ['required', 'string', 'max:200'],
            'provinsi' => ['required', 'string', 'max:200'],
            'alamat' => ['nullable', 'string'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:200'],
            'website' => ['nullable', 'url', 'max:200'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'visi' => ['nullable', 'string'],
            'misi' => ['nullable', 'string'],
            'sejarah' => ['nullable', 'string'],
            'luas_wilayah' => ['nullable', 'string', 'max:50'],
            'batas_utara' => ['nullable', 'string'],
            'batas_selatan' => ['nullable', 'string'],
            'batas_timur' => ['nullable', 'string'],
            'batas_barat' => ['nullable', 'string'],
            'orbitasi_ke_kecamatan' => ['nullable', 'string', 'max:50'],
            'orbitasi_ke_kabupaten' => ['nullable', 'string', 'max:50'],
            'facebook' => ['nullable', 'url', 'max:200'],
            'instagram' => ['nullable', 'url', 'max:200'],
            'youtube' => ['nullable', 'url', 'max:200'],
            'tiktok' => ['nullable', 'url', 'max:200'],
        ]);

        // Hapus logo dari validated agar tidak ikut di-fill (logo ditangani terpisah)
        unset($validated['logo']);

        $profile->fill($validated);

        // Tangani upload logo secara terpisah
        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            try {
                $profile->logo = $request->file('logo')->store('profiles', 'public');
            } catch (\Exception $e) {
                Log::error('Logo upload failed: '.$e->getMessage());

                return back()->with('error', 'Gagal mengupload logo. Silakan coba lagi.');
            }
        }

        $profile->save();

        return back()->with('success', 'Profil desa berhasil diperbarui.');
    }
}
