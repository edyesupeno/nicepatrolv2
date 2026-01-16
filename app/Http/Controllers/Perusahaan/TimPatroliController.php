<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimPatroliController extends Controller
{
    public function master()
    {
        return view('perusahaan.tim-patroli.master');
    }

    public function inventaris()
    {
        return view('perusahaan.tim-patroli.inventaris');
    }
}
