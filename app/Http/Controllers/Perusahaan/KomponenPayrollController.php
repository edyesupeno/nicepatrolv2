<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\KomponenPayroll;
use Illuminate\Http\Request;

class KomponenPayrollController extends Controller
{
    public function index()
    {
        $komponens = KomponenPayroll::orderBy('created_at', 'desc')->get();
        return view('perusahaan.payroll.komponen', compact('komponens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_komponen' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:komponen_payrolls,kode',
            'jenis' => 'required|in:Tunjangan,Potongan',
            'kategori' => 'required|in:Fixed,Variable',
            'tipe_perhitungan' => 'required|in:Tetap,Persentase,Per Hari Masuk,Lembur Per Hari',
            'nilai' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'kena_pajak' => 'boolean',
            'boleh_edit' => 'boolean',
            'aktif' => 'boolean',
        ], [
            'nama_komponen.required' => 'Nama komponen wajib diisi',
            'kode.required' => 'Kode wajib diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'jenis.required' => 'Jenis wajib dipilih',
            'kategori.required' => 'Kategori wajib dipilih',
            'tipe_perhitungan.required' => 'Tipe perhitungan wajib dipilih',
            'nilai.required' => 'Jumlah tetap wajib diisi',
            'nilai.numeric' => 'Jumlah tetap harus berupa angka',
            'nilai.min' => 'Jumlah tetap minimal 0',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['kena_pajak'] = $request->has('kena_pajak');
        $validated['boleh_edit'] = $request->has('boleh_edit');
        $validated['aktif'] = $request->has('aktif');

        KomponenPayroll::create($validated);

        return back()->with('success', 'Komponen payroll berhasil ditambahkan');
    }

    public function update(Request $request, KomponenPayroll $komponenPayroll)
    {
        $validated = $request->validate([
            'nama_komponen' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:komponen_payrolls,kode,' . $komponenPayroll->id,
            'jenis' => 'required|in:Tunjangan,Potongan',
            'kategori' => 'required|in:Fixed,Variable',
            'tipe_perhitungan' => 'required|in:Tetap,Persentase,Per Hari Masuk,Lembur Per Hari',
            'nilai' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'kena_pajak' => 'boolean',
            'boleh_edit' => 'boolean',
            'aktif' => 'boolean',
        ], [
            'nama_komponen.required' => 'Nama komponen wajib diisi',
            'kode.required' => 'Kode wajib diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'jenis.required' => 'Jenis wajib dipilih',
            'kategori.required' => 'Kategori wajib dipilih',
            'tipe_perhitungan.required' => 'Tipe perhitungan wajib dipilih',
            'nilai.required' => 'Jumlah tetap wajib diisi',
            'nilai.numeric' => 'Jumlah tetap harus berupa angka',
            'nilai.min' => 'Jumlah tetap minimal 0',
        ]);

        $validated['kena_pajak'] = $request->has('kena_pajak');
        $validated['boleh_edit'] = $request->has('boleh_edit');
        $validated['aktif'] = $request->has('aktif');

        $komponenPayroll->update($validated);

        return back()->with('success', 'Komponen payroll berhasil diperbarui');
    }

    public function destroy(KomponenPayroll $komponenPayroll)
    {
        $komponenPayroll->delete();
        return back()->with('success', 'Komponen payroll berhasil dihapus');
    }
}
