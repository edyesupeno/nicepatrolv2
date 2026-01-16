<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatusKaryawanController extends Controller
{
    public function index()
    {
        // Data status karyawan statis
        $statuses = [
            [
                'id' => 1,
                'nama' => 'BURUH HARIAN LEPAS',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'nama' => 'KARYAWAN KONTRAK',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'nama' => 'KARYAWAN KONTRAK (TRAINING)',
                'is_active' => true,
            ],
            [
                'id' => 4,
                'nama' => 'KARYAWAN TETAP',
                'is_active' => true,
            ],
            [
                'id' => 5,
                'nama' => 'MAGANG',
                'is_active' => true,
            ],
            [
                'id' => 6,
                'nama' => 'On Job Training',
                'is_active' => true,
            ],
        ];

        return view('perusahaan.status-karyawan.index', compact('statuses'));
    }
}
