<?php

namespace App\Helpers;

use App\Models\KomponenPayroll;

class PayrollCalculationHelper
{
    /**
     * Calculate component value with maximum limit applied
     * 
     * @param KomponenPayroll $komponen
     * @param float $gajiPokok
     * @param int $hariMasuk
     * @param int $hariLembur
     * @return float
     */
    public static function calculateKomponenValue(
        KomponenPayroll $komponen, 
        float $gajiPokok = 0, 
        int $hariMasuk = 0, 
        int $hariLembur = 0
    ): float {
        $nilai = 0;
        
        switch ($komponen->tipe_perhitungan) {
            case 'Tetap':
                $nilai = $komponen->nilai;
                break;
                
            case 'Persentase':
                $nilai = ($komponen->nilai / 100) * $gajiPokok;
                break;
                
            case 'Per Hari Masuk':
                $nilai = $komponen->nilai * $hariMasuk;
                
                // Apply maximum limit if set
                if ($komponen->nilai_maksimal) {
                    $nilai = min($nilai, $komponen->nilai_maksimal);
                }
                break;
                
            case 'Lembur Per Hari':
                $nilai = $komponen->nilai * $hariLembur;
                
                // Apply maximum limit if set
                if ($komponen->nilai_maksimal) {
                    $nilai = min($nilai, $komponen->nilai_maksimal);
                }
                break;
        }
        
        return round($nilai, 2);
    }
    
    /**
     * Get calculation explanation for display
     * 
     * @param KomponenPayroll $komponen
     * @param float $gajiPokok
     * @param int $hariMasuk
     * @param int $hariLembur
     * @return string
     */
    public static function getCalculationExplanation(
        KomponenPayroll $komponen, 
        float $gajiPokok = 0, 
        int $hariMasuk = 0, 
        int $hariLembur = 0
    ): string {
        $nilai = self::calculateKomponenValue($komponen, $gajiPokok, $hariMasuk, $hariLembur);
        
        switch ($komponen->tipe_perhitungan) {
            case 'Tetap':
                return "Rp " . number_format($komponen->nilai, 0, ',', '.');
                
            case 'Persentase':
                return "{$komponen->nilai}% × Rp " . number_format($gajiPokok, 0, ',', '.') . 
                       " = Rp " . number_format($nilai, 0, ',', '.');
                
            case 'Per Hari Masuk':
                $baseCalculation = "Rp " . number_format($komponen->nilai, 0, ',', '.') . 
                                 " × {$hariMasuk} hari = Rp " . number_format($komponen->nilai * $hariMasuk, 0, ',', '.');
                
                if ($komponen->nilai_maksimal && ($komponen->nilai * $hariMasuk) > $komponen->nilai_maksimal) {
                    return $baseCalculation . " (dibatasi max Rp " . number_format($komponen->nilai_maksimal, 0, ',', '.') . 
                           ") = Rp " . number_format($nilai, 0, ',', '.');
                }
                
                return $baseCalculation;
                
            case 'Lembur Per Hari':
                $baseCalculation = "Rp " . number_format($komponen->nilai, 0, ',', '.') . 
                                 " × {$hariLembur} hari = Rp " . number_format($komponen->nilai * $hariLembur, 0, ',', '.');
                
                if ($komponen->nilai_maksimal && ($komponen->nilai * $hariLembur) > $komponen->nilai_maksimal) {
                    return $baseCalculation . " (dibatasi max Rp " . number_format($komponen->nilai_maksimal, 0, ',', '.') . 
                           ") = Rp " . number_format($nilai, 0, ',', '.');
                }
                
                return $baseCalculation;
        }
        
        return "Rp " . number_format($nilai, 0, ',', '.');
    }
}