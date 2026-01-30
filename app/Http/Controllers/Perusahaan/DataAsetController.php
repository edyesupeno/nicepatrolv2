<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\DataAset;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class DataAsetController extends Controller
{
    public function index(Request $request)
    {
        $query = DataAset::with(['project:id,nama', 'createdBy:id,name'])
            ->select([
                'id',
                'project_id',
                'created_by',
                'kode_aset',
                'nama_aset',
                'kategori',
                'tanggal_beli',
                'harga_beli',
                'nilai_penyusutan',
                'pic_penanggung_jawab',
                'status',
                'created_at'
            ]);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Get per_page from request, default 20, max 100
        $perPage = min((int) $request->get('per_page', 20), 100);

        $dataAsets = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Preserve filter parameters in pagination links
        $dataAsets->appends($request->query());

        // Data untuk filter
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $kategoriList = DataAset::getKategoriList();
        $statusOptions = DataAset::getStatusOptions();

        return view('perusahaan.data-aset.index', compact(
            'dataAsets',
            'projects',
            'kategoriList',
            'statusOptions'
        ));
    }

    public function create()
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $kategoriList = DataAset::getKategoriList();
        $statusOptions = DataAset::getStatusOptions();

        return view('perusahaan.data-aset.create', compact(
            'projects',
            'kategoriList',
            'statusOptions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_aset' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'tanggal_beli' => 'required|date',
            'harga_beli' => 'required|numeric|min:0',
            'nilai_penyusutan' => 'nullable|numeric|min:0',
            'pic_penanggung_jawab' => 'required|string|max:255',
            'foto_aset' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'catatan_tambahan' => 'nullable|string',
            'status' => 'required|in:ada,rusak,dijual,dihapus',
        ]);

        // WAJIB: Auto-assign perusahaan_id (sesuai project standards)
        if (!auth()->user()->isSuperAdmin()) {
            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        }
        $validated['created_by'] = auth()->id();

        // Handle foto upload
        if ($request->hasFile('foto_aset')) {
            $validated['foto_aset'] = $request->file('foto_aset')->store('data-aset', 'public');
        }

        // Set default nilai penyusutan jika kosong
        if (empty($validated['nilai_penyusutan'])) {
            $validated['nilai_penyusutan'] = 0;
        }

        DataAset::create($validated);

        return redirect()->route('perusahaan.data-aset.index')
            ->with('success', 'Data aset berhasil ditambahkan');
    }

    public function show(DataAset $dataAset)
    {
        $dataAset->load(['project:id,nama', 'createdBy:id,name']);
        
        return view('perusahaan.data-aset.show', compact('dataAset'));
    }

    public function edit(DataAset $dataAset)
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $kategoriList = DataAset::getKategoriList();
        $statusOptions = DataAset::getStatusOptions();

        return view('perusahaan.data-aset.edit', compact(
            'dataAset',
            'projects',
            'kategoriList',
            'statusOptions'
        ));
    }

    public function update(Request $request, DataAset $dataAset)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_aset' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'tanggal_beli' => 'required|date',
            'harga_beli' => 'required|numeric|min:0',
            'nilai_penyusutan' => 'nullable|numeric|min:0',
            'pic_penanggung_jawab' => 'required|string|max:255',
            'foto_aset' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'catatan_tambahan' => 'nullable|string',
            'status' => 'required|in:ada,rusak,dijual,dihapus',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto_aset')) {
            // Delete old foto
            if ($dataAset->foto_aset) {
                Storage::disk('public')->delete($dataAset->foto_aset);
            }
            $validated['foto_aset'] = $request->file('foto_aset')->store('data-aset', 'public');
        }

        // Set default nilai penyusutan jika kosong
        if (empty($validated['nilai_penyusutan'])) {
            $validated['nilai_penyusutan'] = 0;
        }

        $dataAset->update($validated);

        return redirect()->route('perusahaan.data-aset.index')
            ->with('success', 'Data aset berhasil diperbarui');
    }

    public function destroy(DataAset $dataAset)
    {
        // Delete foto if exists
        if ($dataAset->foto_aset) {
            Storage::disk('public')->delete($dataAset->foto_aset);
        }

        $dataAset->delete();

        return redirect()->route('perusahaan.data-aset.index')
            ->with('success', 'Data aset berhasil dihapus');
    }

    // API untuk autocomplete kategori
    public function getKategoriSuggestions(Request $request)
    {
        $search = $request->get('q', '');
        
        $kategoriList = DataAset::select('kategori')
            ->distinct()
            ->where('kategori', 'like', "%{$search}%")
            ->orderBy('kategori')
            ->limit(10)
            ->pluck('kategori');

        return response()->json($kategoriList);
    }

    // API untuk create kategori baru jika tidak ada
    public function createKategori(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:100'
        ]);

        // Check if kategori already exists
        $exists = DataAset::where('kategori', $validated['kategori'])->exists();
        
        if (!$exists) {
            // Return success - kategori akan dibuat saat aset disimpan
            return response()->json([
                'success' => true,
                'message' => 'Kategori baru akan ditambahkan',
                'kategori' => $validated['kategori']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kategori sudah ada',
            'kategori' => $validated['kategori']
        ]);
    }

    // Export single asset label to PDF
    public function exportLabel(DataAset $dataAset)
    {
        return $this->generateLabelPDF(collect([$dataAset]), 'single');
    }

    // Export multiple asset labels to PDF
    public function exportLabels(Request $request)
    {
        $validated = $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'required|string' // Hash IDs
        ]);

        // Decode hash IDs to get actual IDs
        $actualIds = [];
        foreach ($validated['asset_ids'] as $hashId) {
            $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
            if ($id) {
                $actualIds[] = $id;
            }
        }

        if (empty($actualIds)) {
            return back()->with('error', 'Tidak ada aset yang valid dipilih');
        }

        $dataAsets = DataAset::whereIn('id', $actualIds)->get();
        
        if ($dataAsets->isEmpty()) {
            return back()->with('error', 'Aset tidak ditemukan');
        }

        return $this->generateLabelPDF($dataAsets, 'multiple');
    }

    // Export all filtered assets labels to PDF
    public function exportAllLabels(Request $request)
    {
        $query = DataAset::query();

        // Apply same filters as index
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $dataAsets = $query->orderBy('created_at', 'desc')->get();

        if ($dataAsets->isEmpty()) {
            return back()->with('error', 'Tidak ada aset yang ditemukan untuk diekspor');
        }

        return $this->generateLabelPDF($dataAsets, 'all');
    }

    // Generate PDF with barcode labels
    private function generateLabelPDF($dataAsets, $type = 'single')
    {
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = [];

        foreach ($dataAsets as $aset) {
            // Generate barcode for kode aset
            $barcode = base64_encode($generator->getBarcode($aset->kode_aset, $generator::TYPE_CODE_128));
            
            $barcodeData[] = [
                'aset' => $aset,
                'barcode' => $barcode
            ];
        }

        $pdf = PDF::loadView('perusahaan.data-aset.labels-pdf', [
            'barcodeData' => $barcodeData,
            'type' => $type,
            'totalLabels' => count($barcodeData),
            'generatedAt' => now()->format('d/m/Y H:i'),
            'perusahaan' => auth()->user()->perusahaan->nama
        ]);

        // Set paper size dan orientation untuk label
        $pdf->setPaper('A4', 'portrait');

        $filename = match($type) {
            'single' => 'label-aset-' . $dataAsets->first()->kode_aset . '.pdf',
            'multiple' => 'label-aset-multiple-' . now()->format('Y-m-d-H-i-s') . '.pdf',
            'all' => 'label-aset-all-' . now()->format('Y-m-d-H-i-s') . '.pdf',
            default => 'label-aset.pdf'
        };

        return $pdf->download($filename);
    }
}