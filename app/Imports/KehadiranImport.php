<?php

namespace App\Imports;

use App\Models\Kehadiran;
use App\Models\Karyawan;
use App\Models\JadwalShift;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Import Kehadiran dari Excel (Format Horizontal)
 * 
 * Format Excel:
 * - Row 1-3: Title, Period, Instructions
 * - Row 4: Header (NIK | Nama | Jabatan | Date1 | Date2 | ... | DateN)
 * - Row 5+: Data karyawan dengan status/jam per tanggal
 * 
 * Cell Value Options:
 * 1. Status Code:
 *    - H = Hadir (jam otomatis dari shift schedule)
 *    - A = Alpa
 *    - I = Izin
 *    - S = Sakit
 *    - C = Cuti
 * 
 * 2. Custom Time:
 *    - Format: 08:00-17:00 (jam masuk - jam keluar)
 * 
 * CRITICAL Rules:
 * - SKIP data yang sudah ada dari aplikasi mobile (jangan replace)
 * - Untuk status "H", jam masuk/keluar otomatis diambil dari shift schedule
 * - Maksimal 31 hari per import
 * - Karyawan harus aktif dan terdaftar di project
 */
class KehadiranImport implements ToCollection
{
    protected $perusahaanId;
    protected $projectId;
    protected $tanggalMulai;
    protected $tanggalAkhir;
    protected $dates = [];
    protected $errors = [];
    protected $successCount = 0;
    protected $skippedCount = 0;
    
    public function __construct($perusahaanId, $projectId, $tanggalMulai, $tanggalAkhir)
    {
        $this->perusahaanId = $perusahaanId;
        $this->projectId = $projectId;
        $this->tanggalMulai = Carbon::parse($tanggalMulai);
        $this->tanggalAkhir = Carbon::parse($tanggalAkhir);
        
        // Generate dates array
        $currentDate = $this->tanggalMulai->copy();
        while ($currentDate <= $this->tanggalAkhir) {
            $this->dates[] = $currentDate->copy();
            $currentDate->addDay();
        }
    }
    
