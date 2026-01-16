<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\LokasiAbsensi;
use App\Models\Project;
use Illuminate\Http\Request;

class LokasiAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $query = LokasiAbsensi::with('project');

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Search by nama_lokasi or alamat
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lokasi', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $lokasis = $query->orderBy('project_id')
            ->orderBy('nama_lokasi')
            ->get();
        
        $projects = Project::orderBy('nama')->get();
        
        return view('perusahaan.lokasi-absensi.index', compact('lokasis', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'koordinat' => 'required|string',
            'radius' => 'required|integer|min:10|max:1000',
            'deskripsi' => 'nullable|string',
        ], [
            'project_id.required' => 'Project harus dipilih',
            'project_id.exists' => 'Project tidak valid',
            'nama_lokasi.required' => 'Nama lokasi harus diisi',
            'nama_lokasi.max' => 'Nama lokasi maksimal 255 karakter',
            'alamat.required' => 'Alamat harus diisi',
            'koordinat.required' => 'Koordinat harus diisi',
            'radius.required' => 'Radius harus diisi',
            'radius.integer' => 'Radius harus berupa angka',
            'radius.min' => 'Radius minimal 10 meter',
            'radius.max' => 'Radius maksimal 1000 meter',
        ]);

        // Parse koordinat (latitude, longitude)
        $koordinat = explode(',', $validated['koordinat']);
        if (count($koordinat) != 2) {
            return back()->with('error', 'Format koordinat tidak valid. Gunakan format: latitude, longitude');
        }

        $validated['latitude'] = trim($koordinat[0]);
        $validated['longitude'] = trim($koordinat[1]);
        unset($validated['koordinat']);

        // Auto-assign perusahaan_id
        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        LokasiAbsensi::create($validated);

        return redirect()->route('perusahaan.kehadiran.lokasi-absensi')
            ->with('success', 'Lokasi absensi berhasil ditambahkan');
    }

    public function update(Request $request, LokasiAbsensi $lokasi)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_lokasi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'koordinat' => 'required|string',
            'radius' => 'required|integer|min:10|max:1000',
            'deskripsi' => 'nullable|string',
        ], [
            'project_id.required' => 'Project harus dipilih',
            'project_id.exists' => 'Project tidak valid',
            'nama_lokasi.required' => 'Nama lokasi harus diisi',
            'nama_lokasi.max' => 'Nama lokasi maksimal 255 karakter',
            'alamat.required' => 'Alamat harus diisi',
            'koordinat.required' => 'Koordinat harus diisi',
            'radius.required' => 'Radius harus diisi',
            'radius.integer' => 'Radius harus berupa angka',
            'radius.min' => 'Radius minimal 10 meter',
            'radius.max' => 'Radius maksimal 1000 meter',
        ]);

        // Parse koordinat (latitude, longitude)
        $koordinat = explode(',', $validated['koordinat']);
        if (count($koordinat) != 2) {
            return back()->with('error', 'Format koordinat tidak valid. Gunakan format: latitude, longitude');
        }

        $validated['latitude'] = trim($koordinat[0]);
        $validated['longitude'] = trim($koordinat[1]);
        unset($validated['koordinat']);

        $lokasi->update($validated);

        return redirect()->route('perusahaan.kehadiran.lokasi-absensi')
            ->with('success', 'Lokasi absensi berhasil diperbarui');
    }

    public function destroy(LokasiAbsensi $lokasi)
    {
        $lokasi->delete();

        return redirect()->route('perusahaan.kehadiran.lokasi-absensi')
            ->with('success', 'Lokasi absensi berhasil dihapus');
    }
}
