<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\KomponenPayroll;
use Illuminate\Http\Request;

class KomponenPayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = KomponenPayroll::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_komponen', 'ILIKE', "%{$search}%")
                  ->orWhere('kode', 'ILIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
            });
        }
        
        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $aktif = $request->status === 'aktif' ? 1 : 0;
            $query->where('aktif', $aktif);
        }
        
        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        // Filter by project
        if ($request->filled('project_id')) {
            if ($request->project_id === 'global') {
                $query->whereNull('project_id');
            } else {
                $query->where('project_id', $request->project_id);
            }
        }
        
        $komponens = $query->with('project')->orderBy('created_at', 'desc')->get();
        
        // Get projects for dropdown
        $projects = \App\Models\Project::where('perusahaan_id', auth()->user()->perusahaan_id)
            ->orderBy('nama')
            ->get();
        
        return view('perusahaan.payroll.komponen', compact('komponens', 'projects'));
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
            'nilai_maksimal' => 'nullable|numeric|min:0',
            'project_scope' => 'required|in:global,specific',
            'project_id' => 'nullable|exists:projects,id',
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
            'nilai_maksimal.numeric' => 'Nilai maksimal harus berupa angka',
            'nilai_maksimal.min' => 'Nilai maksimal minimal 0',
            'project_scope.required' => 'Cakupan project wajib dipilih',
            'project_id.exists' => 'Project tidak valid',
        ]);

        // Validate project_id when scope is specific
        if ($validated['project_scope'] === 'specific' && empty($validated['project_id'])) {
            return back()->withInput()->withErrors(['project_id' => 'Project wajib dipilih untuk cakupan spesifik']);
        }

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        
        // Set project_id based on scope
        if ($validated['project_scope'] === 'global') {
            $validated['project_id'] = null;
        }
        
        // Remove project_scope from data (not in database)
        unset($validated['project_scope']);
        
        $validated['kena_pajak'] = $request->has('kena_pajak');
        $validated['boleh_edit'] = $request->has('boleh_edit');
        $validated['aktif'] = $request->has('aktif');

        // Only set nilai_maksimal for per-day calculation types
        if (!in_array($validated['tipe_perhitungan'], ['Per Hari Masuk', 'Lembur Per Hari'])) {
            $validated['nilai_maksimal'] = null;
        }

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
            'nilai_maksimal' => 'nullable|numeric|min:0',
            'project_scope' => 'required|in:global,specific',
            'project_id' => 'nullable|exists:projects,id',
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
            'nilai_maksimal.numeric' => 'Nilai maksimal harus berupa angka',
            'nilai_maksimal.min' => 'Nilai maksimal minimal 0',
            'project_scope.required' => 'Cakupan project wajib dipilih',
            'project_id.exists' => 'Project tidak valid',
        ]);

        // Validate project_id when scope is specific
        if ($validated['project_scope'] === 'specific' && empty($validated['project_id'])) {
            return back()->withInput()->withErrors(['project_id' => 'Project wajib dipilih untuk cakupan spesifik']);
        }

        // Set project_id based on scope
        if ($validated['project_scope'] === 'global') {
            $validated['project_id'] = null;
        }
        
        // Remove project_scope from data (not in database)
        unset($validated['project_scope']);

        $validated['kena_pajak'] = $request->has('kena_pajak');
        $validated['boleh_edit'] = $request->has('boleh_edit');
        $validated['aktif'] = $request->has('aktif');

        // Only set nilai_maksimal for per-day calculation types
        if (!in_array($validated['tipe_perhitungan'], ['Per Hari Masuk', 'Lembur Per Hari'])) {
            $validated['nilai_maksimal'] = null;
        }

        $komponenPayroll->update($validated);

        return back()->with('success', 'Komponen payroll berhasil diperbarui');
    }

    public function destroy(KomponenPayroll $komponenPayroll)
    {
        $komponenPayroll->delete();
        return back()->with('success', 'Komponen payroll berhasil dihapus');
    }
}
