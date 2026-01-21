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
use App\Models\Karyawan;
use Illuminate\Support\Collection;

class KaryawanExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $projectId;
    protected $perusahaanId;
    protected $filters;

    public function __construct($projectId, $perusahaanId, $filters = [])
    {
        $this->projectId = $projectId;
        $this->perusahaanId = $perusahaanId;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Karyawan::with(['project', 'jabatan', 'user'])
            ->where('perusahaan_id', $this->perusahaanId);

        // Filter by project if specified
        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }

        // Apply additional filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_karyawan', 'like', "%{$search}%")
                  ->orWhere('nik_ktp', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($this->filters['status_karyawan'])) {
            $query->where('status_karyawan', $this->filters['status_karyawan']);
        }

        if (!empty($this->filters['jabatan_id'])) {
            $query->where('jabatan_id', $this->filters['jabatan_id']);
        }

        if (isset($this->filters['is_active']) && $this->filters['is_active'] !== '') {
            $query->where('is_active', $this->filters['is_active']);
        }

        $karyawans = $query->orderBy('nama_lengkap')->get();

        return $karyawans->map(function ($karyawan) {
            return [
                $karyawan->nik_karyawan,
                $karyawan->nama_lengkap,
                $karyawan->user->email ?? '-',
                $karyawan->telepon ?? '-',
                $karyawan->project->nama ?? '-',
                $karyawan->jabatan->nama ?? '-',
                $karyawan->status_karyawan,
                $karyawan->jenis_kelamin ?? '-',
                $karyawan->status_perkawinan ?? '-',
                $karyawan->jumlah_tanggungan ?? '0',
                $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('Y-m-d') : '-',
                $karyawan->tempat_lahir ?? '-',
                $karyawan->tanggal_masuk ? $karyawan->tanggal_masuk->format('Y-m-d') : '-',
                $karyawan->tanggal_keluar ? $karyawan->tanggal_keluar->format('Y-m-d') : '-',
                $karyawan->is_active ? 'Aktif' : 'Tidak Aktif',
                $karyawan->nik_ktp ?? '-',
                $karyawan->alamat ?? '-',
                $karyawan->kota ?? '-',
                $karyawan->provinsi ?? '-',
                $karyawan->gaji_pokok ? 'Rp ' . number_format($karyawan->gaji_pokok, 0, ',', '.') : '-',
                $karyawan->user->role ?? '-',
                $karyawan->created_at ? $karyawan->created_at->format('Y-m-d H:i:s') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No Badge',
            'Nama Lengkap',
            'Email',
            'No. Telepon',
            'Project',
            'Jabatan',
            'Status Karyawan',
            'Jenis Kelamin',
            'Status Perkawinan',
            'Jumlah Tanggungan',
            'Tanggal Lahir',
            'Tempat Lahir',
            'Tanggal Masuk',
            'Tanggal Keluar',
            'Status Aktif',
            'NIK KTP',
            'Alamat',
            'Kota',
            'Provinsi',
            'Gaji Pokok',
            'Role',
            'Tanggal Dibuat',
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
            'A' => 18,  // No Badge
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
            'N' => 15,  // Tanggal Keluar
            'O' => 12,  // Status Aktif
            'P' => 20,  // NIK KTP
            'Q' => 30,  // Alamat
            'R' => 15,  // Kota
            'S' => 15,  // Provinsi
            'T' => 18,  // Gaji Pokok
            'U' => 18,  // Role
            'V' => 20,  // Tanggal Dibuat
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Get the highest row and column
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // Add border to all data cells
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);
                
                // Auto-fit row height
                for ($i = 1; $i <= $highestRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(-1);
                }
                
                // Wrap text for address column
                $sheet->getStyle('Q:Q')->getAlignment()->setWrapText(true);
            },
        ];
    }
}