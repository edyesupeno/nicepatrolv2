<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Project;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DaftarPayrollController extends Controller
{
    public function index(Request $request)
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Get filters
        $periode = $request->get('periode', now()->format('Y-m'));
        $projectId = $request->get('project_id');
        $jabatanId = $request->get('jabatan_id');
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        
        // Get projects and jabatans for filters (optimized)
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $jabatans = Jabatan::select('id', 'nama')->orderBy('nama')->get();
        
        // Query payrolls (optimized with specific columns)
        $query = Payroll::select([
                'id',
                'perusahaan_id',
                'karyawan_id',
                'project_id',
                'periode',
                'periode_start',
                'periode_end',
                'gaji_pokok',
                'gaji_bruto',
                'gaji_netto',
                // Kehadiran columns
                'hari_kerja',
                'hari_masuk',
                'hari_alpha',
                'hari_sakit',
                'hari_izin',
                'hari_cuti',
                'hari_lembur',
                // Status & approval
                'status',
                'approved_by',
                'approved_at',
                'paid_by',
                'paid_at',
                'created_at'
            ])
            ->with([
                'karyawan:id,nik_karyawan,nama_lengkap,jabatan_id',
                'karyawan.jabatan:id,nama',
                'project:id,nama'
            ])
            ->where('periode', $periode);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $query->whereHas('karyawan', function($q) use ($jabatanId) {
                $q->where('jabatan_id', $jabatanId);
            });
        }
        
        if ($status != 'all') {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama_lengkap', 'ilike', "%{$search}%")
                  ->orWhere('nik_karyawan', 'ilike', "%{$search}%");
            });
        }
        
        $payrolls = $query->orderBy('created_at', 'desc')
            ->paginate(50)
            ->withQueryString();
        
        // Get statistics (optimized with single query)
        $stats = $this->getStatistics($periode, $projectId, $jabatanId);
        
        return view('perusahaan.payroll.daftar', compact(
            'payrolls',
            'projects',
            'jabatans',
            'periode',
            'projectId',
            'jabatanId',
            'status',
            'search',
            'stats'
        ));
    }
    
    public function show(Payroll $payroll)
    {
        $payroll->load([
            'karyawan:id,nik_karyawan,nama_lengkap,jabatan_id,gaji_pokok',
            'karyawan.jabatan:id,nama',
            'project:id,nama',
            'approvedBy:id,name',
            'paidBy:id,name'
        ]);
        
        // Get payroll settings for BPJS percentage display
        $payrollSetting = \App\Models\PayrollSetting::first();
        
        // Get fixed allowances for this employee (same logic as in LemburController)
        $fixedAllowances = $this->getEmployeeFixedAllowances($payroll->karyawan);
        $totalFixedAllowances = array_sum(array_column($fixedAllowances, 'nilai'));
        $upahTetap = $payroll->gaji_pokok + $totalFixedAllowances;
        
        // Filter tunjangan_detail to show Variable category components from database
        $variableTunjangan = [];
        $totalVariableTunjangan = 0;
        
        // Get Variable components that should be included based on templates
        $expectedVariableComponents = $this->getExpectedVariableComponents($payroll->karyawan);
        
        // Calculate overtime allowance for this period
        $overtimeData = $this->calculateOvertimeAllowance($payroll->karyawan, $payroll->periode);
        
        // Always add Upah Lembur as a default variable component
        $expectedVariableComponents['upah_lembur'] = [
            'id' => 'upah_lembur',
            'nama' => 'Upah Lembur',
            'kode' => 'UPAH_LEMBUR',
            'nilai' => $overtimeData['total_upah'],
            'tipe' => 'Otomatis',
            'source' => 'overtime_calculation',
            'detail' => $overtimeData['total_upah'] > 0 ? 
                "Lembur {$overtimeData['jumlah_hari']} hari ({$overtimeData['total_jam']} jam)" : 
                "Tidak ada lembur yang disetujui"
        ];
        
        // First, process existing tunjangan_detail
        if ($payroll->tunjangan_detail && count($payroll->tunjangan_detail) > 0) {
            foreach ($payroll->tunjangan_detail as $index => $tunjangan) {
                // Check component category from database
                $componentCode = $tunjangan['kode'] ?? $tunjangan['nama'];
                $komponenPayroll = \App\Models\KomponenPayroll::where(function($q) use ($componentCode, $tunjangan) {
                    $q->where('kode', $componentCode)
                      ->orWhere('nama_komponen', $componentCode)
                      ->orWhere('nama_komponen', $tunjangan['nama']);
                })->first();
                
                $isVariable = false;
                if ($komponenPayroll) {
                    // Check if kategori is Variable
                    $isVariable = ($komponenPayroll->kategori === 'Variable');
                } else {
                    // If not found in master, check by name pattern (fallback)
                    $tunjanganNameLower = strtolower(trim($tunjangan['nama']));
                    // Common variable allowances patterns
                    $variablePatterns = ['uang makan', 'transport', 'insentif', 'bonus', 'komisi', 'makan'];
                    foreach ($variablePatterns as $pattern) {
                        if (strpos($tunjanganNameLower, $pattern) !== false) {
                            $isVariable = true;
                            break;
                        }
                    }
                }
                
                if ($isVariable) {
                    $variableTunjangan[] = array_merge($tunjangan, ['index' => $index, 'source' => 'payroll_data']);
                    $totalVariableTunjangan += $tunjangan['nilai_hitung'];
                }
            }
        }
        
        // Then, add missing Variable components from templates that are not in payroll yet
        foreach ($expectedVariableComponents as $expectedComponent) {
            // Check if this component is already in variableTunjangan
            $alreadyExists = false;
            foreach ($variableTunjangan as $existing) {
                if (strtolower(trim($existing['nama'])) === strtolower(trim($expectedComponent['nama']))) {
                    $alreadyExists = true;
                    break;
                }
            }
            
            if (!$alreadyExists) {
                $componentData = [
                    'nama' => $expectedComponent['nama'],
                    'kode' => $expectedComponent['kode'] ?? 'TEMPLATE',
                    'tipe' => $expectedComponent['tipe'] ?? 'Tetap',
                    'nilai_dasar' => $expectedComponent['nilai'],
                    'nilai_hitung' => $expectedComponent['nilai'],
                    'index' => 'template_' . $expectedComponent['id'],
                    'source' => $expectedComponent['source']
                ];
                
                // Add detail for overtime component
                if (isset($expectedComponent['detail'])) {
                    $componentData['detail'] = $expectedComponent['detail'];
                }
                
                $variableTunjangan[] = $componentData;
                $totalVariableTunjangan += $expectedComponent['nilai'];
            }
        }
        
        // Get kehadiran data based on periode_start and periode_end
        // If periode_start/end not set, fallback to periode month
        if ($payroll->periode_start && $payroll->periode_end) {
            $startDate = $payroll->periode_start;
            $endDate = $payroll->periode_end;
        } else {
            $periodeDate = \Carbon\Carbon::createFromFormat('Y-m', $payroll->periode);
            $startDate = $periodeDate->copy()->startOfMonth();
            $endDate = $periodeDate->copy()->endOfMonth();
        }
        
        $kehadirans = \App\Models\Kehadiran::select([
                'id',
                'karyawan_id',
                'tanggal',
                'status',
                'jam_masuk',
                'jam_keluar',
                'durasi_kerja',
                'keterangan'
            ])
            ->where('karyawan_id', $payroll->karyawan_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();
        
        // Get komponen payroll data for editing capability check
        $komponenPayrolls = \App\Models\KomponenPayroll::select(['id', 'kode', 'nama_komponen', 'boleh_edit'])
            ->where('aktif', true)
            ->get()
            ->keyBy('kode');
        
        return view('perusahaan.payroll.detail', compact('payroll', 'kehadirans', 'komponenPayrolls', 'payrollSetting', 'fixedAllowances', 'totalFixedAllowances', 'upahTetap', 'variableTunjangan', 'totalVariableTunjangan'));
    }
    
    public function approve(Request $request, Payroll $payroll)
    {
        try {
            if ($payroll->status != 'draft') {
                return redirect()->back()->with('error', 'Hanya payroll dengan status draft yang bisa di-approve');
            }
            
            $payroll->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            
            return redirect()->back()->with('success', 'Payroll berhasil di-approve');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal approve payroll: ' . $e->getMessage());
        }
    }
    
    public function bulkApprove(Request $request)
    {
        try {
            $validated = $request->validate([
                'payroll_ids' => 'required|array|min:1',
                'payroll_ids.*' => 'exists:payrolls,id',
            ]);
            
            $count = Payroll::whereIn('id', $validated['payroll_ids'])
                ->where('status', 'draft')
                ->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            
            return redirect()->back()->with('success', "Berhasil approve {$count} payroll");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal bulk approve: ' . $e->getMessage());
        }
    }
    
    public function destroy(Payroll $payroll)
    {
        try {
            if ($payroll->status != 'draft') {
                return redirect()->back()->with('error', 'Hanya payroll dengan status draft yang bisa dihapus');
            }
            
            $payroll->delete();
            
            return redirect()->back()->with('success', 'Payroll berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus payroll: ' . $e->getMessage());
        }
    }
    
    public function updateComponent(Request $request, Payroll $payroll)
    {
        try {
            // Only allow editing if payroll is still draft
            if ($payroll->status != 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya payroll dengan status draft yang bisa diedit'
                ], 400);
            }
            
            $validated = $request->validate([
                'component_type' => 'required|in:tunjangan,potongan',
                'component_code' => 'required|string',
                'component_index' => 'nullable|integer|min:0',
                'new_value' => 'required|numeric|min:0',
            ]);
            
            $componentType = $validated['component_type'];
            $componentCode = $validated['component_code'];
            $componentIndex = $validated['component_index'] ?? null;
            $newValue = (float) $validated['new_value'];
            
            // Get current component details
            $detailField = $componentType . '_detail';
            $totalField = 'total_' . $componentType;
            $currentDetails = $payroll->$detailField ?? [];
            
            // Find and update the specific component
            $componentFound = false;
            $oldValue = 0;
            
            foreach ($currentDetails as $index => &$component) {
                $matchFound = false;
                
                // Try to match by kode first (for new payrolls)
                if (isset($component['kode']) && $component['kode'] === $componentCode) {
                    $matchFound = true;
                }
                // Fallback to match by nama (for existing payrolls without kode)
                else if (!isset($component['kode']) && $component['nama'] === $componentCode) {
                    $matchFound = true;
                }
                // Fallback to match by index if provided
                else if ($componentIndex !== null && $index == $componentIndex) {
                    $matchFound = true;
                }
                
                if ($matchFound) {
                    // Check if this component is editable
                    // Use kode if available, otherwise use nama
                    $lookupKey = $component['kode'] ?? $component['nama'];
                    $komponenPayroll = \App\Models\KomponenPayroll::where('kode', $lookupKey)
                        ->orWhere('nama_komponen', $lookupKey)
                        ->first();
                        
                    if (!$komponenPayroll || !$komponenPayroll->boleh_edit) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Komponen ini tidak bisa diedit'
                        ], 400);
                    }
                    
                    $oldValue = $component['nilai_hitung'];
                    $component['nilai_hitung'] = $newValue;
                    $componentFound = true;
                    break;
                }
            }
            
            if (!$componentFound) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komponen tidak ditemukan'
                ], 404);
            }
            
            // Recalculate totals
            $newTotal = array_sum(array_column($currentDetails, 'nilai_hitung'));
            $totalDifference = $newValue - $oldValue;
            
            // Update payroll data
            $updateData = [
                $detailField => $currentDetails,
                $totalField => $newTotal,
            ];
            
            // Recalculate gaji_bruto and gaji_netto using upah tetap calculation
            if ($componentType === 'tunjangan') {
                // Get fixed allowances for upah tetap calculation
                $fixedAllowances = $this->getEmployeeFixedAllowances($payroll->karyawan);
                $totalFixedAllowances = array_sum(array_column($fixedAllowances, 'nilai'));
                $upahTetap = $payroll->gaji_pokok + $totalFixedAllowances;
                
                // Get payroll setting for BPJS calculation
                $payrollSetting = \App\Models\PayrollSetting::first();
                
                // Calculate BPJS based on upah tetap
                $bpjsKesehatanCalculated = $payrollSetting ? ($upahTetap * $payrollSetting->bpjs_kesehatan_perusahaan) / 100 : $payroll->bpjs_kesehatan;
                $bpjsKetenagakerjaanCalculated = $payrollSetting ? 
                    ($upahTetap * ($payrollSetting->bpjs_jht_perusahaan + $payrollSetting->bpjs_jp_perusahaan + $payrollSetting->bpjs_jkk_perusahaan + $payrollSetting->bpjs_jkm_perusahaan)) / 100 : 
                    $payroll->bpjs_ketenagakerjaan;
                
                // Recalculate gaji_bruto: upah_tetap + total_tunjangan_variable + bpjs_calculated
                $updateData['gaji_bruto'] = $upahTetap + $newTotal + $bpjsKesehatanCalculated + $bpjsKetenagakerjaanCalculated;
                
                // Calculate total potongan including BPJS employee + BPJS perusahaan
                $bpjsKaryawanKesehatan = $payrollSetting ? ($upahTetap * $payrollSetting->bpjs_kesehatan_karyawan) / 100 : 0;
                $bpjsKaryawanKetenagakerjaan = $payrollSetting ? ($upahTetap * ($payrollSetting->bpjs_jht_karyawan + $payrollSetting->bpjs_jp_karyawan)) / 100 : 0;
                $totalPotonganRecalculated = $payroll->total_potongan + $bpjsKesehatanCalculated + $bpjsKetenagakerjaanCalculated - ($payroll->bpjs_kesehatan + $payroll->bpjs_ketenagakerjaan);
                
                // Recalculate gaji_netto: gaji_bruto - total_potongan_recalculated - pajak
                $updateData['gaji_netto'] = $updateData['gaji_bruto'] - $totalPotonganRecalculated - $payroll->pajak_pph21;
            } else { // potongan
                // For potongan changes, gaji bruto stays the same but recalculate with upah tetap
                $fixedAllowances = $this->getEmployeeFixedAllowances($payroll->karyawan);
                $totalFixedAllowances = array_sum(array_column($fixedAllowances, 'nilai'));
                $upahTetap = $payroll->gaji_pokok + $totalFixedAllowances;
                
                $payrollSetting = \App\Models\PayrollSetting::first();
                $bpjsKesehatanCalculated = $payrollSetting ? ($upahTetap * $payrollSetting->bpjs_kesehatan_perusahaan) / 100 : $payroll->bpjs_kesehatan;
                $bpjsKetenagakerjaanCalculated = $payrollSetting ? 
                    ($upahTetap * ($payrollSetting->bpjs_jht_perusahaan + $payrollSetting->bpjs_jp_perusahaan + $payrollSetting->bpjs_jkk_perusahaan + $payrollSetting->bpjs_jkm_perusahaan)) / 100 : 
                    $payroll->bpjs_ketenagakerjaan;
                
                // Gaji bruto with upah tetap calculation
                $totalTunjangan = $payroll->total_tunjangan;
                $gajiBrutoRecalculated = $upahTetap + $totalTunjangan + $bpjsKesehatanCalculated + $bpjsKetenagakerjaanCalculated;
                
                // Total potongan includes BPJS perusahaan
                $totalPotonganRecalculated = $newTotal + $bpjsKesehatanCalculated + $bpjsKetenagakerjaanCalculated;
                
                // Only recalculate gaji_netto: gaji_bruto - total_potongan_recalculated - pajak
                $updateData['gaji_netto'] = $gajiBrutoRecalculated - $totalPotonganRecalculated - $payroll->pajak_pph21;
                $updateData['gaji_bruto'] = $gajiBrutoRecalculated; // Update gaji bruto to show correct value
            }
            
            $payroll->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Komponen berhasil diupdate',
                'data' => [
                    'new_value' => $newValue,
                    'new_value_formatted' => 'Rp ' . number_format($newValue, 0, ',', '.'),
                    'new_total' => $newTotal,
                    'new_total_formatted' => 'Rp ' . number_format($newTotal, 0, ',', '.'),
                    'new_gaji_bruto' => $updateData['gaji_bruto'] ?? $payroll->gaji_bruto,
                    'new_gaji_bruto_formatted' => 'Rp ' . number_format($updateData['gaji_bruto'] ?? $payroll->gaji_bruto, 0, ',', '.'),
                    'new_gaji_netto' => $updateData['gaji_netto'],
                    'new_gaji_netto_formatted' => 'Rp ' . number_format($updateData['gaji_netto'], 0, ',', '.'),
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update komponen: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function getStatistics($periode, $projectId = null, $jabatanId = null)
    {
        $query = Payroll::select([
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(gaji_netto) as total_netto'),
                DB::raw('SUM(gaji_bruto) as total_bruto')
            ])
            ->where('periode', $periode);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $query->whereHas('karyawan', function($q) use ($jabatanId) {
                $q->where('jabatan_id', $jabatanId);
            });
        }
        
        // Get grouped statistics in single query
        $grouped = $query->groupBy('status')->get();
        
        // Initialize stats
        $stats = [
            'total' => 0,
            'draft' => 0,
            'approved' => 0,
            'paid' => 0,
            'total_gaji_netto' => 0,
            'total_gaji_bruto' => 0,
        ];
        
        // Process grouped results
        foreach ($grouped as $item) {
            $stats['total'] += $item->count;
            $stats['total_gaji_netto'] += $item->total_netto ?? 0;
            $stats['total_gaji_bruto'] += $item->total_bruto ?? 0;
            
            if ($item->status == 'draft') {
                $stats['draft'] = $item->count;
            } elseif ($item->status == 'approved') {
                $stats['approved'] = $item->count;
            } elseif ($item->status == 'paid') {
                $stats['paid'] = $item->count;
            }
        }
        
        return $stats;
    }
    
    /**
     * Get all fixed allowances for an employee
     */
    private function getEmployeeFixedAllowances($karyawan)
    {
        $allowances = [];
        
        // Get employee-specific allowance templates (highest priority)
        $employeeTemplates = \App\Models\TemplateKomponenGaji::with('komponenPayroll')
            ->where('karyawan_id', $karyawan->id)
            ->where('aktif', true)
            ->whereHas('komponenPayroll', function($q) {
                $q->where('jenis', 'Tunjangan')
                  ->where('kategori', 'Fixed')
                  ->where('aktif', true);
            })
            ->get();
            
        foreach ($employeeTemplates as $template) {
            $allowances[$template->komponen_payroll_id] = [
                'nama' => $template->komponenPayroll->nama_komponen,
                'nilai' => $template->nilai,
                'source' => 'employee_specific'
            ];
        }
        
        // Get jabatan-specific allowances (if not overridden by employee-specific)
        if ($karyawan->jabatan_id) {
            $jabatanTemplates = \App\Models\TemplateKomponenGaji::with('komponenPayroll')
                ->where('jabatan_id', $karyawan->jabatan_id)
                ->where('aktif', true)
                ->whereHas('komponenPayroll', function($q) {
                    $q->where('jenis', 'Tunjangan')
                      ->where('kategori', 'Fixed')
                      ->where('aktif', true);
                })
                ->get();
                
            foreach ($jabatanTemplates as $template) {
                // Only add if not already set by employee-specific template
                if (!isset($allowances[$template->komponen_payroll_id])) {
                    $allowances[$template->komponen_payroll_id] = [
                        'nama' => $template->komponenPayroll->nama_komponen,
                        'nilai' => $template->nilai,
                        'source' => 'jabatan_specific'
                    ];
                }
            }
        }
        
        // Get project-specific allowances (if not overridden)
        if ($karyawan->project_id) {
            $projectTemplates = \App\Models\TemplateKomponenGaji::with('komponenPayroll')
                ->where('project_id', $karyawan->project_id)
                ->where('aktif', true)
                ->whereHas('komponenPayroll', function($q) {
                    $q->where('jenis', 'Tunjangan')
                      ->where('kategori', 'Fixed')
                      ->where('aktif', true);
                })
                ->get();
                
            foreach ($projectTemplates as $template) {
                // Only add if not already set by higher priority templates
                if (!isset($allowances[$template->komponen_payroll_id])) {
                    $allowances[$template->komponen_payroll_id] = [
                        'nama' => $template->komponenPayroll->nama_komponen,
                        'nilai' => $template->nilai,
                        'source' => 'project_specific'
                    ];
                }
            }
        }
        
        // Get default/general allowances (lowest priority)
        $defaultTemplates = \App\Models\TemplateKomponenGaji::with('komponenPayroll')
            ->whereNull('karyawan_id')
            ->whereNull('jabatan_id')
            ->whereNull('project_id')
            ->where('aktif', true)
            ->whereHas('komponenPayroll', function($q) {
                $q->where('jenis', 'Tunjangan')
                  ->where('kategori', 'Fixed')
                  ->where('aktif', true);
            })
            ->get();
            
        foreach ($defaultTemplates as $template) {
            // Only add if not already set by higher priority templates
            if (!isset($allowances[$template->komponen_payroll_id])) {
                $allowances[$template->komponen_payroll_id] = [
                    'nama' => $template->komponenPayroll->nama_komponen,
                    'nilai' => $template->nilai,
                    'source' => 'default'
                ];
            }
        }
        
        return $allowances;
    }
    
    /**
     * Calculate overtime allowance for an employee in a specific period
     */
    private function calculateOvertimeAllowance($karyawan, $periode)
    {
        // Parse periode to get start and end dates
        $periodeDate = \Carbon\Carbon::createFromFormat('Y-m', $periode);
        $startDate = $periodeDate->copy()->startOfMonth();
        $endDate = $periodeDate->copy()->endOfMonth();
        
        // Get approved overtime records for this employee in this period
        $approvedLemburs = \App\Models\Lembur::where('karyawan_id', $karyawan->id)
            ->where('status', 'approved')
            ->whereBetween('tanggal_lembur', [$startDate, $endDate])
            ->get();
        
        $totalUpahLembur = 0;
        $totalJamLembur = 0;
        
        foreach ($approvedLemburs as $lembur) {
            $totalUpahLembur += $lembur->total_upah_lembur ?? 0;
            $totalJamLembur += $lembur->total_jam ?? 0;
        }
        
        return [
            'total_upah' => $totalUpahLembur,
            'total_jam' => $totalJamLembur,
            'jumlah_hari' => $approvedLemburs->count()
        ];
    }

    /**
     * Get expected Variable components based on templates for an employee
     */
    private function getExpectedVariableComponents($karyawan)
    {
        $components = [];
        
        // Get employee-specific Variable templates (highest priority)
        $employeeTemplates = \App\Models\TemplateKomponenGaji::with('komponenPayroll')
            ->where('karyawan_id', $karyawan->id)
            ->where('aktif', true)
            ->whereHas('komponenPayroll', function($q) {
                $q->where('jenis', 'Tunjangan')
                  ->where('kategori', 'Variable')
                  ->where('aktif', true);
            })
            ->get();
            
        foreach ($employeeTemplates as $template) {
            $components[$template->komponen_payroll_id] = [
                'id' => $template->id,
                'nama' => $template->komponenPayroll->nama_komponen,
                'kode' => $template->komponenPayroll->kode,
                'nilai' => $template->nilai,
                'tipe' => $template->komponenPayroll->tipe_perhitungan ?? 'Tetap',
                'source' => 'employee_specific'
            ];
        }
        
        // Get jabatan-specific Variable templates (if not overridden by employee-specific)
        if ($karyawan->jabatan_id) {
            $jabatanTemplates = \App\Models\TemplateKomponenGaji::with('komponenPayroll')
                ->where('jabatan_id', $karyawan->jabatan_id)
                ->where('aktif', true)
                ->whereHas('komponenPayroll', function($q) {
                    $q->where('jenis', 'Tunjangan')
                      ->where('kategori', 'Variable')
                      ->where('aktif', true);
                })
                ->get();
                
            foreach ($jabatanTemplates as $template) {
                // Only add if not already set by employee-specific template
                if (!isset($components[$template->komponen_payroll_id])) {
                    $components[$template->komponen_payroll_id] = [
                        'id' => $template->id,
                        'nama' => $template->komponenPayroll->nama_komponen,
                        'kode' => $template->komponenPayroll->kode,
                        'nilai' => $template->nilai,
                        'tipe' => $template->komponenPayroll->tipe_perhitungan ?? 'Tetap',
                        'source' => 'jabatan_specific'
                    ];
                }
            }
        }
        
        // Get project-specific Variable templates (if not overridden)
        if ($karyawan->project_id) {
            $projectTemplates = \App\Models\TemplateKomponenGaji::with('komponenPayroll')
                ->where('project_id', $karyawan->project_id)
                ->where('aktif', true)
                ->whereHas('komponenPayroll', function($q) {
                    $q->where('jenis', 'Tunjangan')
                      ->where('kategori', 'Variable')
                      ->where('aktif', true);
                })
                ->get();
                
            foreach ($projectTemplates as $template) {
                // Only add if not already set by higher priority templates
                if (!isset($components[$template->komponen_payroll_id])) {
                    $components[$template->komponen_payroll_id] = [
                        'id' => $template->id,
                        'nama' => $template->komponenPayroll->nama_komponen,
                        'kode' => $template->komponenPayroll->kode,
                        'nilai' => $template->nilai,
                        'tipe' => $template->komponenPayroll->tipe_perhitungan ?? 'Tetap',
                        'source' => 'project_specific'
                    ];
                }
            }
        }
        
        // Get default/general Variable templates (lowest priority)
        $defaultTemplates = \App\Models\TemplateKomponenGaji::with('komponenPayroll')
            ->whereNull('karyawan_id')
            ->whereNull('jabatan_id')
            ->whereNull('project_id')
            ->where('aktif', true)
            ->whereHas('komponenPayroll', function($q) {
                $q->where('jenis', 'Tunjangan')
                  ->where('kategori', 'Variable')
                  ->where('aktif', true);
            })
            ->get();
            
        foreach ($defaultTemplates as $template) {
            // Only add if not already set by higher priority templates
            if (!isset($components[$template->komponen_payroll_id])) {
                $components[$template->komponen_payroll_id] = [
                    'id' => $template->id,
                    'nama' => $template->komponenPayroll->nama_komponen,
                    'kode' => $template->komponenPayroll->kode,
                    'nilai' => $template->nilai,
                    'tipe' => $template->komponenPayroll->tipe_perhitungan ?? 'Tetap',
                    'source' => 'default'
                ];
            }
        }
        
        return $components;
    }
}
