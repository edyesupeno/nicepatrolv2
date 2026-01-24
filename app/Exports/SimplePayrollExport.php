<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SimplePayrollExport implements FromCollection, WithHeadings
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

        $payrolls = $query->orderBy('created_at', 'desc')->get();

        // Transform to simple array
        return $payrolls->map(function($payroll) {
            return [
                'no_badge' => $payroll->karyawan->nik_karyawan ?? '-',
                'nama_karyawan' => $payroll->karyawan->nama_lengkap ?? '-',
                'nama_project' => $payroll->project->nama ?? '-',
                'nama_bank' => $payroll->karyawan->nama_bank ?? '-',
                'no_rekening' => $payroll->karyawan->nomor_rekening ?? '-',
                'nama_pemilik_rekening' => $payroll->karyawan->nama_pemilik_rekening ?? '-',
                'gaji_netto' => $payroll->gaji_netto ?? 0
            ];
        });
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
}