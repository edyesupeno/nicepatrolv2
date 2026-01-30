<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\AsetKendaraan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class AsetKendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = AsetKendaraan::with([
                'project' => function($query) {
                    $query->withoutGlobalScope('project_access')->select('id', 'nama');
                },
                'createdBy:id,name'
            ])
            ->select([
                'id',
                'project_id',
                'created_by',
                'kode_kendaraan',
                'jenis_kendaraan',
                'merk',
                'model',
                'tahun_pembuatan',
                'warna',
                'nomor_polisi',
                'tanggal_pembelian',
                'harga_pembelian',
                'nilai_penyusutan',
                'driver_utama',
                'status_kendaraan',
                'tanggal_berlaku_stnk',
                'tanggal_berlaku_asuransi',
                'jatuh_tempo_pajak',
                'created_at'
            ]);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by jenis kendaraan
        if ($request->filled('jenis_kendaraan')) {
            $query->where('jenis_kendaraan', $request->jenis_kendaraan);
        }

        // Filter by status
        if ($request->filled('status_kendaraan')) {
            $query->where('status_kendaraan', $request->status_kendaraan);
        }

        // Filter expiring documents
        if ($request->filled('expiring_soon')) {
            $query->expiringSoon(30);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Get per_page from request, default 20, max 100
        $perPage = min((int) $request->get('per_page', 20), 100);

        $kendaraans = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        // Preserve filter parameters in pagination links
        $kendaraans->appends($request->query());

        // Data untuk filter - load all projects without project_access scope
        $projects = Project::withoutGlobalScope('project_access')
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();
        $jenisOptions = AsetKendaraan::getJenisOptions();
        $statusOptions = AsetKendaraan::getStatusOptions();

        return view('perusahaan.aset-kendaraan.index', compact(
            'kendaraans',
            'projects',
            'jenisOptions',
            'statusOptions'
        ));
    }

    public function create()
    {
        $projects = Project::withoutGlobalScope('project_access')
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();
        $jenisOptions = AsetKendaraan::getJenisOptions();
        $statusOptions = AsetKendaraan::getStatusOptions();

        return view('perusahaan.aset-kendaraan.create', compact(
            'projects',
            'jenisOptions',
            'statusOptions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'jenis_kendaraan' => 'required|in:mobil,motor',
            'merk' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'tahun_pembuatan' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'warna' => 'required|string|max:50',
            'nomor_polisi' => 'required|string|max:20|unique:aset_kendaraans,nomor_polisi',
            'nomor_rangka' => 'required|string|max:50|unique:aset_kendaraans,nomor_rangka',
            'nomor_mesin' => 'required|string|max:50|unique:aset_kendaraans,nomor_mesin',
            'tanggal_pembelian' => 'required|date',
            'harga_pembelian' => 'required|numeric|min:0',
            'nilai_penyusutan' => 'nullable|numeric|min:0',
            'nomor_stnk' => 'required|string|max:50',
            'tanggal_berlaku_stnk' => 'required|date|after:today',
            'nomor_bpkb' => 'required|string|max:50',
            'atas_nama_bpkb' => 'required|string|max:255',
            'perusahaan_asuransi' => 'nullable|string|max:255',
            'nomor_polis_asuransi' => 'nullable|string|max:100',
            'tanggal_berlaku_asuransi' => 'nullable|date|after:today',
            'nilai_pajak_tahunan' => 'nullable|numeric|min:0',
            'jatuh_tempo_pajak' => 'nullable|date',
            'kilometer_terakhir' => 'nullable|integer|min:0',
            'tanggal_service_terakhir' => 'nullable|date',
            'tanggal_service_berikutnya' => 'nullable|date|after:tanggal_service_terakhir',
            'driver_utama' => 'nullable|string|max:255',
            'lokasi_parkir' => 'nullable|string|max:255',
            'status_kendaraan' => 'required|in:aktif,maintenance,rusak,dijual,hilang',
            'foto_kendaraan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file_stnk' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'file_bpkb' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'file_asuransi' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'catatan' => 'nullable|string',
        ]);

        // WAJIB: Auto-assign perusahaan_id (sesuai project standards)
        if (!auth()->user()->isSuperAdmin()) {
            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        }
        $validated['created_by'] = auth()->id();

        // Handle file uploads
        if ($request->hasFile('foto_kendaraan')) {
            $validated['foto_kendaraan'] = $request->file('foto_kendaraan')->store('aset-kendaraan/foto', 'public');
        }

        if ($request->hasFile('file_stnk')) {
            $validated['file_stnk'] = $request->file('file_stnk')->store('aset-kendaraan/stnk', 'public');
        }

        if ($request->hasFile('file_bpkb')) {
            $validated['file_bpkb'] = $request->file('file_bpkb')->store('aset-kendaraan/bpkb', 'public');
        }

        if ($request->hasFile('file_asuransi')) {
            $validated['file_asuransi'] = $request->file('file_asuransi')->store('aset-kendaraan/asuransi', 'public');
        }

        // Set default nilai penyusutan jika kosong
        if (empty($validated['nilai_penyusutan'])) {
            $validated['nilai_penyusutan'] = 0;
        }

        AsetKendaraan::create($validated);

        return redirect()->route('perusahaan.aset-kendaraan.index')
            ->with('success', 'Data kendaraan berhasil ditambahkan');
    }

    public function show(AsetKendaraan $asetKendaraan)
    {
        $asetKendaraan->load([
            'project' => function($query) {
                $query->withoutGlobalScope('project_access')->select('id', 'nama');
            },
            'createdBy:id,name'
        ]);
        
        return view('perusahaan.aset-kendaraan.show', compact('asetKendaraan'));
    }

    public function edit(AsetKendaraan $asetKendaraan)
    {
        $projects = Project::withoutGlobalScope('project_access')
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();
        $jenisOptions = AsetKendaraan::getJenisOptions();
        $statusOptions = AsetKendaraan::getStatusOptions();

        return view('perusahaan.aset-kendaraan.edit', compact(
            'asetKendaraan',
            'projects',
            'jenisOptions',
            'statusOptions'
        ));
    }

    public function update(Request $request, AsetKendaraan $asetKendaraan)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'jenis_kendaraan' => 'required|in:mobil,motor',
            'merk' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'tahun_pembuatan' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'warna' => 'required|string|max:50',
            'nomor_polisi' => [
                'required',
                'string',
                'max:20',
                Rule::unique('aset_kendaraans')->ignore($asetKendaraan->id)
            ],
            'nomor_rangka' => [
                'required',
                'string',
                'max:50',
                Rule::unique('aset_kendaraans')->ignore($asetKendaraan->id)
            ],
            'nomor_mesin' => [
                'required',
                'string',
                'max:50',
                Rule::unique('aset_kendaraans')->ignore($asetKendaraan->id)
            ],
            'tanggal_pembelian' => 'required|date',
            'harga_pembelian' => 'required|numeric|min:0',
            'nilai_penyusutan' => 'nullable|numeric|min:0',
            'nomor_stnk' => 'required|string|max:50',
            'tanggal_berlaku_stnk' => 'required|date',
            'nomor_bpkb' => 'required|string|max:50',
            'atas_nama_bpkb' => 'required|string|max:255',
            'perusahaan_asuransi' => 'nullable|string|max:255',
            'nomor_polis_asuransi' => 'nullable|string|max:100',
            'tanggal_berlaku_asuransi' => 'nullable|date',
            'nilai_pajak_tahunan' => 'nullable|numeric|min:0',
            'jatuh_tempo_pajak' => 'nullable|date',
            'kilometer_terakhir' => 'nullable|integer|min:0',
            'tanggal_service_terakhir' => 'nullable|date',
            'tanggal_service_berikutnya' => 'nullable|date|after:tanggal_service_terakhir',
            'driver_utama' => 'nullable|string|max:255',
            'lokasi_parkir' => 'nullable|string|max:255',
            'status_kendaraan' => 'required|in:aktif,maintenance,rusak,dijual,hilang',
            'foto_kendaraan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file_stnk' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'file_bpkb' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'file_asuransi' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'catatan' => 'nullable|string',
        ]);

        // Handle file uploads
        if ($request->hasFile('foto_kendaraan')) {
            // Delete old file
            if ($asetKendaraan->foto_kendaraan) {
                Storage::disk('public')->delete($asetKendaraan->foto_kendaraan);
            }
            $validated['foto_kendaraan'] = $request->file('foto_kendaraan')->store('aset-kendaraan/foto', 'public');
        }

        if ($request->hasFile('file_stnk')) {
            if ($asetKendaraan->file_stnk) {
                Storage::disk('public')->delete($asetKendaraan->file_stnk);
            }
            $validated['file_stnk'] = $request->file('file_stnk')->store('aset-kendaraan/stnk', 'public');
        }

        if ($request->hasFile('file_bpkb')) {
            if ($asetKendaraan->file_bpkb) {
                Storage::disk('public')->delete($asetKendaraan->file_bpkb);
            }
            $validated['file_bpkb'] = $request->file('file_bpkb')->store('aset-kendaraan/bpkb', 'public');
        }

        if ($request->hasFile('file_asuransi')) {
            if ($asetKendaraan->file_asuransi) {
                Storage::disk('public')->delete($asetKendaraan->file_asuransi);
            }
            $validated['file_asuransi'] = $request->file('file_asuransi')->store('aset-kendaraan/asuransi', 'public');
        }

        // Set default nilai penyusutan jika kosong
        if (empty($validated['nilai_penyusutan'])) {
            $validated['nilai_penyusutan'] = 0;
        }

        $asetKendaraan->update($validated);

        return redirect()->route('perusahaan.aset-kendaraan.index')
            ->with('success', 'Data kendaraan berhasil diperbarui');
    }

    public function destroy(AsetKendaraan $asetKendaraan)
    {
        // Delete files if exist
        if ($asetKendaraan->foto_kendaraan) {
            Storage::disk('public')->delete($asetKendaraan->foto_kendaraan);
        }
        if ($asetKendaraan->file_stnk) {
            Storage::disk('public')->delete($asetKendaraan->file_stnk);
        }
        if ($asetKendaraan->file_bpkb) {
            Storage::disk('public')->delete($asetKendaraan->file_bpkb);
        }
        if ($asetKendaraan->file_asuransi) {
            Storage::disk('public')->delete($asetKendaraan->file_asuransi);
        }

        $asetKendaraan->delete();

        return redirect()->route('perusahaan.aset-kendaraan.index')
            ->with('success', 'Data kendaraan berhasil dihapus');
    }

    // API untuk autocomplete merk
    public function getMerkSuggestions(Request $request)
    {
        $search = $request->get('q', '');
        
        $merkList = AsetKendaraan::select('merk')
            ->distinct()
            ->where('merk', 'like', "%{$search}%")
            ->orderBy('merk')
            ->limit(10)
            ->pluck('merk');

        return response()->json($merkList);
    }

    // Dashboard untuk expiring documents
    public function expiringDocuments()
    {
        $expiringSoon = AsetKendaraan::with([
                'project' => function($query) {
                    $query->withoutGlobalScope('project_access')->select('id', 'nama');
                }
            ])
            ->select([
                'id', 'project_id', 'kode_kendaraan', 'merk', 'model', 'nomor_polisi',
                'tanggal_berlaku_stnk', 'tanggal_berlaku_asuransi', 'jatuh_tempo_pajak'
            ])
            ->expiringSoon(30)
            ->orderBy('tanggal_berlaku_stnk')
            ->get();

        return view('perusahaan.aset-kendaraan.expiring', compact('expiringSoon'));
    }

    // Export single vehicle label to PDF
    public function exportLabel(AsetKendaraan $asetKendaraan)
    {
        return $this->generateLabelPDF(collect([$asetKendaraan]), 'single');
    }

    // Export multiple vehicle labels to PDF
    public function exportLabels(Request $request)
    {
        $validated = $request->validate([
            'selected_ids' => 'required|array|min:1',
            'selected_ids.*' => 'required|string'
        ]);

        // Decode hash IDs to get actual IDs
        $actualIds = [];
        foreach ($validated['selected_ids'] as $hashId) {
            $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
            if ($id) {
                $actualIds[] = $id;
            }
        }

        if (empty($actualIds)) {
            return back()->with('error', 'Tidak ada kendaraan yang valid dipilih');
        }

        $kendaraans = AsetKendaraan::with([
                'project' => function($query) {
                    $query->withoutGlobalScope('project_access')->select('id', 'nama');
                }
            ])
            ->whereIn('id', $actualIds)
            ->get();

        return $this->generateLabelPDF($kendaraans, 'multiple');
    }

    // Export all filtered vehicles labels to PDF
    public function exportAllLabels(Request $request)
    {
        $query = AsetKendaraan::with([
            'project' => function($query) {
                $query->withoutGlobalScope('project_access')->select('id', 'nama');
            }
        ]);

        // Apply same filters as index
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('jenis_kendaraan')) {
            $query->where('jenis_kendaraan', $request->jenis_kendaraan);
        }

        if ($request->filled('status_kendaraan')) {
            $query->where('status_kendaraan', $request->status_kendaraan);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $kendaraans = $query->get();

        if ($kendaraans->isEmpty()) {
            return back()->with('error', 'Tidak ada kendaraan yang ditemukan untuk diekspor');
        }

        return $this->generateLabelPDF($kendaraans, 'all');
    }

    // Generate PDF with barcode labels
    private function generateLabelPDF($kendaraans, $type = 'single')
    {
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = [];

        foreach ($kendaraans as $kendaraan) {
            // Generate barcode for kode kendaraan
            $barcode = base64_encode($generator->getBarcode($kendaraan->kode_kendaraan, $generator::TYPE_CODE_128));
            
            $barcodeData[] = [
                'kendaraan' => $kendaraan,
                'barcode' => $barcode
            ];
        }

        $pdf = PDF::loadView('perusahaan.aset-kendaraan.labels-pdf', [
            'barcodeData' => $barcodeData,
            'type' => $type,
            'totalLabels' => count($barcodeData),
            'generatedAt' => now()->format('d/m/Y H:i'),
            'perusahaan' => auth()->user()->perusahaan->nama
        ]);

        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'label-kendaraan-' . now()->format('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
}