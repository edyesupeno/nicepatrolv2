<?php

namespace App\Imports;

use App\Models\JadwalShift;
use App\Models\Karyawan;
use App\Models\Shift;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class JadwalShiftImport implements ToCollection
{
    protected $perusahaanId;
    protected $projectId;
    protected $errors = [];
    protected $successCount = 0;
    
    public function __construct($perusahaanId, $projectId)
    {
        $this->perusahaanId = $perusahaanId;
        $this->projectId = $projectId;
    }
    
    public function collection(Collection $rows)
    {
        // Row 1-3: Title, Period, Instructions (skip)
        // Row 4 (index 3): Header with dates
        // Row 5+ (index 4+): Data karyawan
        
        if ($rows->count() < 5) {
            $this->errors[] = "File Excel tidak valid: Format tidak sesuai template";
            return;
        }
        
        // Get header row (row 4 = index 3) to parse dates
        $headerRow = $rows->get(3);
        if (!$headerRow) {
            $this->errors[] = "File Excel tidak valid: Header tidak ditemukan";
            return;
        }
        
        // Parse dates from header (starting from column D = index 3)
        $dates = [];
        for ($i = 3; $i < count($headerRow); $i++) {
            $headerValue = $headerRow[$i];
            if (empty($headerValue)) {
                break; // No more date columns
            }
            
            // Parse date from format "dd/mm/yyyy\nHari"
            $dateParts = explode("\n", $headerValue);
            if (count($dateParts) > 0) {
                try {
                    $dateStr = trim($dateParts[0]);
                    // Try to parse dd/mm/yyyy format
                    $date = Carbon::createFromFormat('d/m/Y', $dateStr);
                    $dates[$i] = $date;
                } catch (\Exception $e) {
                    // Skip invalid date
                    continue;
                }
            }
        }
        
        if (empty($dates)) {
            $this->errors[] = "Tidak ada tanggal yang valid di header Excel";
            return;
        }
        
        // Process data rows (starting from row 5 = index 4)
        for ($rowIndex = 4; $rowIndex < $rows->count(); $rowIndex++) {
            $row = $rows->get($rowIndex);
            $actualRow = $rowIndex + 1; // Actual row number in Excel (1-based)
            
            // Skip if NIK is empty
            if (empty($row[0])) {
                continue;
            }
            
            $nik = trim($row[0]);
            
            // Skip legend rows (DAFTAR KODE SHIFT, etc)
            if (strtoupper($nik) === 'DAFTAR KODE SHIFT' || 
                strtoupper($nik) === 'KODE SHIFT' ||
                empty($row[1])) { // Skip if nama karyawan empty
                break; // Stop processing, we've reached the legend section
            }
            
            // Find karyawan by NIK
            $karyawan = Karyawan::where('nik_karyawan', $nik)
                ->where('project_id', $this->projectId)
                ->where('is_active', true)
                ->first();
            
            if (!$karyawan) {
                $this->errors[] = "Baris {$actualRow}: NIK {$nik} tidak ditemukan atau tidak aktif di project ini";
                continue;
            }
            
            // Process each date column
            foreach ($dates as $columnIndex => $date) {
                if (!isset($row[$columnIndex]) || empty($row[$columnIndex])) {
                    continue; // Skip empty cells
                }
                
                $kodeShift = trim($row[$columnIndex]);
                
                // Find shift by kode
                $shift = Shift::where('kode_shift', $kodeShift)
                    ->where('project_id', $this->projectId)
                    ->first();
                
                if (!$shift) {
                    $columnLetter = $this->getColumnLetter($columnIndex + 1);
                    $this->errors[] = "Baris {$actualRow}, Kolom {$columnLetter}: Kode shift '{$kodeShift}' tidak ditemukan";
                    continue;
                }
                
                // Create or update jadwal shift
                try {
                    JadwalShift::updateOrCreate(
                        [
                            'karyawan_id' => $karyawan->id,
                            'tanggal' => $date->format('Y-m-d'),
                        ],
                        [
                            'shift_id' => $shift->id,
                            'perusahaan_id' => $this->perusahaanId,
                        ]
                    );
                    $this->successCount++;
                } catch (\Exception $e) {
                    $this->errors[] = "Baris {$actualRow}: Gagal menyimpan jadwal - " . $e->getMessage();
                }
            }
        }
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function getSuccessCount()
    {
        return $this->successCount;
    }
    
    private function getColumnLetter($columnNumber)
    {
        $letter = '';
        while ($columnNumber > 0) {
            $temp = ($columnNumber - 1) % 26;
            $letter = chr($temp + 65) . $letter;
            $columnNumber = ($columnNumber - $temp - 1) / 26;
        }
        return $letter;
    }
}
