<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use App\Models\User;
use App\Models\Kantor;
use App\Models\Patroli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $perusahaan = auth()->user()->perusahaan;
        
        $stats = [
            'total_users' => User::where('perusahaan_id', $perusahaan->id)->count(),
            'total_kantor' => Kantor::where('perusahaan_id', $perusahaan->id)->count(),
            'total_patroli' => Patroli::where('perusahaan_id', $perusahaan->id)->count(),
        ];

        return view('perusahaan.profil.index', compact('perusahaan', 'stats'));
    }

    public function update(Request $request)
    {
        $perusahaan = auth()->user()->perusahaan;

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ], [
            'nama.required' => 'Nama perusahaan wajib diisi',
            'nama.max' => 'Nama perusahaan maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'telepon.max' => 'No. telepon maksimal 20 karakter',
        ]);

        $perusahaan->update($validated);

        return redirect()->route('perusahaan.profil.index')
            ->with('success', 'Profil perusahaan berhasil diupdate');
    }

    public function uploadLogo(Request $request)
    {
        $perusahaan = auth()->user()->perusahaan;

        $validated = $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
        ], [
            'logo.required' => 'File logo wajib dipilih',
            'logo.image' => 'File harus berupa gambar',
            'logo.mimes' => 'Format file harus: jpeg, png, jpg, atau gif',
            'logo.max' => 'Ukuran file maksimal 5MB',
        ]);

        // Delete old logo if exists
        if ($perusahaan->logo) {
            Storage::disk('public')->delete($perusahaan->logo);
        }

        // Upload new logo
        $path = $request->file('logo')->store('logos', 'public');

        $perusahaan->update(['logo' => $path]);

        return redirect()->route('perusahaan.profil.index')
            ->with('success', 'Logo perusahaan berhasil diupload');
    }
}
