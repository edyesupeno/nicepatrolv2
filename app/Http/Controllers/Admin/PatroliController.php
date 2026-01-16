<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patroli;
use Illuminate\Http\Request;

class PatroliController extends Controller
{
    public function index()
    {
        $patrolis = Patroli::with(['user', 'lokasi', 'details'])->latest()->get();
        return view('admin.patrolis.index', compact('patrolis'));
    }

    public function show(Patroli $patroli)
    {
        $patroli->load(['user', 'lokasi', 'details.checkpoint']);
        return view('admin.patrolis.show', compact('patroli'));
    }

    public function destroy(Patroli $patroli)
    {
        $patroli->delete();

        return redirect()->route('admin.patrolis.index')
            ->with('success', 'Patroli berhasil dihapus');
    }
}
