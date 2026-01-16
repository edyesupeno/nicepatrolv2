<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Project;
use App\Models\Jabatan;
use App\Models\StatusKaryawan;

class KaryawanTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $projectId;
    protected $perusahaanId;

    public function __construct($projectId, $perusahaanId)
    {
        $this->projectId = $projectId;
        $this->perusahaanId = $perusahaanId;
    }

    public function collection()
    {
        // Return empty collection for template
        return collect([
            [
                'CONTOH001',
                'John Doe',
                'john.doe@example.com',
                '081234567890',
                'Office Pekanbaru',
                'Security Officer',
                'Kontrak',
                'Laki-laki',
                'TK',
                '0',
                '1990-01-15',
                'Jakarta',
                '2024-01-01',
                '2024-12-31',
                'Aktif',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'NIK Karyawan *',
            'Nama Lengkap *',
            'Email *',
            'No. Telepon',
            'Project *',
            'Jabatan *',
            'Status Karyawan *',
            'Jenis Kelamin *',
            'Status Perkawinan *',
            'Jumlah Tanggungan *',
            'Tanggal Lahir',
            'Tempat Lahir',
            'Tanggal Masuk *',
            'Habis Kontrak',
            'Status *',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
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
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,  // NIK
            'B' => 25,  // Nama
            'C' => 25,  // Email
            'D' => 18,  // Telepon
            'E' => 20,  // Project
            'F' => 20,  // Jabatan
            'G' => 18,  // Status Karyawan
            'H' => 15,  // Jenis Kelamin
            'I' => 18,  // Status Perkawinan
            'J' => 18,  // Jumlah Tanggungan
            'K' => 15,  // Tanggal Lahir
            'L' => 20,  // Tempat Lahir
            'M' => 15,  // Tanggal Masuk
            'N' => 15,  // Habis Kontrak
            'O' => 12,  // Status
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Add border to all cells
                $sheet->getStyle('A1:O2')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);
                
                // Get data for dropdowns
                $projects = Project::where('perusahaan_id', $this->perusahaanId)
                    ->orderBy('nama')
                    ->pluck('nama')
                    ->toArray();
                
                $jabatans = Jabatan::where('perusahaan_id', $this->perusahaanId)
                    ->orderBy('nama')
                    ->pluck('nama')
                    ->toArray();
                
                $statusKaryawans = StatusKaryawan::orderBy('nama')
                    ->pluck('nama')
                    ->toArray();
                
                // Add data validation for Project column (E)
                if (!empty($projects)) {
                    $projectList = '"' . implode(',', $projects) . '"';
                    $validation = $sheet->getCell('E2')->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input Error');
                    $validation->setError('Pilih project dari dropdown');
                    $validation->setPromptTitle('Pilih Project');
                    $validation->setPrompt('Pilih project dari dropdown yang tersedia');
                    $validation->setFormula1($projectList);
                    
                    for ($i = 2; $i <= 1000; $i++) {
                        $sheet->getCell('E' . $i)->setDataValidation(clone $validation);
                    }
                }
                
                // Add data validation for Jabatan column (F)
                if (!empty($jabatans)) {
                    $jabatanList = '"' . implode(',', $jabatans) . '"';
                    $validation = $sheet->getCell('F2')->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input Error');
                    $validation->setError('Pilih jabatan dari dropdown');
                    $validation->setPromptTitle('Pilih Jabatan');
                    $validation->setPrompt('Pilih jabatan dari dropdown yang tersedia');
                    $validation->setFormula1($jabatanList);
                    
                    for ($i = 2; $i <= 1000; $i++) {
                        $sheet->getCell('F' . $i)->setDataValidation(clone $validation);
                    }
                }
                
                // Add data validation for Status Karyawan column (G)
                if (!empty($statusKaryawans)) {
                    $statusKaryawanList = '"' . implode(',', $statusKaryawans) . '"';
                    $validation = $sheet->getCell('G2')->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input Error');
                    $validation->setError('Pilih status karyawan dari dropdown');
                    $validation->setPromptTitle('Pilih Status Karyawan');
                    $validation->setPrompt('Pilih status karyawan dari dropdown yang tersedia');
                    $validation->setFormula1($statusKaryawanList);
                    
                    for ($i = 2; $i <= 1000; $i++) {
                        $sheet->getCell('G' . $i)->setDataValidation(clone $validation);
                    }
                }
                
                // Add data validation for Jenis Kelamin column (H)
                $genderValidation = $sheet->getCell('H2')->getDataValidation();
                $genderValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $genderValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $genderValidation->setAllowBlank(false);
                $genderValidation->setShowInputMessage(true);
                $genderValidation->setShowErrorMessage(true);
                $genderValidation->setShowDropDown(true);
                $genderValidation->setErrorTitle('Input Error');
                $genderValidation->setError('Pilih jenis kelamin dari dropdown');
                $genderValidation->setPromptTitle('Pilih Jenis Kelamin');
                $genderValidation->setPrompt('Pilih Laki-laki atau Perempuan');
                $genderValidation->setFormula1('"Laki-laki,Perempuan"');
                
                for ($i = 2; $i <= 1000; $i++) {
                    $sheet->getCell('H' . $i)->setDataValidation(clone $genderValidation);
                }
                
                // Add data validation for Status Perkawinan column (I)
                $statusPerkawinanValidation = $sheet->getCell('I2')->getDataValidation();
                $statusPerkawinanValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $statusPerkawinanValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $statusPerkawinanValidation->setAllowBlank(false);
                $statusPerkawinanValidation->setShowInputMessage(true);
                $statusPerkawinanValidation->setShowErrorMessage(true);
                $statusPerkawinanValidation->setShowDropDown(true);
                $statusPerkawinanValidation->setErrorTitle('Input Error');
                $statusPerkawinanValidation->setError('Pilih status perkawinan dari dropdown');
                $statusPerkawinanValidation->setPromptTitle('Pilih Status Perkawinan');
                $statusPerkawinanValidation->setPrompt('TK = Tidak Kawin, K = Kawin');
                $statusPerkawinanValidation->setFormula1('"TK,K"');
                
                for ($i = 2; $i <= 1000; $i++) {
                    $sheet->getCell('I' . $i)->setDataValidation(clone $statusPerkawinanValidation);
                }
                
                // Add data validation for Jumlah Tanggungan column (J)
                $tanggunganValidation = $sheet->getCell('J2')->getDataValidation();
                $tanggunganValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $tanggunganValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $tanggunganValidation->setAllowBlank(false);
                $tanggunganValidation->setShowInputMessage(true);
                $tanggunganValidation->setShowErrorMessage(true);
                $tanggunganValidation->setShowDropDown(true);
                $tanggunganValidation->setErrorTitle('Input Error');
                $tanggunganValidation->setError('Pilih jumlah tanggungan dari dropdown');
                $tanggunganValidation->setPromptTitle('Pilih Jumlah Tanggungan');
                $tanggunganValidation->setPrompt('Maksimal 3 tanggungan untuk PTKP pajak');
                $tanggunganValidation->setFormula1('"0,1,2,3"');
                
                for ($i = 2; $i <= 1000; $i++) {
                    $sheet->getCell('J' . $i)->setDataValidation(clone $tanggunganValidation);
                }
                
                // Add data validation for Status column (O)
                $statusValidation = $sheet->getCell('O2')->getDataValidation();
                $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $statusValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $statusValidation->setAllowBlank(false);
                $statusValidation->setShowInputMessage(true);
                $statusValidation->setShowErrorMessage(true);
                $statusValidation->setShowDropDown(true);
                $statusValidation->setErrorTitle('Input Error');
                $statusValidation->setError('Pilih status dari dropdown');
                $statusValidation->setPromptTitle('Pilih Status');
                $statusValidation->setPrompt('Pilih Aktif atau Tidak Aktif');
                $statusValidation->setFormula1('"Aktif,Tidak Aktif"');
                
                for ($i = 2; $i <= 1000; $i++) {
                    $sheet->getCell('O' . $i)->setDataValidation(clone $statusValidation);
                }
                
                // Add instructions
                $sheet->setCellValue('A4', 'PETUNJUK PENGISIAN:');
                $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(11);
                
                $instructions = [
                    'A5' => '1. Kolom dengan tanda (*) wajib diisi',
                    'A6' => '2. NIK Karyawan harus unik (tidak boleh sama)',
                    'A7' => '3. Email harus valid dan unik',
                    'A8' => '4. Project: Pilih dari dropdown',
                    'A9' => '5. Jabatan: Pilih dari dropdown',
                    'A10' => '6. Status Karyawan: Pilih dari dropdown (Tetap/Kontrak/Harian/dll)',
                    'A11' => '7. Jenis Kelamin: Pilih dari dropdown (Laki-laki/Perempuan)',
                    'A12' => '8. Status Perkawinan: Pilih dari dropdown (TK = Tidak Kawin, K = Kawin)',
                    'A13' => '9. Jumlah Tanggungan: Pilih dari dropdown (0, 1, 2, 3) - untuk PTKP pajak',
                    'A14' => '10. Tanggal Lahir format: YYYY-MM-DD (contoh: 1990-01-15)',
                    'A15' => '11. Tanggal Masuk format: YYYY-MM-DD (contoh: 2024-01-01)',
                    'A16' => '12. Habis Kontrak format: YYYY-MM-DD (opsional, kosongkan jika tidak ada)',
                    'A17' => '13. Status: Pilih dari dropdown (Aktif/Tidak Aktif)',
                    'A18' => '14. Password default untuk semua user: nicepatrol',
                    'A19' => '15. Hapus baris contoh sebelum import',
                ];
                
                foreach ($instructions as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                    $sheet->getStyle($cell)->getFont()->setSize(9);
                }
                
                // Show available options
                $row = 21;
                
                if (!empty($projects)) {
                    $sheet->setCellValue('A' . $row, 'DAFTAR PROJECT:');
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
                    $row++;
                    foreach ($projects as $project) {
                        $sheet->setCellValue('A' . $row, '- ' . $project);
                        $sheet->getStyle('A' . $row)->getFont()->setSize(9);
                        $row++;
                    }
                    $row++;
                }
                
                if (!empty($jabatans)) {
                    $sheet->setCellValue('A' . $row, 'DAFTAR JABATAN:');
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
                    $row++;
                    foreach ($jabatans as $jabatan) {
                        $sheet->setCellValue('A' . $row, '- ' . $jabatan);
                        $sheet->getStyle('A' . $row)->getFont()->setSize(9);
                        $row++;
                    }
                    $row++;
                }
                
                if (!empty($statusKaryawans)) {
                    $sheet->setCellValue('A' . $row, 'DAFTAR STATUS KARYAWAN:');
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(10);
                    $row++;
                    foreach ($statusKaryawans as $status) {
                        $sheet->setCellValue('A' . $row, '- ' . $status);
                        $sheet->getStyle('A' . $row)->getFont()->setSize(9);
                        $row++;
                    }
                }
            },
        ];
    }
}
