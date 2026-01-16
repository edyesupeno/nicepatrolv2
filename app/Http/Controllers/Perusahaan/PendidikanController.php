<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Pendidikan;
use Illuminate\Http\Request;

class PendidikanController extends Controller
{
    public function store(Request $request, $karyawanHashId)
    {
        // Decode hash_id to get real id
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        
        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);

        $validated = $request->validate([
            'jenjang_pendidikan' => 'required|string',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'ipk' => 'nullable|string|max:10',
            'tahun_mulai' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'tahun_selesai' => 'required|integer|min:1900|max:' . (date('Y') + 10) . '|gte:tahun_mulai',
        ], [
            'jenjang_pendidikan.required' => 'Jenjang pendidikan wajib dipilih',
            'nama_institusi.required' => 'Nama institusi wajib diisi',
            'tahun_mulai.required' => 'Tahun mulai wajib diisi',
            'tahun_mulai.integer' => 'Tahun mulai harus berupa angka',
            'tahun_mulai.min' => 'Tahun mulai tidak valid',
            'tahun_mulai.max' => 'Tahun mulai tidak valid',
            'tahun_selesai.required' => 'Tahun selesai wajib diisi',
            'tahun_selesai.integer' => 'Tahun selesai harus berupa angka',
            'tahun_selesai.min' => 'Tahun selesai tidak valid',
            'tahun_selesai.max' => 'Tahun selesai tidak valid',
            'tahun_selesai.gte' => 'Tahun selesai harus sama atau setelah tahun mulai',
        ]);

        try {
            $validated['karyawan_id'] = $karyawan->id;
            Pendidikan::create($validated);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Pendidikan berhasil ditambahkan', 'active_tab' => 'pendidikan']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menambahkan pendidikan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $karyawanHashId, $pendidikanHashId)
    {
        // Decode hash_id to get real id
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        $pendidikanId = \Vinkla\Hashids\Facades\Hashids::decode($pendidikanHashId)[0] ?? null;
        
        if (!$karyawanId || !$pendidikanId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);
        $pendidikan = Pendidikan::where('karyawan_id', $karyawan->id)
                                ->findOrFail($pendidikanId);

        $validated = $request->validate([
            'jenjang_pendidikan' => 'required|string',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'ipk' => 'nullable|string|max:10',
            'tahun_mulai' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'tahun_selesai' => 'required|integer|min:1900|max:' . (date('Y') + 10) . '|gte:tahun_mulai',
        ], [
            'jenjang_pendidikan.required' => 'Jenjang pendidikan wajib dipilih',
            'nama_institusi.required' => 'Nama institusi wajib diisi',
            'tahun_mulai.required' => 'Tahun mulai wajib diisi',
            'tahun_mulai.integer' => 'Tahun mulai harus berupa angka',
            'tahun_mulai.min' => 'Tahun mulai tidak valid',
            'tahun_mulai.max' => 'Tahun mulai tidak valid',
            'tahun_selesai.required' => 'Tahun selesai wajib diisi',
            'tahun_selesai.integer' => 'Tahun selesai harus berupa angka',
            'tahun_selesai.min' => 'Tahun selesai tidak valid',
            'tahun_selesai.max' => 'Tahun selesai tidak valid',
            'tahun_selesai.gte' => 'Tahun selesai harus sama atau setelah tahun mulai',
        ]);

        try {
            $pendidikan->update($validated);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Pendidikan berhasil diperbarui', 'active_tab' => 'pendidikan']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui pendidikan: ' . $e->getMessage());
        }
    }

    public function destroy($karyawanHashId, $pendidikanHashId)
    {
        // Decode hash_id to get real id
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        $pendidikanId = \Vinkla\Hashids\Facades\Hashids::decode($pendidikanHashId)[0] ?? null;
        
        if (!$karyawanId || !$pendidikanId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);
        $pendidikan = Pendidikan::where('karyawan_id', $karyawan->id)
                                ->findOrFail($pendidikanId);

        try {
            $pendidikan->delete();

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Pendidikan berhasil dihapus', 'active_tab' => 'pendidikan']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menghapus pendidikan: ' . $e->getMessage());
        }
    }
}
