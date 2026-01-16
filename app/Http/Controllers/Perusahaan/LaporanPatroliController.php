<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanPatroliController extends Controller
{
    public function insiden()
    {
        return view('perusahaan.laporan-patroli.insiden');
    }

    public function kawasan()
    {
        return view('perusahaan.laporan-patroli.kawasan');
    }

    public function inventaris()
    {
        return view('perusahaan.laporan-patroli.inventaris');
    }

    public function kruChange()
    {
        return view('perusahaan.laporan-patroli.kru-change');
    }
}
