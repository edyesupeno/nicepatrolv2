<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\MedicalCheckup;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class MedicalCheckupController extends Controller
{
    public function store(Request $request, $karyawanHashId)
    {
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        
        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);

        $validated = $request->validate([
            'jenis_checkup' => 'required|string|max:255',
            'tanggal_checkup' => 'required|date',
            'status_kesehatan' => 'required|string|max:255',
            'tinggi_badan' => 'nullable|numeric|min:0|max:300',
            'berat_badan' => 'nullable|numeric|min:0|max:500',
            'golongan_darah' => 'nullable|string|max:10',
            'tekanan_darah' => 'nullable|string|max:20',
            'gula_darah' => 'nullable|numeric|min:0|max:1000',
            'kolesterol' => 'nullable|numeric|min:0|max:1000',
            'rumah_sakit' => 'nullable|string|max:255',
            'nama_dokter' => 'nullable|string|max:255',
            'diagnosis' => 'nullable|string',
            'catatan_tambahan' => 'nullable|string',
        ], [
            'jenis_checkup.required' => 'Jenis checkup wajib dipilih',
            'tanggal_checkup.required' => 'Tanggal checkup wajib diisi',
            'tanggal_checkup.date' => 'Format tanggal tidak valid',
            'status_kesehatan.required' => 'Status kesehatan wajib dipilih',
            'tinggi_badan.numeric' => 'Tinggi badan harus berupa angka',
            'berat_badan.numeric' => 'Berat badan harus berupa angka',
            'gula_darah.numeric' => 'Gula darah harus berupa angka',
            'kolesterol.numeric' => 'Kolesterol harus berupa angka',
        ]);

        try {
            $validated['karyawan_id'] = $karyawan->id;
            MedicalCheckup::create($validated);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Data medical checkup berhasil ditambahkan', 'active_tab' => 'medical-checkup']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal menambahkan medical checkup: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $karyawanHashId, $checkupHashId)
    {
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        $checkupId = \Vinkla\Hashids\Facades\Hashids::decode($checkupHashId)[0] ?? null;
        
        if (!$karyawanId || !$checkupId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);
        $checkup = MedicalCheckup::where('karyawan_id', $karyawan->id)->findOrFail($checkupId);

        $validated = $request->validate([
            'jenis_checkup' => 'required|string|max:255',
            'tanggal_checkup' => 'required|date',
            'status_kesehatan' => 'required|string|max:255',
            'tinggi_badan' => 'nullable|numeric|min:0|max:300',
            'berat_badan' => 'nullable|numeric|min:0|max:500',
            'golongan_darah' => 'nullable|string|max:10',
            'tekanan_darah' => 'nullable|string|max:20',
            'gula_darah' => 'nullable|numeric|min:0|max:1000',
            'kolesterol' => 'nullable|numeric|min:0|max:1000',
            'rumah_sakit' => 'nullable|string|max:255',
            'nama_dokter' => 'nullable|string|max:255',
            'diagnosis' => 'nullable|string',
            'catatan_tambahan' => 'nullable|string',
        ], [
            'jenis_checkup.required' => 'Jenis checkup wajib dipilih',
            'tanggal_checkup.required' => 'Tanggal checkup wajib diisi',
            'tanggal_checkup.date' => 'Format tanggal tidak valid',
            'status_kesehatan.required' => 'Status kesehatan wajib dipilih',
            'tinggi_badan.numeric' => 'Tinggi badan harus berupa angka',
            'berat_badan.numeric' => 'Berat badan harus berupa angka',
            'gula_darah.numeric' => 'Gula darah harus berupa angka',
            'kolesterol.numeric' => 'Kolesterol harus berupa angka',
        ]);

        try {
            $checkup->update($validated);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Data medical checkup berhasil diperbarui', 'active_tab' => 'medical-checkup']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal memperbarui medical checkup: ' . $e->getMessage());
        }
    }

    public function destroy($karyawanHashId, $checkupHashId)
    {
        $karyawanId = \Vinkla\Hashids\Facades\Hashids::decode($karyawanHashId)[0] ?? null;
        $checkupId = \Vinkla\Hashids\Facades\Hashids::decode($checkupHashId)[0] ?? null;
        
        if (!$karyawanId || !$checkupId) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($karyawanId);
        $checkup = MedicalCheckup::where('karyawan_id', $karyawan->id)->findOrFail($checkupId);

        try {
            $checkup->delete();

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Data medical checkup berhasil dihapus', 'active_tab' => 'medical-checkup']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menghapus medical checkup: ' . $e->getMessage());
        }
    }
}
