<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Project;
use App\Models\Jabatan;
use App\Exports\SimplePayrollExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

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
                'tunjangan_detail',
                'total_tunjangan',
                'potongan_detail',
                'total_potongan',
                'bpjs_kesehatan',
                'bpjs_ketenagakerjaan',
                'pajak_pph21',
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
                'karyawan:id,nik_karyawan,nama_lengkap,jabatan_id,gaji_pokok',
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
        
        // Calculate actual values from detail breakdown for each payroll
        $payrollSetting = \App\Models\PayrollSetting::first();
        
        foreach ($payrolls as $payroll) {
            // Use the SAME calculation logic as in detail view
            
            // Calculate upah tetap (gaji pokok + fixed allowances)
            $fixedAllowances = $this->getEmployeeFixedAllowances($payroll->karyawan);
            $totalFixedAllowances = array_sum(array_column($fixedAllowances, 'nilai'));
            $upahTetap = $payroll->gaji_pokok + $totalFixedAllowances;
            
            // Calculate Variable tunjangan (same logic as detail view)
            $totalVariableTunjangan = 0;
            
            // Get Variable components from templates
            $expectedVariableComponents = $this->getExpectedVariableComponents($payroll->karyawan);
            
            // Calculate overtime allowance
            $overtimeData = $this->calculateOvertimeAllowance($payroll->karyawan, $payroll->periode);
            
            // Add Upah Lembur as variable component
            $expectedVariableComponents['upah_lembur'] = [
                'id' => 'upah_lembur',
                'nama' => 'Upah Lembur',
                'kode' => 'UPAH_LEMBUR',
                'nilai' => $overtimeData['total_upah'],
                'tipe' => 'Otomatis',
                'source' => 'overtime_calculation'
            ];
            
            // Process existing tunjangan_detail for Variable category
            if ($payroll->tunjangan_detail && count($payroll->tunjangan_detail) > 0) {
                foreach ($payroll->tunjangan_detail as $tunjangan) {
                    $componentCode = $tunjangan['kode'] ?? $tunjangan['nama'];
                    $komponenPayroll = \App\Models\KomponenPayroll::where(function($q) use ($componentCode, $tunjangan) {
                        $q->where('kode', $componentCode)
                          ->orWhere('nama_komponen', $componentCode)
                          ->orWhere('nama_komponen', $tunjangan['nama']);
                    })->first();
                    
                    $isVariable = false;
                    if ($komponenPayroll) {
                        $isVariable = ($komponenPayroll->kategori === 'Variable');
                    } else {
                        // Fallback pattern matching
                        $tunjanganNameLower = strtolower(trim($tunjangan['nama']));
                        $variablePatterns = ['uang makan', 'transport', 'insentif', 'bonus', 'komisi', 'makan'];
                        foreach ($variablePatterns as $pattern) {
                            if (strpos($tunjanganNameLower, $pattern) !== false) {
                                $isVariable = true;
                                break;
                            }
                        }
                    }
                    
                    if ($isVariable) {
                        $totalVariableTunjangan += $tunjangan['nilai_hitung'];
                    }
                }
            }
            
            // Add missing Variable components from templates
            foreach ($expectedVariableComponents as $expectedComponent) {
                $alreadyExists = false;
                if ($payroll->tunjangan_detail) {
                    foreach ($payroll->tunjangan_detail as $existing) {
                        if (strtolower(trim($existing['nama'])) === strtolower(trim($expectedComponent['nama']))) {
                            $alreadyExists = true;
                            break;
                        }
                    }
                }
                
                if (!$alreadyExists) {
                    $totalVariableTunjangan += $expectedComponent['nilai'];
                }
            }
            
            // Calculate BPJS (same as detail view)
            $bpjsKesehatanCalculated = $payrollSetting ? ($upahTetap * $payrollSetting->bpjs_kesehatan_perusahaan) / 100 : $payroll->bpjs_kesehatan;
            $bpjsKetenagakerjaanCalculated = $payrollSetting ? 
                ($upahTetap * ($payrollSetting->bpjs_jht_perusahaan + $payrollSetting->bpjs_jp_perusahaan + $payrollSetting->bpjs_jkk_perusahaan + $payrollSetting->bpjs_jkm_perusahaan)) / 100 : 
                $payroll->bpjs_ketenagakerjaan;
            
            // Calculate Gaji Bruto (EXACT same formula as detail view)
            $gajiBrutoRecalculated = $upahTetap + $totalVariableTunjangan + $bpjsKesehatanCalculated + $bpjsKetenagakerjaanCalculated;
            
            // Calculate total potongan (same logic as detail view)
            $totalPotonganRecalculated = 0;
            if ($payroll->potongan_detail && count($payroll->potongan_detail) > 0) {
                foreach ($payroll->potongan_detail as $potongan) {
                    $displayValue = $potongan['nilai_hitung'];
                    $potonganName = strtolower(trim($potongan['nama']));
                    
                    // Recalculate BPJS employee deductions based on upah tetap
                    if (strpos($potonganName, 'bpjs kesehatan') !== false && $payrollSetting) {
                        $displayValue = ($upahTetap * $payrollSetting->bpjs_kesehatan_karyawan) / 100;
                    } elseif (strpos($potonganName, 'bpjs ketenagakerjaan') !== false && $payrollSetting) {
                        $displayValue = ($upahTetap * ($payrollSetting->bpjs_jht_karyawan + $payrollSetting->bpjs_jp_karyawan)) / 100;
                    }
                    
                    $totalPotonganRecalculated += $displayValue;
                }
            }
            
            // Add BPJS perusahaan to total potongan (same as detail view)
            $totalPotonganRecalculated += $bpjsKesehatanCalculated + $bpjsKetenagakerjaanCalculated;
            
            // Calculate Gaji Netto (EXACT same formula as detail view)
            $gajiNettoRecalculated = $gajiBrutoRecalculated - $totalPotonganRecalculated - $payroll->pajak_pph21;
            
            // Set calculated values as attributes for display
            $payroll->calculated_gaji_bruto = $gajiBrutoRecalculated;
            $payroll->calculated_gaji_netto = $gajiNettoRecalculated;
        }
        
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
    
    public function testExport()
    {
        try {
            // Simple test data
            $data = collect([
                ['No Badge' => 'B1849324', 'Nama' => 'Hang Nadim', 'Gaji' => 5000000],
                ['No Badge' => 'B1849325', 'Nama' => 'Test User', 'Gaji' => 4000000],
            ]);
            
            return Excel::download(new class($data) implements FromCollection, WithHeadings {
                private $data;
                
                public function __construct($data) {
                    $this->data = $data;
                }
                
                public function collection() {
                    return $this->data;
                }
                
                public function headings(): array {
                    return ['No Badge', 'Nama', 'Gaji'];
                }
            }, 'test_export.xlsx');
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    
    public function export(Request $request)
    {
        try {
            // Get filters (same as index method)
            $periode = $request->get('periode', now()->format('Y-m'));
            $projectId = $request->get('project_id');
            $jabatanId = $request->get('jabatan_id');
            $status = $request->get('status', 'all');
            $search = $request->get('search');
            
            // Validate periode format
            try {
                \Carbon\Carbon::createFromFormat('Y-m', $periode);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Format periode tidak valid']);
            }
            
            // Get payroll data
            $query = Payroll::with([
                    'karyawan:id,nik_karyawan,nama_lengkap,nama_bank,nomor_rekening,nama_pemilik_rekening',
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

            $payrolls = $query->orderBy('created_at', 'desc')->get();
            
            // Transform to array for Excel
            $data = $payrolls->map(function($payroll) {
                return [
                    $payroll->karyawan->nik_karyawan ?? '-',
                    $payroll->karyawan->nama_lengkap ?? '-',
                    $payroll->project->nama ?? '-',
                    $payroll->karyawan->nama_bank ?? '-',
                    $payroll->karyawan->nomor_rekening ?? '-',
                    $payroll->karyawan->nama_pemilik_rekening ?? '-',
                    $payroll->gaji_netto ?? 0
                ];
            });
            
            // Generate filename
            $periodeFormatted = \Carbon\Carbon::createFromFormat('Y-m', $periode)->format('F_Y');
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "Payroll_Export_{$periodeFormatted}_{$timestamp}.xlsx";
            
            // Create Excel export
            $export = new class($data) implements FromCollection, WithHeadings {
                private $data;
                
                public function __construct($data) {
                    $this->data = $data;
                }
                
                public function collection() {
                    return $this->data;
                }
                
                public function headings(): array {
                    return [
                        'No Badge',
                        'Nama Karyawan',
                        'Nama Project',
                        'Nama Bank',
                        'No Rekening',
                        'Nama Pemilik Rekening',
                        'Jumlah Gaji Netto (Take Home Pay)'
                    ];
                }
            };
            
            return Excel::download($export, $filename);
            
        } catch (\Exception $e) {
            \Log::error('Export Payroll Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json(['error' => 'Gagal export payroll: ' . $e->getMessage()]);
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
            
            // Recalculate totals from detail arrays
            $newTotal = array_sum(array_column($currentDetails, 'nilai_hitung'));
            
            // Update payroll data - only update the detail and total fields
            $updateData = [
                $detailField => $currentDetails,
                $totalField => $newTotal,
            ];
            
            $payroll->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Komponen berhasil diupdate',
                'data' => [
                    'new_value' => $newValue,
                    'new_value_formatted' => 'Rp ' . number_format($newValue, 0, ',', '.'),
                    'new_total' => $newTotal,
                    'new_total_formatted' => 'Rp ' . number_format($newTotal, 0, ',', '.'),
                    // Add recalculated totals for auto-update
                    'recalculated_totals' => $this->getRecalculatedTotals($payroll)
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
    
    /**
     * Get recalculated totals for auto-update functionality
     * Just return the current calculated values from the view logic
     */
    private function getRecalculatedTotals($payroll)
    {
        // Reload the payroll to get fresh data
        $payroll->refresh();
        
        // Get the same variables that are calculated in the view
        $payrollSetting = \App\Models\PayrollSetting::first();
        $fixedAllowances = $this->getEmployeeFixedAllowances($payroll->karyawan);
        $totalFixedAllowances = array_sum(array_column($fixedAllowances, 'nilai'));
        $upahTetap = $payroll->gaji_pokok + $totalFixedAllowances;
        
        // Get variable tunjangan (same logic as in view)
        $variableTunjangan = [];
        $totalVariableTunjangan = 0;
        
        // Get Variable components from templates
        $expectedVariableComponents = $this->getExpectedVariableComponents($payroll->karyawan);
        
        // Calculate overtime allowance
        $overtimeData = $this->calculateOvertimeAllowance($payroll->karyawan, $payroll->periode);
        
        // Add Upah Lembur as variable component
        $expectedVariableComponents['upah_lembur'] = [
            'id' => 'upah_lembur',
            'nama' => 'Upah Lembur',
            'kode' => 'UPAH_LEMBUR',
            'nilai' => $overtimeData['total_upah'],
            'tipe' => 'Otomatis',
            'source' => 'overtime_calculation'
        ];
        
        // Process existing tunjangan_detail for Variable category
        if ($payroll->tunjangan_detail && count($payroll->tunjangan_detail) > 0) {
            foreach ($payroll->tunjangan_detail as $tunjangan) {
                $componentCode = $tunjangan['kode'] ?? $tunjangan['nama'];
                $komponenPayroll = \App\Models\KomponenPayroll::where(function($q) use ($componentCode, $tunjangan) {
                    $q->where('kode', $componentCode)
                      ->orWhere('nama_komponen', $componentCode)
                      ->orWhere('nama_komponen', $tunjangan['nama']);
                })->first();
                
                $isVariable = false;
                if ($komponenPayroll) {
                    $isVariable = ($komponenPayroll->kategori === 'Variable');
                } else {
                    // Fallback pattern matching
                    $tunjanganNameLower = strtolower(trim($tunjangan['nama']));
                    $variablePatterns = ['uang makan', 'transport', 'insentif', 'bonus', 'komisi', 'makan'];
                    foreach ($variablePatterns as $pattern) {
                        if (strpos($tunjanganNameLower, $pattern) !== false) {
                            $isVariable = true;
                            break;
                        }
                    }
                }
                
                if ($isVariable) {
                    $totalVariableTunjangan += $tunjangan['nilai_hitung'];
                }
            }
        }
        
        // Add missing Variable components from templates
        foreach ($expectedVariableComponents as $expectedComponent) {
            $alreadyExists = false;
            if ($payroll->tunjangan_detail) {
                foreach ($payroll->tunjangan_detail as $existing) {
                    if (strtolower(trim($existing['nama'])) === strtolower(trim($expectedComponent['nama']))) {
                        $alreadyExists = true;
                        break;
                    }
                }
            }
            
            if (!$alreadyExists) {
                $totalVariableTunjangan += $expectedComponent['nilai'];
            }
        }
        
        // Calculate BPJS (same as in view)
        $bpjsKesehatanCalculated = $payrollSetting ? ($upahTetap * $payrollSetting->bpjs_kesehatan_perusahaan) / 100 : $payroll->bpjs_kesehatan;
        $bpjsKetenagakerjaanCalculated = $payrollSetting ? 
            ($upahTetap * ($payrollSetting->bpjs_jht_perusahaan + $payrollSetting->bpjs_jp_perusahaan + $payrollSetting->bpjs_jkk_perusahaan + $payrollSetting->bpjs_jkm_perusahaan)) / 100 : 
            $payroll->bpjs_ketenagakerjaan;
        
        // Calculate Gaji Bruto (same as in view)
        $gajiBrutoRecalculated = $upahTetap + $totalVariableTunjangan + $bpjsKesehatanCalculated + $bpjsKetenagakerjaanCalculated;
        
        // Calculate total potongan (same logic as in view)
        $totalPotonganRecalculated = 0;
        if ($payroll->potongan_detail && count($payroll->potongan_detail) > 0) {
            foreach ($payroll->potongan_detail as $potongan) {
                $displayValue = $potongan['nilai_hitung'];
                $potonganName = strtolower(trim($potongan['nama']));
                
                // Recalculate BPJS employee deductions based on upah tetap
                if (strpos($potonganName, 'bpjs kesehatan') !== false && $payrollSetting) {
                    $displayValue = ($upahTetap * $payrollSetting->bpjs_kesehatan_karyawan) / 100;
                } elseif (strpos($potonganName, 'bpjs ketenagakerjaan') !== false && $payrollSetting) {
                    $displayValue = ($upahTetap * ($payrollSetting->bpjs_jht_karyawan + $payrollSetting->bpjs_jp_karyawan)) / 100;
                }
                
                $totalPotonganRecalculated += $displayValue;
            }
        }
        
        // Add BPJS perusahaan to total potongan (same as in view)
        $totalPotonganRecalculated += $bpjsKesehatanCalculated + $bpjsKetenagakerjaanCalculated;
        
        // Calculate Gaji Netto (same as in view)
        $gajiNettoRecalculated = $gajiBrutoRecalculated - $totalPotonganRecalculated - $payroll->pajak_pph21;
        
        return [
            'total_variable_tunjangan' => $totalVariableTunjangan,
            'total_variable_tunjangan_formatted' => 'Rp ' . number_format($totalVariableTunjangan, 0, ',', '.'),
            'gaji_bruto' => $gajiBrutoRecalculated,
            'gaji_bruto_formatted' => 'Rp ' . number_format($gajiBrutoRecalculated, 0, ',', '.'),
            'total_potongan' => $totalPotonganRecalculated,
            'total_potongan_formatted' => 'Rp ' . number_format($totalPotonganRecalculated, 0, ',', '.'),
            'gaji_netto' => $gajiNettoRecalculated,
            'gaji_netto_formatted' => 'Rp ' . number_format($gajiNettoRecalculated, 0, ',', '.')
        ];
    }
}
