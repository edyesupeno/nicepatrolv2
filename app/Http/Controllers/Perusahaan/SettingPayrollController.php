<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\PayrollSetting;
use Illuminate\Http\Request;

class SettingPayrollController extends Controller
{
    public function index()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Get or create setting
        $setting = PayrollSetting::firstOrCreate(
            ['perusahaan_id' => $perusahaanId],
            [
                // Default values sudah di migration
            ]
        );
        
        return view('perusahaan.payroll.setting', compact('setting'));
    }
    
    public function update(Request $request)
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Check if periode_auto_generate is enabled
        $periodeAutoGenerate = $request->has('periode_auto_generate');
        
        $rules = [
            // BPJS
            'bpjs_kesehatan_perusahaan' => 'required|numeric|min:0|max:100',
            'bpjs_kesehatan_karyawan' => 'required|numeric|min:0|max:100',
            'bpjs_jht_perusahaan' => 'required|numeric|min:0|max:100',
            'bpjs_jht_karyawan' => 'required|numeric|min:0|max:100',
            'bpjs_jp_perusahaan' => 'required|numeric|min:0|max:100',
            'bpjs_jp_karyawan' => 'required|numeric|min:0|max:100',
            'bpjs_jkk_perusahaan' => 'required|numeric|min:0|max:100',
            'bpjs_jkm_perusahaan' => 'required|numeric|min:0|max:100',
            // PPh 21
            'pph21_bracket1_rate' => 'required|numeric|min:0|max:100',
            'pph21_bracket2_rate' => 'required|numeric|min:0|max:100',
            'pph21_bracket3_rate' => 'required|numeric|min:0|max:100',
            'pph21_bracket4_rate' => 'required|numeric|min:0|max:100',
            'pph21_bracket5_rate' => 'required|numeric|min:0|max:100',
            // PTKP
            'ptkp_tk0' => 'required|numeric|min:0',
            'ptkp_tk1' => 'required|numeric|min:0',
            'ptkp_tk2' => 'required|numeric|min:0',
            'ptkp_tk3' => 'required|numeric|min:0',
            'ptkp_k0' => 'required|numeric|min:0',
            'ptkp_k1' => 'required|numeric|min:0',
            'ptkp_k2' => 'required|numeric|min:0',
            'ptkp_k3' => 'required|numeric|min:0',
            // Lembur
            'lembur_hari_kerja' => 'required|numeric|min:0',
            'lembur_akhir_pekan' => 'required|numeric|min:0',
            'lembur_hari_libur' => 'required|numeric|min:0',
            'lembur_max_jam_per_hari' => 'required|integer|min:0',
            // Periode - conditional required
            'periode_cutoff_tanggal' => $periodeAutoGenerate ? 'required|integer|min:1|max:31' : 'nullable|integer|min:1|max:31',
            'periode_pembayaran_tanggal' => $periodeAutoGenerate ? 'required|integer|min:1|max:31' : 'nullable|integer|min:1|max:31',
            'periode_auto_generate' => 'boolean',
        ];
        
        $messages = [
            'required' => ':attribute wajib diisi',
            'numeric' => ':attribute harus berupa angka',
            'integer' => ':attribute harus berupa bilangan bulat',
            'min' => ':attribute minimal :min',
            'max' => ':attribute maksimal :max',
        ];
        
        $validated = $request->validate($rules, $messages);
        
        $validated['perusahaan_id'] = $perusahaanId;
        $validated['periode_auto_generate'] = $periodeAutoGenerate;
        
        // Set default values if periode_auto_generate is false
        if (!$periodeAutoGenerate) {
            $validated['periode_cutoff_tanggal'] = 25;
            $validated['periode_pembayaran_tanggal'] = 1;
        }
        
        PayrollSetting::updateOrCreate(
            ['perusahaan_id' => $perusahaanId],
            $validated
        );
        
        return back()->with('success', 'Pengaturan payroll berhasil disimpan');
    }
}
