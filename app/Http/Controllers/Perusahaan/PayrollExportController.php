<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class PayrollExportController extends Controller
{
    public function showExportPage()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Get projects and jabatans for filters (optimized)
        $projects = \App\Models\Project::select('id', 'nama')
            ->where('perusahaan_id', $perusahaanId)
            ->orderBy('nama')
            ->get();
            
        $jabatans = \App\Models\Jabatan::select('id', 'nama')
            ->orderBy('nama')
            ->get();
        
        return view('perusahaan.payroll.export', compact('projects', 'jabatans'));
    }

    public function export(Request $request)
    {
        try {
            // WAJIB: Validasi input
            $validated = $request->validate([
                'periode' => 'required|date_format:Y-m',
                'project_id' => 'required|exists:projects,id',
                'jabatan_id' => 'nullable|exists:jabatans,id',
                'status' => 'nullable|in:all,draft,approved,paid',
                'search' => 'nullable|string|max:255'
            ], [
                'periode.required' => 'Periode wajib dipilih',
                'periode.date_format' => 'Format periode tidak valid',
                'project_id.required' => 'Project wajib dipilih',
                'project_id.exists' => 'Project tidak ditemukan',
                'jabatan_id.exists' => 'Jabatan tidak ditemukan',
                'status.in' => 'Status tidak valid'
            ]);
            
            // Get filters
            $periode = $validated['periode'];
            $projectId = $validated['project_id'];
            $jabatanId = $validated['jabatan_id'] ?? null;
            $status = $validated['status'] ?? 'all';
            $search = $validated['search'] ?? null;
            
            // CRITICAL: Multi-tenancy filter - WAJIB untuk keamanan
            $perusahaanId = auth()->user()->perusahaan_id;
            if (!$perusahaanId) {
                return response()->json(['error' => 'User tidak memiliki akses perusahaan'], 403);
            }
            
            // SECURITY: Validasi project ownership
            $project = \App\Models\Project::where('id', $projectId)
                ->where('perusahaan_id', $perusahaanId)
                ->first();
                
            if (!$project) {
                return response()->json(['error' => 'Project tidak ditemukan atau tidak memiliki akses'], 403);
            }
            
            // Get payroll data with MANDATORY perusahaan_id filter
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
                    'status',
                    'created_at'
                ])
                ->with([
                    'karyawan:id,nik_karyawan,nama_lengkap,jabatan_id,gaji_pokok,nama_bank,nomor_rekening,nama_pemilik_rekening',
                    'karyawan.jabatan:id,nama',
                    'project:id,nama'
                ])
                ->where('perusahaan_id', $perusahaanId)  // CRITICAL: Multi-tenancy filter
                ->where('periode', $periode)
                ->where('project_id', $projectId);  // WAJIB: Project filter

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

            // PERFORMANCE: Check total count before processing
            $totalCount = $query->count();
            if ($totalCount > 5000) {
                return response()->json([
                    'error' => "Terlalu banyak data untuk di-export ({$totalCount} records). Gunakan filter untuk mengurangi data."
                ], 400);
            }
            
            if ($totalCount == 0) {
                return response()->json([
                    'error' => 'Tidak ada data payroll yang sesuai dengan filter yang dipilih.'
                ], 400);
            }

            $payrolls = $query->orderBy('created_at', 'desc')->get();
            
            // Calculate actual values using SAME logic as DaftarPayrollController
            $payrollSetting = \App\Models\PayrollSetting::where('perusahaan_id', $perusahaanId)->first();

            foreach ($payrolls as $payroll) {
                // Use the SAME calculation logic as in DaftarPayrollController
                $fixedAllowances = $this->getEmployeeFixedAllowances($payroll->karyawan);
                $totalFixedAllowances = array_sum(array_column($fixedAllowances, 'nilai'));
                $upahTetap = $payroll->gaji_pokok + $totalFixedAllowances;
                
                // Calculate Variable tunjangan (same logic as controller)
                $totalVariableTunjangan = 0;
                $expectedVariableComponents = $this->getExpectedVariableComponents($payroll->karyawan);
                
                // Calculate overtime allowance
                $overtimeData = $this->calculateOvertimeAllowance($payroll->karyawan, $payroll->periode);
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
                
                // Calculate BPJS (same as controller)
                $bpjsKesehatanCalculated = $payrollSetting ? ($upahTetap * $payrollSetting->bpjs_kesehatan_perusahaan) / 100 : $payroll->bpjs_kesehatan;
                $bpjsKetenagakerjaanCalculated = $payrollSetting ? 
                    ($upahTetap * ($payrollSetting->bpjs_jht_perusahaan + $payrollSetting->bpjs_jp_perusahaan + $payrollSetting->bpjs_jkk_perusahaan + $payrollSetting->bpjs_jkm_perusahaan)) / 100 : 
                    $payroll->bpjs_ketenagakerjaan;
                
                // Calculate Gaji Bruto (EXACT same formula as controller)
                $gajiBrutoRecalculated = $upahTetap + $totalVariableTunjangan + $bpjsKesehatanCalculated + $bpjsKetenagakerjaanCalculated;
                
                // Calculate total potongan (same logic as controller)
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
                
                // Add BPJS perusahaan to total potongan (same as controller)
                $totalPotonganRecalculated += $bpjsKesehatanCalculated + $bpjsKetenagakerjaanCalculated;
                
                // Calculate Gaji Netto (EXACT same formula as controller)
                $gajiNettoRecalculated = $gajiBrutoRecalculated - $totalPotonganRecalculated - $payroll->pajak_pph21;
                
                // Set calculated values as attributes for export
                $payroll->calculated_gaji_bruto = $gajiBrutoRecalculated;
                $payroll->calculated_gaji_netto = $gajiNettoRecalculated;
            }
            
            // Transform REAL payroll data to array for Excel
            $data = $payrolls->map(function($payroll) {
                return [
                    $payroll->karyawan->nik_karyawan ?? '-',
                    $payroll->karyawan->nama_lengkap ?? '-',
                    $payroll->project->nama ?? '-',
                    $payroll->karyawan->nama_bank ?? '-',
                    $payroll->karyawan->nomor_rekening ?? '-',  // Will be formatted as text by Excel formatting
                    $payroll->karyawan->nama_pemilik_rekening ?? '-',
                    round($payroll->calculated_gaji_netto ?? 0)  // ROUND to remove decimals (same as dashboard)
                ];
            });
            
            // Generate filename with company and project info
            $periodeFormatted = \Carbon\Carbon::createFromFormat('Y-m', $periode)->format('F_Y');
            $timestamp = now()->format('Y-m-d_H-i-s');
            $companyName = auth()->user()->perusahaan->nama ?? 'Company';
            $projectName = $project->nama;
            $filename = "Payroll_Export_{$companyName}_{$projectName}_{$periodeFormatted}_{$timestamp}.xlsx";
            
            // Log export activity for audit
            \Log::info('Payroll Export', [
                'user_id' => auth()->id(),
                'perusahaan_id' => $perusahaanId,
                'project_id' => $projectId,
                'project_name' => $project->nama,
                'periode' => $periode,
                'total_records' => $totalCount,
                'filters' => [
                    'jabatan_id' => $jabatanId,
                    'status' => $status,
                    'search' => $search
                ]
            ]);
            
            // Create Excel export with proper formatting
            $export = new class($data) implements FromCollection, WithHeadings, WithEvents {
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
                
                public function registerEvents(): array {
                    return [
                        AfterSheet::class => function(AfterSheet $event) {
                            $sheet = $event->sheet->getDelegate();
                            
                            // Get the highest row and column
                            $highestRow = $sheet->getHighestRow();
                            $highestColumn = $sheet->getHighestColumn();
                            
                            // Format specific columns as text BEFORE Excel processes them
                            $sheet->getStyle('A2:A' . $highestRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                            $sheet->getStyle('E2:E' . $highestRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                            
                            // Format gaji column as number
                            $sheet->getStyle('G2:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');
                            
                            // Set all cells in columns A and E to text format and re-set values
                            for ($row = 2; $row <= $highestRow; $row++) {
                                // Force No Badge as text
                                $noBadgeValue = $sheet->getCell('A' . $row)->getValue();
                                $sheet->setCellValueExplicit('A' . $row, $noBadgeValue, DataType::TYPE_STRING);
                                
                                // Force No Rekening as text
                                $noRekeningValue = $sheet->getCell('E' . $row)->getValue();
                                if ($noRekeningValue !== '-') {
                                    $sheet->setCellValueExplicit('E' . $row, $noRekeningValue, DataType::TYPE_STRING);
                                }
                            }
                        },
                    ];
                }
            };
            
            return Excel::download($export, $filename);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Export Payroll Error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'perusahaan_id' => auth()->user()->perusahaan_id ?? null,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Gagal export: ' . $e->getMessage()]);
        }
    }

    // Helper methods (same as DaftarPayrollController)
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
}