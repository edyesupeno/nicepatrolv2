<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Patroli;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class PatroliController extends Controller
{
    public function index()
    {
        $patrolis = Patroli::with(['lokasi:id,nama_lokasi'])
            ->where('user_id', auth()->id())
            ->orderBy('tanggal_patroli', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(20);

        return view('mobile.patroli.index', compact('patrolis'));
    }

    public function create()
    {
        $lokasis = Lokasi::select('id', 'nama_lokasi')
            ->where('is_active', true)
            ->orderBy('nama_lokasi')
            ->get();

        return view('mobile.patroli.create', compact('lokasis'));
    }
}
