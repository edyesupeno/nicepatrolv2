<?php

namespace App\Exports;

use App\Models\Payroll;
use App\Models\PayrollSetting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PayrollExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $periode;
    protected $projectId;
    protected $jabatanId;
    protected $status;
    protected $search;

    public function __construct($periode, $projectId = null, $jabatanId = null, $status = 'all', $search = null)
    {
        $this->periode = $periode;
        $this->projectId = $projectId;
        $this->jabatanId = $jabatanId;
        $this->status = $status;
        $this->search = $search;
    }

    public function collection()
    {
        // Simple query first to avoid complex calculations causing errors
        $query = Payroll::with([
                'karyawan:id,nik_karyawan,nama_lengkap,nama_bank,nomor_rekening,nama_pemilik_rekening',
                'project:id,nama'
            ])
            ->where('periode', $this->periode);

        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }

        if ($this->jabatanId) {
            $query->whereHas('karyawan', function($q) {
                $q->where('jabatan_id', $this->jabatanId);
            });
        }

        if ($this->status != 'all') {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->whereHas('karyawan', function($q) {
                $q->where('nama_lengkap', 'ilike', "%{$this->search}%")
                  ->orWhere('nik_karyawan', 'ilike', "%{$this->search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
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

    public function map($payroll): array
    {
        // Use the stored gaji_netto value for now to avoid calculation errors
        return [
            $payroll->karyawan->nik_karyawan ?? '-',
            $payroll->karyawan->nama_lengkap ?? '-',
            $payroll->project->nama ?? '-',
            $payroll->karyawan->nama_bank ?? '-',
            $payroll->karyawan->nomor_rekening ?? '-',
            $payroll->karyawan->nama_pemilik_rekening ?? '-',
            $payroll->gaji_netto ?? 0
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
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
            ],
            // Data rows styling
            'A:G' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Amount column (G) - right align and number format
            'G:G' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
                'numberFormat' => [
                    'formatCode' => '#,##0',
                ],
            ],
        ];
    }
}