    public function collection(Collection $rows)
    {
        // Row 1-3: Title, Period, Instructions (skip)
        // Row 4 (index 3): Header with dates
        // Row 5+ (index 4+): Data karyawan
        
        if ($rows->count() < 5) {
            $this->errors[] = "File Excel tidak valid: Format tidak sesuai template (total rows: " . $rows->count() . ")";
            return;
        }
        
        // Parse header row to get dates (row 4 = index 3)
        $headerRow = $rows->get(3);
        $dateColumns = [];
        
        // Column 0-2: NIK, Nama, Jabatan
        // Column 3+: Dates
        for ($colIndex = 3; $colIndex < count($headerRow); $colIndex++) {
            $cellValue = $headerRow[$colIndex];
            
            // Skip if empty or legend
            if (empty($cellValue) || stripos($cellValue, 'KETERANGAN') !== false) {
                break;
            }
            
            // Parse date from header
            // Format bisa: "dd/mm/yyyy\nDay" atau "dd/mm/yyyy Day" atau "dd/mm/yyyy"
            $dateStr = $cellValue;
            
            // Remove day name if exists (Min, Sen, Sel, etc)
            $dateStr = preg_replace('/\s*(Min|Sen|Sel|Rab|Kam|Jum|Sab)\s*$/i', '', $dateStr);
            $dateStr = trim($dateStr);
            
            // Remove newline and everything after it
            if (strpos($dateStr, "\n") !== false) {
                $dateStr = explode("\n", $dateStr)[0];
                $dateStr = trim($dateStr);
            }
            
            try {
                $date = null;
                
                // Try format: dd/mm/yyyy
                if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateStr, $matches)) {
                    $date = Carbon::createFromFormat('d/m/Y', $dateStr);
                }
                // Try Excel serial number
                elseif (is_numeric($cellValue) && $cellValue > 40000) {
                    $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue));
                }
                
                if ($date) {
                    $dateColumns[$colIndex] = $date;
                }
            } catch (\Exception $e) {
                // Skip invalid date column
                continue;
            }
        }
        
        if (empty($dateColumns)) {
            $this->errors[] = "File Excel tidak valid: Tidak ada kolom tanggal yang valid";
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
            
            // Skip legend/sample rows
            if (strtoupper($nik) === 'SAMPLE001' || stripos($nik, 'KETERANGAN') !== false || empty($row[1])) {
                break; // Stop processing when reaching legend
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
            
            // Process each date column for this karyawan
            foreach ($dateColumns as $colIndex => $tanggal) {
                $cellValue = isset($row[$colIndex]) ? trim($row[$colIndex]) : '';
                
                // Skip if empty
                if (empty($cellValue)) {
                    continue;
                }
                
                // CRITICAL: Check if kehadiran already exists (from mobile app)
                $existingKehadiran = Kehadiran::where('karyawan_id', $karyawan->id)
                    ->where('tanggal', $tanggal->format('Y-m-d'))
                    ->first();
                
                if ($existingKehadiran) {
                    $this->skippedCount++;
                    continue; // SKIP - Don't replace existing data from app
                }
                
                // Get shift from jadwal
                $jadwalShift = JadwalShift::with('shift')
                    ->where('karyawan_id', $karyawan->id)
                    ->where('tanggal', $tanggal->format('Y-m-d'))
                    ->first();
                
                $shiftId = $jadwalShift ? $jadwalShift->shift_id : null;
                $shift = $jadwalShift ? $jadwalShift->shift : null;
                
                // Check if shift is OFF or HL (Holiday)
                if ($shift && in_array(strtoupper($shift->kode_shift), ['OFF', 'HL'])) {
                    // Skip - this is a day off or holiday, no need to import attendance
                    continue;
                }
                
                // Parse cell value
                $jamMasuk = null;
                $jamKeluar = null;
                $status = 'hadir';
                $keterangan = null;
                
                // Check if status code (H, A, I, S, C)
                $cellValueUpper = strtoupper($cellValue);
                
                if ($cellValueUpper === 'H') {
                    // Hadir - Auto-fetch jam from shift
                    if ($shift) {
                        $jamMasuk = $shift->jam_mulai;
                        $jamKeluar = $shift->jam_selesai;
                        
                        // Calculate status based on shift time (with 15 minutes tolerance)
                        $status = $this->calculateStatus($jamMasuk, $jamKeluar, $shift->jam_mulai, $shift->jam_selesai);
                    } else {
                        // No shift found - skip this entry
                        $this->errors[] = "Baris {$actualRow}, Tanggal {$tanggal->format('d/m/Y')}: Karyawan tidak memiliki shift, data di-skip";
                        continue;
                    }
                } elseif ($cellValueUpper === 'A') {
                    // Alpa
                    $status = 'alpa';
                    $keterangan = 'Alpa';
                } elseif ($cellValueUpper === 'I') {
                    // Izin
                    $status = 'izin';
                    $keterangan = 'Izin';
                } elseif ($cellValueUpper === 'S') {
                    // Sakit
                    $status = 'sakit';
                    $keterangan = 'Sakit';
                } elseif ($cellValueUpper === 'C') {
                    // Cuti
                    $status = 'cuti';
                    $keterangan = 'Cuti';
                } elseif (preg_match('/^(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})$/', $cellValue, $matches)) {
                    // Custom time format: 08:00-17:00
                    $jamMasuk = $matches[1];
                    $jamKeluar = $matches[2];
                    
                    // Validate time format
                    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $jamMasuk)) {
                        $this->errors[] = "Baris {$actualRow}, Tanggal {$tanggal->format('d/m/Y')}: Format jam masuk tidak valid (gunakan HH:MM)";
                        continue;
                    }
                    
                    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $jamKeluar)) {
                        $this->errors[] = "Baris {$actualRow}, Tanggal {$tanggal->format('d/m/Y')}: Format jam keluar tidak valid (gunakan HH:MM)";
                        continue;
                    }
                    
                    // Calculate status based on shift time (if shift exists)
                    if ($shift) {
                        $status = $this->calculateStatus($jamMasuk, $jamKeluar, $shift->jam_mulai, $shift->jam_selesai);
                    } else {
                        $status = 'hadir'; // Default if no shift
                    }
                } else {
                    // Invalid format
                    $this->errors[] = "Baris {$actualRow}, Tanggal {$tanggal->format('d/m/Y')}: Format tidak valid. Gunakan H/A/I/S/C atau 08:00-17:00";
                    continue;
                }
                
                // Calculate durasi kerja (in minutes)
                $durasiKerja = null;
                if ($jamMasuk && $jamKeluar) {
                    try {
                        $masuk = Carbon::createFromFormat('H:i', $jamMasuk);
                        $keluar = Carbon::createFromFormat('H:i', $jamKeluar);
                        
                        // Handle overnight shift
                        if ($keluar->lt($masuk)) {
                            $keluar->addDay();
                        }
                        
                        $durasiKerja = $masuk->diffInMinutes($keluar);
                    } catch (\Exception $e) {
                        // Ignore calculation error
                    }
                }
                
                // Create kehadiran
                try {
                    Kehadiran::create([
                        'karyawan_id' => $karyawan->id,
                        'perusahaan_id' => $this->perusahaanId,
                        'project_id' => $this->projectId,
                        'shift_id' => $shiftId,
                        'tanggal' => $tanggal->format('Y-m-d'),
                        'jam_masuk' => $jamMasuk,
                        'jam_keluar' => $jamKeluar,
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'durasi_kerja' => $durasiKerja,
                        'on_radius' => true, // Default: on radius (asumsi dari Excel)
                        'sumber_data' => 'excel', // Mark as imported from Excel
                    ]);
                    
                    $this->successCount++;
                } catch (\Exception $e) {
                    $this->errors[] = "Baris {$actualRow}, Tanggal {$tanggal->format('d/m/Y')}: Gagal menyimpan - " . $e->getMessage();
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
    
    public function getSkippedCount()
    {
        return $this->skippedCount;
    }
    
    /**
     * Calculate attendance status based on actual time vs shift time
     * 
     * @param string $jamMasuk Actual check-in time (datetime or HH:MM)
     * @param string $jamKeluar Actual check-out time (datetime or HH:MM)
     * @param string $shiftMulai Shift start time (HH:MM:SS or HH:MM)
     * @param string $shiftSelesai Shift end time (HH:MM:SS or HH:MM)
     * @return string Status: hadir, terlambat, pulang_cepat
     */
    private function calculateStatus($jamMasuk, $jamKeluar, $shiftMulai, $shiftSelesai)
    {
        try {
            // Parse times - handle both datetime and time-only formats
            if (strlen($jamMasuk) > 8) {
                // Datetime format: 2026-01-15 07:00:00
                $masuk = Carbon::parse($jamMasuk);
            } else {
                // Time only format: 07:00 or 07:00:00
                $masuk = Carbon::createFromFormat('H:i', substr($jamMasuk, 0, 5));
            }
            
            if ($jamKeluar) {
                if (strlen($jamKeluar) > 8) {
                    $keluar = Carbon::parse($jamKeluar);
                } else {
                    $keluar = Carbon::createFromFormat('H:i', substr($jamKeluar, 0, 5));
                }
            } else {
                $keluar = null;
            }
            
            $shiftStart = Carbon::createFromFormat('H:i', substr($shiftMulai, 0, 5));
            $shiftEnd = Carbon::createFromFormat('H:i', substr($shiftSelesai, 0, 5));
            
            // Toleransi: 15 menit untuk terlambat, 15 menit untuk pulang cepat
            $toleransiTerlambat = 15; // minutes
            $toleransiPulangCepat = 15; // minutes
            
            $isTerlambat = false;
            $isPulangCepat = false;
            
            // Extract time only for comparison
            $masukTime = Carbon::createFromFormat('H:i', $masuk->format('H:i'));
            $keluarTime = $keluar ? Carbon::createFromFormat('H:i', $keluar->format('H:i')) : null;
            
            // Check terlambat: jam masuk > shift mulai + toleransi
            $batasTerlambat = $shiftStart->copy()->addMinutes($toleransiTerlambat);
            if ($masukTime->gt($batasTerlambat)) {
                $isTerlambat = true;
            }
            
            // Check pulang cepat: jam keluar < shift selesai - toleransi
            if ($keluarTime) {
                $batasPulangCepat = $shiftEnd->copy()->subMinutes($toleransiPulangCepat);
                
                // Handle overnight shift
                if ($shiftEnd->lt($shiftStart)) {
                    $keluarTime->addDay();
                    $shiftEnd->addDay();
                    $batasPulangCepat = $shiftEnd->copy()->subMinutes($toleransiPulangCepat);
                }
                
                if ($keluarTime->lt($batasPulangCepat)) {
                    $isPulangCepat = true;
                }
            }
            
            // Determine final status
            if ($isTerlambat && $isPulangCepat) {
                // Both violations occurred
                return 'terlambat_pulang_cepat';
            } elseif ($isTerlambat) {
                return 'terlambat';
            } elseif ($isPulangCepat) {
                return 'pulang_cepat';
            } else {
                return 'hadir';
            }
            
        } catch (\Exception $e) {
            // If calculation fails, default to hadir
            return 'hadir';
        }
    }
}
