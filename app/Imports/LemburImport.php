<?php

namespace App\Imports;

use App\Models\Lembur;
use App\Models\Karyawan;
use App\Models\Project;
use App\Models\PayrollSetting;
use App\Models\TemplateKomponenGaji;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LemburImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $perusahaanId;
    protected $projectId;
    protected $employeeIds;
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $silentlySkippedCount = 0; // For employees not selected
    protected $errorRows = [];

    public function __construct($perusahaanId, $projectId = null, $employeeIds = [])
    {
        $this->perusahaanId = $perusahaanId;
        $this->projectId = $projectId;
        $this->employeeIds = is_array($employeeIds) ? $employeeIds : [];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $result = $this->processRow($row, $index + 2); // +2 because of header row and 0-based index
                
                // If processRow returns null, it means the row was skipped silently
                // Don't count this as an error or increment any counters
                
            } catch (\Exception $e) {
                $this->errorRows[] = [
                    'row' => $index + 2,
                    'error' => $e->getMessage(),
                    'data' => $row->toArray()
                ];
                $this->skippedCount++;
            }
        }
    }

    protected function processRow($row, $rowNumber)
    {
        // Find karyawan by NIK (No Badge)
        $karyawan = Karyawan::where('nik_karyawan', $row['no_badge'])
            ->where('perusahaan_id', $this->perusahaanId)
            ->where('is_active', true)
            ->first();

        if (!$karyawan) {
            throw new \Exception("Karyawan dengan No Badge {$row['no_badge']} tidak ditemukan atau tidak aktif");
        }

        // If specific employees are selected, check if karyawan is in the list FIRST
        // This prevents unnecessary validation for unselected employees
        // Skip this check if no employee restrictions are set (import all from Excel)
        if (!empty($this->employeeIds) && !in_array($karyawan->id, $this->employeeIds)) {
            // Skip this row silently - karyawan tidak dipilih untuk import
            $this->silentlySkippedCount++;
            return;
        }

        // Validate nama karyawan matches (optional validation)
        if (isset($row['nama_karyawan']) && !empty($row['nama_karyawan'])) {
            $namaFromExcel = strtolower(trim($row['nama_karyawan']));
            $namaFromDB = strtolower(trim($karyawan->nama_lengkap));
            
            if ($namaFromExcel !== $namaFromDB) {
                throw new \Exception("Nama karyawan '{$row['nama_karyawan']}' tidak sesuai dengan No Badge {$row['no_badge']} (seharusnya: {$karyawan->nama_lengkap})");
            }
        }

        // Find project by name
        $project = Project::where('nama', $row['project'])
            ->where('perusahaan_id', $this->perusahaanId)
            ->first();

        if (!$project) {
            throw new \Exception("Project '{$row['project']}' tidak ditemukan");
        }

        // If specific project is set, validate it matches
        if ($this->projectId && $project->id != $this->projectId) {
            throw new \Exception("Project '{$row['project']}' tidak sesuai dengan project yang dipilih");
        }

        // Validate karyawan belongs to project
        if ($karyawan->project_id != $project->id) {
            throw new \Exception("Karyawan {$karyawan->nama_lengkap} tidak terdaftar di project {$project->nama}");
        }

        // Parse date
        $tanggalLembur = $this->parseDate($row['tanggal_lembur']);
        if (!$tanggalLembur) {
            throw new \Exception("Format tanggal tidak valid: {$row['tanggal_lembur']}");
        }

        // Parse time
        $jamMulai = $this->parseTime($row['jam_mulai']);
        $jamSelesai = $this->parseTime($row['jam_selesai']);

        if (!$jamMulai || !$jamSelesai) {
            throw new \Exception("Format jam tidak valid");
        }

        // Calculate total hours
        $jamMulaiCarbon = Carbon::parse($jamMulai);
        $jamSelesaiCarbon = Carbon::parse($jamSelesai);
        
        // Handle overnight shift
        if ($jamSelesaiCarbon->lt($jamMulaiCarbon)) {
            $jamSelesaiCarbon->addDay();
        }
        
        $totalJam = $jamSelesaiCarbon->diffInHours($jamMulaiCarbon, true);
        
        // Validate reasonable working hours
        if ($totalJam > 12) {
            throw new \Exception("Total jam lembur tidak boleh lebih dari 12 jam");
        }
        
        if ($totalJam <= 0) {
            throw new \Exception("Jam selesai harus lebih besar dari jam mulai");
        }
        
        if ($totalJam < 0.5) {
            throw new \Exception("Durasi lembur minimal adalah 30 menit");
        }

        // Calculate overtime rate automatically
        $tarifLemburPerJam = $this->calculateOvertimeRate($tanggalLembur, $karyawan);

        // Check for duplicate
        $existing = Lembur::where('karyawan_id', $karyawan->id)
            ->where('tanggal_lembur', $tanggalLembur)
            ->where('jam_mulai', $jamMulai)
            ->where('jam_selesai', $jamSelesai)
            ->first();

        if ($existing) {
            throw new \Exception("Data lembur sudah ada untuk karyawan ini pada tanggal dan jam yang sama");
        }

        // Create lembur record
        Lembur::create([
            'perusahaan_id' => $this->perusahaanId,
            'project_id' => $project->id,
            'karyawan_id' => $karyawan->id,
            'tanggal_lembur' => $tanggalLembur,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'total_jam' => round($totalJam, 0),
            'alasan_lembur' => $row['alasan_lembur'] ?? 'Import dari Excel',
            'deskripsi_pekerjaan' => $row['deskripsi_pekerjaan'] ?? 'Import dari Excel',
            'tarif_lembur_per_jam' => $tarifLemburPerJam,
            'total_upah_lembur' => round($totalJam * $tarifLemburPerJam, 0),
            'status' => 'pending'
        ]);

        $this->importedCount++;
    }

    protected function parseDate($dateString)
    {
        try {
            // Try different date formats
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y'];
            
            foreach ($formats as $format) {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date && $date->format($format) === $dateString) {
                    return $date->format('Y-m-d');
                }
            }
            
            // Try parsing with Carbon's flexible parser
            $date = Carbon::parse($dateString);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function parseTime($timeString)
    {
        try {
            // Try different time formats
            $formats = ['H:i', 'H:i:s', 'g:i A', 'g:i:s A'];
            
            foreach ($formats as $format) {
                $time = Carbon::createFromFormat($format, $timeString);
                if ($time && $time->format($format) === $timeString) {
                    return $time->format('H:i');
                }
            }
            
            // Try parsing with Carbon's flexible parser
            $time = Carbon::parse($timeString);
            return $time->format('H:i');
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function calculateOvertimeRate($date, $karyawan)
    {
        // Get payroll settings
        $payrollSetting = PayrollSetting::first();
        if (!$payrollSetting) {
            return 0;
        }
        
        // Determine day type
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek;
        
        // Determine overtime multiplier
        $multiplier = 0;
        if ($dayOfWeek == 0 || $dayOfWeek == 6) { // Sunday or Saturday
            $multiplier = $payrollSetting->lembur_akhir_pekan ?? 2.0;
        } else {
            $multiplier = $payrollSetting->lembur_hari_kerja ?? 1.5;
        }
        
        // Calculate total monthly salary
        $totalMonthlySalary = $this->calculateTotalMonthlySalary($karyawan);
        
        // Calculate hourly rate (assuming 173 working hours per month)
        $hourlyRate = $totalMonthlySalary > 0 ? ($totalMonthlySalary / 173) : 0;
        $overtimeRate = $hourlyRate * $multiplier;
        
        return round($overtimeRate, 0);
    }

    protected function calculateTotalMonthlySalary($karyawan)
    {
        // Start with base salary
        $totalSalary = $karyawan->gaji_pokok ?? 0;
        
        // Get all fixed allowances for this employee
        $fixedAllowances = $this->getEmployeeFixedAllowances($karyawan);
        
        // Add all fixed allowances
        foreach ($fixedAllowances as $allowance) {
            $totalSalary += $allowance['nilai'];
        }
        
        return $totalSalary;
    }

    protected function getEmployeeFixedAllowances($karyawan)
    {
        $allowances = [];
        
        // Get employee-specific allowance templates (highest priority)
        $employeeTemplates = TemplateKomponenGaji::with('komponenPayroll')
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
            $jabatanTemplates = TemplateKomponenGaji::with('komponenPayroll')
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
            $projectTemplates = TemplateKomponenGaji::with('komponenPayroll')
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
        $defaultTemplates = TemplateKomponenGaji::with('komponenPayroll')
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

    public function rules(): array
    {
        return [
            'no_badge' => 'required|string',
            'nama_karyawan' => 'nullable|string',
            'project' => 'required|string',
            'tanggal_lembur' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'alasan_lembur' => 'nullable|string',
            'deskripsi_pekerjaan' => 'nullable|string',
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getSkippedCount()
    {
        return $this->skippedCount;
    }

    public function getSilentlySkippedCount()
    {
        return $this->silentlySkippedCount;
    }

    public function getErrorRows()
    {
        return $this->errorRows;
    }
}