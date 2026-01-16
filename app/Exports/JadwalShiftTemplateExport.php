<?php

namespace App\Exports;

use App\Models\Karyawan;
use App\Models\Shift;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class JadwalShiftTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $projectId;
    protected $perusahaanId;
    protected $tanggalMulai;
    protected $tanggalAkhir;
    protected $dates = [];
    
    public function __construct($projectId, $tanggalMulai, $tanggalAkhir, $perusahaanId = null)
    {
        $this->projectId = $projectId;
        $this->perusahaanId = $perusahaanId;
        $this->tanggalMulai = Carbon::parse($tanggalMulai);
        $this->tanggalAkhir = Carbon::parse($tanggalAkhir);
        
        // Generate dates
        $currentDate = $this->tanggalMulai->copy();
        while ($currentDate <= $this->tanggalAkhir) {
            $this->dates[] = $currentDate->copy();
            $currentDate->addDay();
        }
    }
    
    public function collection()
    {
        // Get karyawan from project
        $query = Karyawan::withoutGlobalScope('perusahaan')
            ->with('jabatan')
            ->where('project_id', $this->projectId)
            ->where('is_active', true);
        
        // Add perusahaan filter if provided
        if ($this->perusahaanId) {
            $query->where('perusahaan_id', $this->perusahaanId);
        }
        
        $karyawans = $query->orderBy('nama_lengkap')->get();
        
        $data = new Collection();
        
        // Add 4 empty rows for title, period, instructions, and header
        $data->push(['']); // Row 1 - Title will be added in AfterSheet
        $data->push(['']); // Row 2 - Period will be added in AfterSheet  
        $data->push(['']); // Row 3 - Instructions will be added in AfterSheet
        
        // Row 4 - Header with dates
        $headerRow = ['NIK', 'Nama Karyawan', 'Jabatan'];
        foreach ($this->dates as $date) {
            $dayNames = [
                'Sunday' => 'Min',
                'Monday' => 'Sen', 
                'Tuesday' => 'Sel',
                'Wednesday' => 'Rab',
                'Thursday' => 'Kam',
                'Friday' => 'Jum',
                'Saturday' => 'Sab'
            ];
            $dayIndo = $dayNames[$date->format('l')];
            $headerRow[] = $date->format('d/m/Y') . "\n" . $dayIndo;
        }
        $data->push($headerRow);
        
        if ($karyawans->isEmpty()) {
            // If no karyawan, add sample data
            $row = ['SAMPLE001', 'Contoh Karyawan', 'Contoh Jabatan'];
            foreach ($this->dates as $date) {
                $row[] = '';
            }
            $data->push($row);
        } else {
            foreach ($karyawans as $karyawan) {
                $row = [
                    $karyawan->nik_karyawan,
                    $karyawan->nama_lengkap,
                    $karyawan->jabatan ? $karyawan->jabatan->nama : '-',
                ];
                
                // Add empty cells for each date (will be filled with dropdown)
                foreach ($this->dates as $date) {
                    $row[] = '';
                }
                
                $data->push($row);
            }
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        // Return empty array since we're handling headers in collection
        return [];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Header style (row 4)
        $lastColumn = $this->getColumnLetter(3 + count($this->dates));
        
        $sheet->getStyle('A4:' . $lastColumn . '4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82C8'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        
        $sheet->getRowDimension(4)->setRowHeight(35);
        
        // Center align date columns
        for ($i = 4; $i <= 3 + count($this->dates); $i++) {
            $col = $this->getColumnLetter($i);
            $sheet->getStyle($col . '5:' . $col . '1000')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        return [];
    }
    
    public function columnWidths(): array
    {
        $widths = [
            'A' => 15,  // NIK
            'B' => 25,  // Nama
            'C' => 20,  // Jabatan
        ];
        
        // Date columns
        for ($i = 0; $i < count($this->dates); $i++) {
            $widths[$this->getColumnLetter(4 + $i)] = 12;
        }
        
        return $widths;
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $this->getColumnLetter(3 + count($this->dates));
                
                // Title
                $sheet->mergeCells('A1:' . $lastColumn . '1');
                $sheet->setCellValue('A1', 'TEMPLATE IMPORT JADWAL SHIFT');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2563A8']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Period
                $sheet->mergeCells('A2:' . $lastColumn . '2');
                $sheet->setCellValue('A2', 'Periode: ' . $this->tanggalMulai->format('d M Y') . ' - ' . $this->tanggalAkhir->format('d M Y'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '059669']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Instructions
                $sheet->mergeCells('A3:' . $lastColumn . '3');
                $sheet->setCellValue('A3', 'Petunjuk: Pilih Kode Shift dari dropdown untuk setiap tanggal. Jangan ubah kolom NIK, Nama, dan Jabatan.');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'wrapText' => true],
                ]);
                
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(30);
                
                // Get shifts for dropdown
                $shifts = Shift::withoutGlobalScope('perusahaan')
                    ->where('project_id', $this->projectId)
                    ->orderBy('kode_shift')
                    ->get();
                
                if ($shifts->isNotEmpty()) {
                    $shiftOptions = $shifts->pluck('kode_shift')->toArray();
                    $shiftList = '"' . implode(',', $shiftOptions) . '"';
                    
                    // Get row count
                    $query = Karyawan::withoutGlobalScope('perusahaan')
                        ->where('project_id', $this->projectId)
                        ->where('is_active', true);
                    
                    if ($this->perusahaanId) {
                        $query->where('perusahaan_id', $this->perusahaanId);
                    }
                    
                    $karyawanCount = $query->count();
                    
                    if ($karyawanCount == 0) {
                        $karyawanCount = 1; // For sample data
                    }
                    
                    $lastRow = 4 + $karyawanCount;
                    
                    // Apply dropdown to date columns (starting from column D = index 4)
                    for ($i = 0; $i < count($this->dates); $i++) {
                        $col = $this->getColumnLetter(4 + $i);
                        
                        for ($row = 5; $row <= $lastRow; $row++) {
                            $validation = $sheet->getCell($col . $row)->getDataValidation();
                            $validation->setType(DataValidation::TYPE_LIST);
                            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                            $validation->setAllowBlank(true);
                            $validation->setShowInputMessage(true);
                            $validation->setShowErrorMessage(true);
                            $validation->setShowDropDown(true);
                            $validation->setErrorTitle('Input Error');
                            $validation->setError('Pilih shift dari dropdown');
                            $validation->setPromptTitle('Pilih Shift');
                            $validation->setPrompt('Pilih kode shift dari dropdown');
                            $validation->setFormula1($shiftList);
                        }
                    }
                    
                    // Add shift legend at the bottom
                    $legendRow = $lastRow + 2;
                    $sheet->setCellValue('A' . $legendRow, 'DAFTAR KODE SHIFT:');
                    $sheet->getStyle('A' . $legendRow)->getFont()->setBold(true);
                    
                    $legendRow++;
                    foreach ($shifts as $shift) {
                        $sheet->setCellValue('A' . $legendRow, $shift->kode_shift);
                        $sheet->setCellValue('B' . $legendRow, $shift->nama_shift);
                        $sheet->setCellValue('C' . $legendRow, $shift->jam_mulai . ' - ' . $shift->jam_selesai);
                        $legendRow++;
                    }
                }
                
                // Freeze panes (freeze first 3 columns and header)
                $sheet->freezePane('D5');
            },
        ];
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
