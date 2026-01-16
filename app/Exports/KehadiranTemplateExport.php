<?php

namespace App\Exports;

use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\JadwalShift;
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

class KehadiranTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
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
        
        if ($this->perusahaanId) {
            $query->where('perusahaan_id', $this->perusahaanId);
        }
        
        $karyawans = $query->orderBy('nama_lengkap')->get();
        
        $data = new Collection();
        
        // Add 4 empty rows for title, period, instructions, and header
        $data->push(['']); // Row 1 - Title
        $data->push(['']); // Row 2 - Period
        $data->push(['']); // Row 3 - Instructions
        
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
            // Sample data
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
                
                // Add empty cells for each date
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
                'startColor' => ['rgb' => '667eea'],
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
            $widths[$this->getColumnLetter(4 + $i)] = 15;
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
                $sheet->setCellValue('A1', 'TEMPLATE IMPORT KEHADIRAN KARYAWAN');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '764ba2']],
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
                $sheet->setCellValue('A3', 'Petunjuk: Isi status kehadiran (H=Hadir, A=Alpa, I=Izin, S=Sakit, C=Cuti) atau jam custom (08:00-17:00). Status H akan auto-fetch jam dari shift. Jika shift OFF/HL akan otomatis di-skip. Kosongkan cell jika tidak ada data. Jangan ubah kolom NIK, Nama, Jabatan.');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'wrapText' => true],
                ]);
                
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(40);
                
                // Get row count
                $query = Karyawan::withoutGlobalScope('perusahaan')
                    ->where('project_id', $this->projectId)
                    ->where('is_active', true);
                
                if ($this->perusahaanId) {
                    $query->where('perusahaan_id', $this->perusahaanId);
                }
                
                $karyawanCount = $query->count();
                if ($karyawanCount == 0) {
                    $karyawanCount = 1;
                }
                
                $lastRow = 4 + $karyawanCount;
                
                // Add dropdown validation for date columns
                $statusList = '"H,A,I,S,C"'; // H=Hadir, A=Alpa, I=Izin, S=Sakit, C=Cuti
                
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
                        $validation->setError('Pilih status atau isi jam (08:00-17:00)');
                        $validation->setPromptTitle('Status Kehadiran');
                        $validation->setPrompt('H=Hadir, A=Alpa, I=Izin, S=Sakit, C=Cuti atau isi jam custom');
                        $validation->setFormula1($statusList);
                    }
                }
                
                // Add legend at the bottom
                $legendRow = $lastRow + 2;
                $sheet->setCellValue('A' . $legendRow, 'KETERANGAN STATUS:');
                $sheet->getStyle('A' . $legendRow)->getFont()->setBold(true);
                
                $legendRow++;
                $sheet->setCellValue('A' . $legendRow, 'H = Hadir (jam otomatis dari shift)');
                $legendRow++;
                $sheet->setCellValue('A' . $legendRow, 'A = Alpa');
                $legendRow++;
                $sheet->setCellValue('A' . $legendRow, 'I = Izin');
                $legendRow++;
                $sheet->setCellValue('A' . $legendRow, 'S = Sakit');
                $legendRow++;
                $sheet->setCellValue('A' . $legendRow, 'C = Cuti');
                $legendRow++;
                $sheet->setCellValue('A' . $legendRow, 'Custom: 08:00-17:00 (jam masuk-jam keluar)');
                $legendRow++;
                $sheet->setCellValue('A' . $legendRow, 'Shift OFF/HL akan otomatis di-skip (tidak perlu diisi)');
                
                // Freeze panes
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
