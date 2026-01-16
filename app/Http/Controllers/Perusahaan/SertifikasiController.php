<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Sertifikasi;
use Illuminate\Http\Request;

class SertifikasiController extends Controller
{
    public function store(Request $request, $karyawanHashId)
    {
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        
        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);

        $validated = $request->validate([
            'nama_sertifikasi' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tanggal_terbit' => 'required|date',
            'tanggal_expired' => 'nullable|date|after:tanggal_terbit',
            'nomor_sertifikat' => 'nullable|string|max:255',
            'url_sertifikat' => 'nullable|url|max:500',
        ], [
            'nama_sertifikasi.required' => 'Nama sertifikasi wajib diisi',
            'penerbit.required' => 'Penerbit wajib diisi',
            'tanggal_terbit.required' => 'Tanggal terbit wajib diisi',
            'tanggal_terbit.date' => 'Format tanggal tidak valid',
            'tanggal_expired.date' => 'Format tanggal tidak valid',
            'tanggal_expired.after' => 'Tanggal expired harus setelah tanggal terbit',
            'url_sertifikat.url' => 'Format URL tidak valid',
        ]);

        try {
            $validated['karyawan_id'] = $karyawan->id;
            Sertifikasi::create($validated);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Sertifikasi berhasil ditambahkan', 'active_tab' => 'sertifikasi']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menambahkan sertifikasi: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $karyawanHashId, $sertifikasiHashId)
    {
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        $sertifikasiId = \Vinkla\Hashids\Facades\Hashids::decode($sertifikasiHashId)[0] ?? null;
        
        if (!$karyawanId || !$sertifikasiId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);
        $sertifikasi = Sertifikasi::where('karyawan_id', $karyawan->id)
                                  ->findOrFail($sertifikasiId);

        $validated = $request->validate([
            'nama_sertifikasi' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tanggal_terbit' => 'required|date',
            'tanggal_expired' => 'nullable|date|after:tanggal_terbit',
            'nomor_sertifikat' => 'nullable|string|max:255',
            'url_sertifikat' => 'nullable|url|max:500',
        ], [
            'nama_sertifikasi.required' => 'Nama sertifikasi wajib diisi',
            'penerbit.required' => 'Penerbit wajib diisi',
            'tanggal_terbit.required' => 'Tanggal terbit wajib diisi',
            'tanggal_terbit.date' => 'Format tanggal tidak valid',
            'tanggal_expired.date' => 'Format tanggal tidak valid',
            'tanggal_expired.after' => 'Tanggal expired harus setelah tanggal terbit',
            'url_sertifikat.url' => 'Format URL tidak valid',
        ]);

        try {
            $sertifikasi->update($validated);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Sertifikasi berhasil diperbarui', 'active_tab' => 'sertifikasi']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui sertifikasi: ' . $e->getMessage());
        }
    }

    public function destroy($karyawanHashId, $sertifikasiHashId)
    {
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        $sertifikasiId = \Vinkla\Hashids\Facades\Hashids::decode($sertifikasiHashId)[0] ?? null;
        
        if (!$karyawanId || !$sertifikasiId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);
        $sertifikasi = Sertifikasi::where('karyawan_id', $karyawan->id)
                                  ->findOrFail($sertifikasiId);

        try {
            $sertifikasi->delete();

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Sertifikasi berhasil dihapus', 'active_tab' => 'sertifikasi']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menghapus sertifikasi: ' . $e->getMessage());
        }
    }
}
