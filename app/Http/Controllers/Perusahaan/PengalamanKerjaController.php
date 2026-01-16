<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\PengalamanKerja;
use Illuminate\Http\Request;

class PengalamanKerjaController extends Controller
{
    public function store(Request $request, $karyawanHashId)
    {
        // Decode hash_id to get real id
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        
        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);

        $rules = [
            'nama_perusahaan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'masih_bekerja' => 'boolean',
            'deskripsi_pekerjaan' => 'nullable|string',
            'pencapaian' => 'nullable|string',
        ];

        $validated = $request->validate($rules, [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_mulai.date' => 'Format tanggal tidak valid',
            'tanggal_selesai.date' => 'Format tanggal tidak valid',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ]);

        try {
            // If masih_bekerja is checked, set tanggal_selesai to null
            if ($request->has('masih_bekerja') && $request->masih_bekerja) {
                $validated['tanggal_selesai'] = null;
            }

            $validated['karyawan_id'] = $karyawan->id;
            $validated['masih_bekerja'] = $request->has('masih_bekerja') ? true : false;

            PengalamanKerja::create($validated);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Pengalaman kerja berhasil ditambahkan', 'active_tab' => 'pengalaman-kerja']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menambahkan pengalaman kerja: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $karyawanHashId, $pengalamanHashId)
    {
        // Decode hash_id to get real id
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        $pengalamanId = \Vinkla\Hashids\Facades\Hashids::decode($pengalamanHashId)[0] ?? null;
        
        if (!$karyawanId || !$pengalamanId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);
        $pengalaman = PengalamanKerja::where('karyawan_id', $karyawan->id)
                                     ->findOrFail($pengalamanId);

        $rules = [
            'nama_perusahaan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'masih_bekerja' => 'boolean',
            'deskripsi_pekerjaan' => 'nullable|string',
            'pencapaian' => 'nullable|string',
        ];

        $validated = $request->validate($rules, [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_mulai.date' => 'Format tanggal tidak valid',
            'tanggal_selesai.date' => 'Format tanggal tidak valid',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ]);

        try {
            // If masih_bekerja is checked, set tanggal_selesai to null
            if ($request->has('masih_bekerja') && $request->masih_bekerja) {
                $validated['tanggal_selesai'] = null;
            }

            $validated['masih_bekerja'] = $request->has('masih_bekerja') ? true : false;

            $pengalaman->update($validated);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Pengalaman kerja berhasil diperbarui', 'active_tab' => 'pengalaman-kerja']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui pengalaman kerja: ' . $e->getMessage());
        }
    }

    public function destroy($karyawanHashId, $pengalamanHashId)
    {
        // Decode hash_id to get real id
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        $pengalamanId = \Vinkla\Hashids\Facades\Hashids::decode($pengalamanHashId)[0] ?? null;
        
        if (!$karyawanId || !$pengalamanId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);
        $pengalaman = PengalamanKerja::where('karyawan_id', $karyawan->id)
                                     ->findOrFail($pengalamanId);

        try {
            $pengalaman->delete();

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Pengalaman kerja berhasil dihapus', 'active_tab' => 'pengalaman-kerja']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menghapus pengalaman kerja: ' . $e->getMessage());
        }
    }
}
