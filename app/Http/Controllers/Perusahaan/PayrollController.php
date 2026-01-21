<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Project;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\TemplateKomponenGaji;
use App\Models\PayrollSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function generate(Request $request)
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Get projects and jabatans for filters
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $jabatans = Jabatan::select('id', 'nama')->orderBy('nama')->get();
        
        // Get payroll setting
        $setting = PayrollSetting::first();
        
        // Get karyawans if project selected
        $karyawans = collect();
        if ($request->has('project_id') && $request->project_id) {
            $query = Karyawan::select('id', 'nik_karyawan', 'nama_lengkap', 'jabatan_id')
                ->where('project_id', $request->project_id)
                ->where('is_active', true);
            
            if ($request->has('jabatan_id') && $request->jabatan_id) {
                $query->where('jabatan_id', $request->jabatan_id);
            }
            
            $karyawans = $query->orderBy('nama_lengkap')->get();
        }
        
        return view('perusahaan.payroll.generate', compact('projects', 'jabatans', 'karyawans', 'setting'));
    }
    
    public function store(Request $request)
    {
        try {
            // Get payroll setting to check if auto generate is enabled
            $payrollSetting = PayrollSetting::first();
            $isAutoGenerate = $payrollSetting && $payrollSetting->periode_auto_generate;
            
            // Conditional validation based on auto generate setting
            if ($isAutoGenerate) {
                $validated = $request->validate([
                    'periode' => 'required|date_format:Y-m',
                    'project_id' => 'required|exists:projects,id',
                    'jabatan_id' => 'nullable|exists:jabatans,id',
                    'karyawan_ids' => 'nullable|array',
                    'karyawan_ids.*' => 'exists:karyawans,id',
                ], [
                    'periode.required' => 'Periode wajib diisi',
                    'periode.date_format' => 'Format periode harus YYYY-MM',
                    'project_id.required' => 'Project wajib dipilih',
                ]);
                
                $periode = $validated['periode'];
                $periodeDate = Carbon::createFromFormat('Y-m', $periode);
                $startDate = $periodeDate->copy()->startOfMonth();
                $endDate = $periodeDate->copy()->endOfMonth();
            } else {
                $validated = $request->validate([
                    'periode_start' => 'required|date',
                    'periode_end' => 'required|date|after_or_equal:periode_start',
                    'project_id' => 'required|exists:projects,id',
                    'jabatan_id' => 'nullable|exists:jabatans,id',
                    'karyawan_ids' => 'nullable|array',
                    'karyawan_ids.*' => 'exists:karyawans,id',
                ], [
                    'periode_start.required' => 'Tanggal mulai periode wajib diisi',
                    'periode_end.required' => 'Tanggal akhir periode wajib diisi',
                    'periode_end.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai',
                    'project_id.required' => 'Project wajib dipilih',
                ]);
                
                // Validate max 31 days
                $startDate = Carbon::parse($validated['periode_start']);
                $endDate = Carbon::parse($validated['periode_end']);
                $diffDays = $startDate->diffInDays($endDate) + 1; // +1 to include both dates
                
                if ($diffDays > 31) {
                    return redirect()->back()
                        ->with('error', "Periode maksimal 31 hari. Periode yang dipilih: {$diffDays} hari")
                        ->withInput();
                }
                
                // Create periode string from dates (use month of start date)
                $periode = $startDate->format('Y-m');
            }
            
            $perusahaanId = auth()->user()->perusahaan_id;
            $projectId = $validated['project_id'];
            
            // Get karyawans to process
            $query = Karyawan::where('project_id', $projectId)
                ->where('is_active', true);
            
            if (!empty($validated['jabatan_id'])) {
                $query->where('jabatan_id', $validated['jabatan_id']);
            }
            
            if (!empty($validated['karyawan_ids'])) {
                $query->whereIn('id', $validated['karyawan_ids']);
            }
            
            $karyawans = $query->get();
            
            if ($karyawans->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada karyawan yang dipilih');
            }
            
            $successCount = 0;
            $skipCount = 0;
            $errors = [];
            
            DB::transaction(function () use ($karyawans, $periode, $startDate, $endDate, $perusahaanId, $payrollSetting, &$successCount, &$skipCount, &$errors) {
                foreach ($karyawans as $karyawan) {
                    try {
                        // Check if payroll already exists
                        $existing = Payroll::where('karyawan_id', $karyawan->id)
                            ->where('periode', $periode)
                            ->first();
                        
                        if ($existing) {
                            $skipCount++;
                            continue;
                        }
                        
                        // Calculate payroll with custom date range
                        $payrollData = $this->calculatePayroll($karyawan, $periode, $startDate, $endDate, $perusahaanId, $payrollSetting);
                        
                        // Create payroll
                        Payroll::create($payrollData);
                        $successCount++;
                        
                    } catch (\Exception $e) {
                        $errors[] = "Karyawan {$karyawan->nama_lengkap}: " . $e->getMessage();
                    }
                }
            });
            
            $message = "Berhasil generate {$successCount} payroll";
            if ($skipCount > 0) {
                $message .= ", {$skipCount} sudah ada (dilewati)";
            }
            if (!empty($errors)) {
                $message .= ". Error: " . implode(', ', $errors);
            }
            
            return redirect()->route('perusahaan.daftar-payroll.index', ['periode' => $periode])
                ->with('success', $message);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()))
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal generate payroll: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    private function calculatePayroll($karyawan, $periode, $startDate, $endDate, $perusahaanId, $payrollSetting)
    {
        // Get kehadiran data based on custom date range
        $kehadirans = Kehadiran::where('karyawan_id', $karyawan->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();
        
        // Count kehadiran - include all statuses that count as "masuk"
        // Also count if jam_masuk is not null (for records without status_kehadiran)
        $hariMasuk = $kehadirans->filter(function($k) {
            return in_array($k->status, ['hadir', 'terlambat', 'pulang_cepat']) 
                || (!empty($k->jam_masuk) && empty($k->status));
        })->count();
        
        $hariAlpha = $kehadirans->where('status', 'alpa')->count();
        $hariSakit = $kehadirans->where('status', 'sakit')->count();
        $hariIzin = $kehadirans->where('status', 'izin')->count();
        $hariCuti = $kehadirans->where('status', 'cuti')->count();
        $hariLembur = $kehadirans->where('lembur', true)->count();
        
        // Calculate hari kerja based on date range (excluding weekends)
        $hariKerja = 0;
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            // Count only weekdays (Monday-Friday)
            if ($currentDate->isWeekday()) {
                $hariKerja++;
            }
            $currentDate->addDay();
        }
        
        // Gaji Pokok
        $gajiPokok = $karyawan->gaji_pokok ?? 0;
        
        // Get template komponen (prioritas: karyawan > jabatan > default)
        $templates = $this->getTemplateKomponen($karyawan);
        
        // Calculate tunjangan
        $tunjanganDetail = [];
        $totalTunjangan = 0;
        
        foreach ($templates as $template) {
            if ($template->komponenPayroll->jenis == 'Tunjangan') {
                $nilai = $this->calculateKomponenNilai(
                    $template->komponenPayroll->tipe_perhitungan,
                    $template->nilai,
                    $gajiPokok,
                    $hariMasuk,
                    $hariLembur
                );
                
                $tunjanganDetail[] = [
                    'kode' => $template->komponenPayroll->kode,
                    'nama' => $template->komponenPayroll->nama_komponen,
                    'tipe' => $template->komponenPayroll->tipe_perhitungan,
                    'nilai_dasar' => $template->nilai,
                    'nilai_hitung' => $nilai,
                ];
                
                $totalTunjangan += $nilai;
            }
        }
        
        // Calculate BPJS (Perusahaan portion)
        $bpjsKesehatan = 0;
        $bpjsKetenagakerjaan = 0;
        
        if ($payrollSetting) {
            // BPJS Kesehatan (perusahaan)
            $bpjsKesehatan = ($gajiPokok * $payrollSetting->bpjs_kesehatan_perusahaan) / 100;
            
            // BPJS Ketenagakerjaan (perusahaan) = JHT + JP + JKK + JKM
            $bpjsJht = ($gajiPokok * $payrollSetting->bpjs_jht_perusahaan) / 100;
            $bpjsJp = ($gajiPokok * $payrollSetting->bpjs_jp_perusahaan) / 100;
            $bpjsJkk = ($gajiPokok * $payrollSetting->bpjs_jkk_perusahaan) / 100;
            $bpjsJkm = ($gajiPokok * $payrollSetting->bpjs_jkm_perusahaan) / 100;
            $bpjsKetenagakerjaan = $bpjsJht + $bpjsJp + $bpjsJkk + $bpjsJkm;
        }
        
        // Calculate potongan
        $potonganDetail = [];
        $totalPotongan = 0;
        
        foreach ($templates as $template) {
            if ($template->komponenPayroll->jenis == 'Potongan') {
                $nilai = $this->calculateKomponenNilai(
                    $template->komponenPayroll->tipe_perhitungan,
                    $template->nilai,
                    $gajiPokok,
                    $hariMasuk,
                    $hariLembur
                );
                
                $potonganDetail[] = [
                    'kode' => $template->komponenPayroll->kode,
                    'nama' => $template->komponenPayroll->nama_komponen,
                    'tipe' => $template->komponenPayroll->tipe_perhitungan,
                    'nilai_dasar' => $template->nilai,
                    'nilai_hitung' => $nilai,
                ];
                
                $totalPotongan += $nilai;
            }
        }
        
        // Add BPJS potongan karyawan
        if ($payrollSetting) {
            // BPJS Kesehatan (karyawan)
            if ($payrollSetting->bpjs_kesehatan_karyawan > 0) {
                $potonganBpjsKes = ($gajiPokok * $payrollSetting->bpjs_kesehatan_karyawan) / 100;
                $potonganDetail[] = [
                    'kode' => 'BPJS_KES_KARYAWAN',
                    'nama' => 'Potongan BPJS Kesehatan',
                    'tipe' => 'Persentase',
                    'nilai_dasar' => $payrollSetting->bpjs_kesehatan_karyawan,
                    'nilai_hitung' => $potonganBpjsKes,
                ];
                $totalPotongan += $potonganBpjsKes;
            }
            
            // BPJS Ketenagakerjaan (karyawan) = JHT + JP
            $potonganBpjsJht = ($gajiPokok * $payrollSetting->bpjs_jht_karyawan) / 100;
            $potonganBpjsJp = ($gajiPokok * $payrollSetting->bpjs_jp_karyawan) / 100;
            $potonganBpjsKer = $potonganBpjsJht + $potonganBpjsJp;
            
            if ($potonganBpjsKer > 0) {
                $potonganDetail[] = [
                    'kode' => 'BPJS_TK_KARYAWAN',
                    'nama' => 'Potongan BPJS Ketenagakerjaan (JHT + JP)',
                    'tipe' => 'Persentase',
                    'nilai_dasar' => $payrollSetting->bpjs_jht_karyawan + $payrollSetting->bpjs_jp_karyawan,
                    'nilai_hitung' => $potonganBpjsKer,
                ];
                $totalPotongan += $potonganBpjsKer;
            }
        }
        
        // Get PTKP status and value from karyawan
        $ptkpStatus = $karyawan->ptkp_status; // TK/0, K/1, dll
        $ptkpValue = $karyawan->ptkp_value; // Nilai PTKP per tahun
        
        // Calculate pajak PPh 21 dengan PTKP
        $gajiKotorSebelumPajak = $gajiPokok + $totalTunjangan + $bpjsKesehatan + $bpjsKetenagakerjaan - $totalPotongan;
        $pajakPph21 = 0;
        
        if ($payrollSetting && $payrollSetting->pph21_bracket1_rate > 0) {
            // Hitung penghasilan bruto per tahun
            $penghasilanBrutoTahunan = $gajiKotorSebelumPajak * 12;
            
            // Hitung penghasilan netto (bruto - biaya jabatan 5%, max 500rb/bulan = 6jt/tahun)
            $biayaJabatan = min($penghasilanBrutoTahunan * 0.05, 6000000);
            $penghasilanNettoTahunan = $penghasilanBrutoTahunan - $biayaJabatan;
            
            // Hitung Penghasilan Kena Pajak (PKP) = Netto - PTKP
            $pkp = max(0, $penghasilanNettoTahunan - $ptkpValue);
            
            // Bulatkan ke bawah ribuan
            $pkp = floor($pkp / 1000) * 1000;
            
            // Hitung PPh 21 dengan tarif progresif
            $pph21Tahunan = 0;
            
            if ($pkp > 0) {
                // Layer 1: 0 - 60 juta (5%)
                if ($pkp <= 60000000) {
                    $pph21Tahunan = $pkp * ($payrollSetting->pph21_bracket1_rate / 100);
                } 
                // Layer 2: 60 - 250 juta (15%)
                else if ($pkp <= 250000000) {
                    $pph21Tahunan = (60000000 * ($payrollSetting->pph21_bracket1_rate / 100)) +
                                   (($pkp - 60000000) * ($payrollSetting->pph21_bracket2_rate / 100));
                }
                // Layer 3: 250 - 500 juta (25%)
                else if ($pkp <= 500000000) {
                    $pph21Tahunan = (60000000 * ($payrollSetting->pph21_bracket1_rate / 100)) +
                                   (190000000 * ($payrollSetting->pph21_bracket2_rate / 100)) +
                                   (($pkp - 250000000) * ($payrollSetting->pph21_bracket3_rate / 100));
                }
                // Layer 4: 500 juta - 5 miliar (30%)
                else if ($pkp <= 5000000000) {
                    $pph21Tahunan = (60000000 * ($payrollSetting->pph21_bracket1_rate / 100)) +
                                   (190000000 * ($payrollSetting->pph21_bracket2_rate / 100)) +
                                   (250000000 * ($payrollSetting->pph21_bracket3_rate / 100)) +
                                   (($pkp - 500000000) * ($payrollSetting->pph21_bracket4_rate / 100));
                }
                // Layer 5: > 5 miliar (35%)
                else {
                    $pph21Tahunan = (60000000 * ($payrollSetting->pph21_bracket1_rate / 100)) +
                                   (190000000 * ($payrollSetting->pph21_bracket2_rate / 100)) +
                                   (250000000 * ($payrollSetting->pph21_bracket3_rate / 100)) +
                                   (4500000000 * ($payrollSetting->pph21_bracket4_rate / 100)) +
                                   (($pkp - 5000000000) * ($payrollSetting->pph21_bracket5_rate / 100));
                }
            }
            
            // PPh 21 per bulan
            $pajakPph21 = $pph21Tahunan / 12;
        }
        
        // Calculate totals
        $gajiBruto = $gajiPokok + $totalTunjangan + $bpjsKesehatan + $bpjsKetenagakerjaan;
        $gajiNetto = $gajiBruto - $totalPotongan - $pajakPph21;
        
        return [
            'perusahaan_id' => $perusahaanId,
            'project_id' => $karyawan->project_id,
            'karyawan_id' => $karyawan->id,
            'periode' => $periode,
            'periode_start' => $startDate->format('Y-m-d'),
            'periode_end' => $endDate->format('Y-m-d'),
            'tanggal_generate' => now(),
            'gaji_pokok' => $gajiPokok,
            'hari_kerja' => $hariKerja,
            'hari_masuk' => $hariMasuk,
            'hari_alpha' => $hariAlpha,
            'hari_sakit' => $hariSakit,
            'hari_izin' => $hariIzin,
            'hari_cuti' => $hariCuti,
            'hari_lembur' => $hariLembur,
            'ptkp_status' => $ptkpStatus,
            'ptkp_value' => $ptkpValue,
            'tunjangan_detail' => $tunjanganDetail,
            'total_tunjangan' => $totalTunjangan,
            'bpjs_kesehatan' => $bpjsKesehatan,
            'bpjs_ketenagakerjaan' => $bpjsKetenagakerjaan,
            'potongan_detail' => $potonganDetail,
            'total_potongan' => $totalPotongan,
            'pajak_pph21' => $pajakPph21,
            'gaji_bruto' => $gajiBruto,
            'gaji_netto' => $gajiNetto,
            'status' => 'draft',
        ];
    }
    
    private function getTemplateKomponen($karyawan)
    {
        // Priority: karyawan > jabatan > default
        
        // 1. Check karyawan template
        $karyawanTemplate = TemplateKomponenGaji::with('komponenPayroll')
            ->where('karyawan_id', $karyawan->id)
            ->where('aktif', true)
            ->get();
        
        if ($karyawanTemplate->isNotEmpty()) {
            return $karyawanTemplate;
        }
        
        // 2. Check jabatan template
        $jabatanTemplate = TemplateKomponenGaji::with('komponenPayroll')
            ->where('project_id', $karyawan->project_id)
            ->where('jabatan_id', $karyawan->jabatan_id)
            ->whereNull('karyawan_id')
            ->where('aktif', true)
            ->get();
        
        if ($jabatanTemplate->isNotEmpty()) {
            return $jabatanTemplate;
        }
        
        // 3. Return empty (will use default: gaji pokok + BPJS only)
        return collect();
    }
    
    private function calculateKomponenNilai($tipe, $nilaiDasar, $gajiPokok, $hariMasuk, $hariLembur)
    {
        switch ($tipe) {
            case 'Tetap':
                return $nilaiDasar;
            
            case 'Persentase':
                return ($gajiPokok * $nilaiDasar) / 100;
            
            case 'Per Hari Masuk':
                return $nilaiDasar * $hariMasuk;
            
            case 'Lembur Per Hari':
                return $nilaiDasar * $hariLembur;
            
            default:
                return 0;
        }
    }
}
