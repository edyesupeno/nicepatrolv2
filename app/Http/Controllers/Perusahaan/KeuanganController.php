<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    /**
     * Under construction page for financial features
     */
    public function underConstruction(Request $request)
    {
        $feature = $request->route()->parameter('feature') ?? $request->get('feature');
        
        $features = [
            'reimbursement-pengajuan' => [
                'title' => 'Pengajuan Reimbursement',
                'description' => 'Fitur untuk mengajukan reimbursement biaya operasional',
                'icon' => 'fas fa-plus-circle',
                'color' => 'blue'
            ],
            'reimbursement-proses' => [
                'title' => 'Proses Reimbursement',
                'description' => 'Fitur untuk memproses dan menyetujui pengajuan reimbursement',
                'icon' => 'fas fa-tasks',
                'color' => 'green'
            ],
            'cash-advance-pengajuan' => [
                'title' => 'Pengajuan Cash Advance',
                'description' => 'Fitur untuk mengajukan uang muka operasional',
                'icon' => 'fas fa-plus-circle',
                'color' => 'purple'
            ],
            'cash-advance-proses' => [
                'title' => 'Proses Cash Advance',
                'description' => 'Fitur untuk memproses dan menyetujui pengajuan cash advance',
                'icon' => 'fas fa-tasks',
                'color' => 'indigo'
            ],
            'rekening' => [
                'title' => 'Rekening',
                'description' => 'Manajemen rekening bank perusahaan dan karyawan',
                'icon' => 'fas fa-university',
                'color' => 'yellow'
            ],
            'laporan-arus-kas' => [
                'title' => 'Laporan Arus Kas',
                'description' => 'Laporan cash flow dan analisis keuangan perusahaan',
                'icon' => 'fas fa-chart-line',
                'color' => 'red'
            ]
        ];

        $featureData = $features[$feature] ?? [
            'title' => 'Fitur Keuangan',
            'description' => 'Fitur keuangan sedang dalam pengembangan',
            'icon' => 'fas fa-tools',
            'color' => 'gray'
        ];

        return view('perusahaan.keuangan.under-construction', compact('featureData'));
    }
}