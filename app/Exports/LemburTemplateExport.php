<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class LemburTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $projectId;
    protected $employeeIds;
    protected $startDate;
    protected $endDate;

    public function __construct($projectId = null, $employeeIds = [], $startDate = null, $endDate = null)
    {
        $this->projectId = $projectId;
        $this->employeeIds = is_array($employeeIds) ? $employeeIds : [];
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $data = [];
        
        // If project is specified, get sample data from that project
        if ($this->projectId) {
            $project = Project::find($this->projectId);
            
            $karyawanQuery = Karyawan::where('project_id', $this->projectId)
                ->where('is_active', true);
                
            // If specific employees are selected, filter by them
            if (!empty($this->employeeIds)) {
                $karyawanQuery->whereIn('id', $this->employeeIds);
            }
            
            $karyawans = $karyawanQuery->orderBy('nama_lengkap')->get();
                
            if ($project && $karyawans->count() > 0) {
                $sampleDate = $this->startDate ? Carbon::parse($this->startDate)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
                
                foreach ($karyawans as $index => $karyawan) {
                    $data[] = [
                        $karyawan->nik_karyawan,
                        $karyawan->nama_lengkap,
                        $project->nama,
                        $sampleDate,
                        '18:00',
                        '20:00',
                        'Menyelesaikan pekerjaan tambahan',
                        'Menyelesaikan laporan dan dokumentasi project'
                    ];
                    
                    // Add one day for next sample (but don't exceed end date)
                    if ($this->endDate) {
                        $nextDate = Carbon::parse($sampleDate)->addDay();
                        if ($nextDate->lte(Carbon::parse($this->endDate))) {
                            $sampleDate = $nextDate->format('Y-m-d');
                        }
                    } else {
                        $sampleDate = Carbon::parse($sampleDate)->addDay()->format('Y-m-d');
                    }
                }
            }
        }
        
        // If no project data or empty, use default samples
        if (empty($data)) {
            $sampleDate = $this->startDate ? Carbon::parse($this->startDate)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
            $projectName = $this->projectId ? (Project::find($this->projectId)->nama ?? 'Project Sample') : 'Project Sample';
            
            $data = [
                [
                    'BBB1881',
                    'John Doe',
                    $projectName,
                    $sampleDate,
                    '18:00',
                    '20:00',
                    'Menyelesaikan laporan bulanan',
                    'Membuat laporan keuangan dan analisis data'
                ],
                [
                    'BBB1884',
                    'Jane Smith',
                    $projectName,
                    Carbon::parse($sampleDate)->addDay()->format('Y-m-d'),
                    '19:00',
                    '22:00',
                    'Maintenance server',
                    'Update sistem dan backup database'
                ]
            ];
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'No Badge',
            'Nama Karyawan',
            'Project',
            'Tanggal Lembur',
            'Jam Mulai',
            'Jam Selesai',
            'Alasan Lembur',
            'Deskripsi Pekerjaan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'], // Green-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Data rows style
        $dataRowCount = count($this->array());
        if ($dataRowCount > 0) {
            $sheet->getStyle('A2:H' . ($dataRowCount + 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // Center align for specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No Badge
        $sheet->getStyle('D:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tanggal, Jam Mulai, Jam Selesai

        // Add instructions below the data
        $instructionRow = $dataRowCount + 3;
        $sheet->setCellValue('A' . $instructionRow, 'PETUNJUK PENGISIAN:');
        $sheet->getStyle('A' . $instructionRow)->getFont()->setBold(true);
        
        $instructions = [
            '1. No Badge: Masukkan nomor badge karyawan yang terdaftar di sistem',
            '2. Nama Karyawan: Nama akan divalidasi dengan No Badge yang diinput',
            '3. Project: Nama project harus sesuai dengan yang ada di sistem',
            '4. Tanggal Lembur: Format YYYY-MM-DD (contoh: 2024-01-15)',
            '5. Jam Mulai/Selesai: Format HH:MM (contoh: 18:00)',
            '6. Tarif lembur akan dihitung otomatis berdasarkan gaji dan hari',
            '7. Pastikan karyawan sudah terdaftar di project yang dipilih',
            '8. Durasi lembur minimal 30 menit, maksimal 12 jam per hari'
        ];
        
        foreach ($instructions as $index => $instruction) {
            $sheet->setCellValue('A' . ($instructionRow + 1 + $index), $instruction);
            $sheet->getStyle('A' . ($instructionRow + 1 + $index))->getFont()->setSize(9);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // No Badge
            'B' => 25,  // Nama Karyawan
            'C' => 20,  // Project
            'D' => 15,  // Tanggal Lembur
            'E' => 12,  // Jam Mulai
            'F' => 12,  // Jam Selesai
            'G' => 25,  // Alasan Lembur
            'H' => 35,  // Deskripsi Pekerjaan
        ];
    }

    public function title(): string
    {
        $title = 'Template Import Lembur';
        
        if ($this->projectId) {
            $project = Project::find($this->projectId);
            if ($project) {
                $title .= ' - ' . $project->nama;
            }
        }
        
        if ($this->startDate && $this->endDate) {
            $title .= ' (' . Carbon::parse($this->startDate)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDate)->format('d/m/Y') . ')';
        }
        
        return $title;
    }
}