<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\MutasiAset;
use App\Models\DataAset;
use App\Models\AsetKendaraan;
use App\Models\Karyawan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class MutasiAsetController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = MutasiAset::with(['karyawan', 'disetujuiOleh', 'projectAsal', 'projectTujuan'])
                ->orderBy('created_at', 'desc');

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_mutasi', 'like', "%{$search}%")
                      ->orWhereHas('karyawan', function ($kq) use ($search) {
                          $kq->where('nama_lengkap', 'like', "%{$search}%");
                      })
                      ->orWhereHas('projectAsal', function ($pq) use ($search) {
                          $pq->where('nama', 'like', "%{$search}%");
                      })
                      ->orWhereHas('projectTujuan', function ($pq) use ($search) {
                          $pq->where('nama', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by asset type
            if ($request->filled('asset_type')) {
                $query->where('asset_type', $request->asset_type);
            }

            // Filter by date range
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_mutasi', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_mutasi', '<=', $request->tanggal_sampai);
            }

            $mutasiAsets = $query->paginate(20)->withQueryString();

            return view('perusahaan.mutasi-aset.index', compact('mutasiAsets'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $projects = Project::select('id', 'nama')
            ->orderBy('nama')
            ->get();

        return view('perusahaan.mutasi-aset.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mutasi' => 'required|date',
            'asset_type' => 'required|in:data_aset,aset_kendaraan',
            'asset_id' => 'required|integer',
            'karyawan_id' => 'required|exists:karyawans,id',
            'project_asal_id' => 'required|exists:projects,id',
            'project_tujuan_id' => 'required|exists:projects,id|different:project_asal_id',
            'alasan_mutasi' => 'required|string',
            'keterangan' => 'nullable|string',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // Validate asset exists and available
        if ($validated['asset_type'] === 'data_aset') {
            $asset = DataAset::find($validated['asset_id']);
            if (!$asset || $asset->status !== 'tersedia') {
                return back()->withErrors(['asset_id' => 'Aset tidak tersedia untuk dimutasi.']);
            }
            // Validate asset is in project_asal
            if ($asset->project_id != $validated['project_asal_id']) {
                return back()->withErrors(['project_asal_id' => 'Aset tidak berada di project asal yang dipilih.']);
            }
        } elseif ($validated['asset_type'] === 'aset_kendaraan') {
            $asset = AsetKendaraan::find($validated['asset_id']);
            if (!$asset || $asset->status_kendaraan !== 'aktif') {
                return back()->withErrors(['asset_id' => 'Kendaraan tidak tersedia untuk dimutasi.']);
            }
            // Validate asset is in project_asal
            if ($asset->project_id != $validated['project_asal_id']) {
                return back()->withErrors(['project_asal_id' => 'Kendaraan tidak berada di project asal yang dipilih.']);
            }
        }

        // Handle file upload
        if ($request->hasFile('dokumen_pendukung')) {
            $file = $request->file('dokumen_pendukung');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('mutasi-aset/dokumen', $filename, 'public');
            $validated['dokumen_pendukung'] = $path;
        }

        $mutasiAset = MutasiAset::create($validated);

        return redirect()->route('perusahaan.mutasi-aset.show', $mutasiAset->hash_id)
            ->with('success', 'Mutasi aset berhasil dibuat dengan nomor: ' . $mutasiAset->nomor_mutasi);
    }

    public function show(MutasiAset $mutasiAset)
    {
        $mutasiAset->load(['karyawan', 'disetujuiOleh', 'projectAsal', 'projectTujuan', 'perusahaan']);
        
        // Load the specific asset based on asset_type
        if ($mutasiAset->asset_type === 'data_aset') {
            $mutasiAset->load('dataAset');
        } elseif ($mutasiAset->asset_type === 'aset_kendaraan') {
            $mutasiAset->load('asetKendaraan');
        }
        
        return view('perusahaan.mutasi-aset.show', compact('mutasiAset'));
    }

    public function edit(MutasiAset $mutasiAset)
    {
        if ($mutasiAset->status !== 'pending') {
            return redirect()->route('perusahaan.mutasi-aset.show', $mutasiAset->hash_id)
                ->with('error', 'Mutasi aset yang sudah diproses tidak dapat diedit.');
        }

        // Load asset relationships
        if ($mutasiAset->asset_type === 'data_aset') {
            $mutasiAset->load('dataAset');
        } elseif ($mutasiAset->asset_type === 'aset_kendaraan') {
            $mutasiAset->load('asetKendaraan');
        }

        $projects = Project::select('id', 'nama')
            ->orderBy('nama')
            ->get();

        return view('perusahaan.mutasi-aset.edit', compact('mutasiAset', 'projects'));
    }

    public function update(Request $request, MutasiAset $mutasiAset)
    {
        if ($mutasiAset->status !== 'pending') {
            return redirect()->route('perusahaan.mutasi-aset.show', $mutasiAset->hash_id)
                ->with('error', 'Mutasi aset yang sudah diproses tidak dapat diedit.');
        }

        $validated = $request->validate([
            'tanggal_mutasi' => 'required|date',
            'asset_type' => 'required|in:data_aset,aset_kendaraan',
            'asset_id' => 'required|integer',
            'karyawan_id' => 'required|exists:karyawans,id',
            'project_asal_id' => 'required|exists:projects,id',
            'project_tujuan_id' => 'required|exists:projects,id|different:project_asal_id',
            'alasan_mutasi' => 'required|string',
            'keterangan' => 'nullable|string',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // Handle file upload
        if ($request->hasFile('dokumen_pendukung')) {
            // Delete old file
            if ($mutasiAset->dokumen_pendukung) {
                Storage::disk('public')->delete($mutasiAset->dokumen_pendukung);
            }

            $file = $request->file('dokumen_pendukung');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('mutasi-aset/dokumen', $filename, 'public');
            $validated['dokumen_pendukung'] = $path;
        }

        $mutasiAset->update($validated);

        return redirect()->route('perusahaan.mutasi-aset.show', $mutasiAset->hash_id)
            ->with('success', 'Mutasi aset berhasil diperbarui.');
    }

    public function destroy(MutasiAset $mutasiAset)
    {
        if ($mutasiAset->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Mutasi aset yang sudah diproses tidak dapat dihapus.'
            ], 400);
        }

        // Delete file if exists
        if ($mutasiAset->dokumen_pendukung) {
            Storage::disk('public')->delete($mutasiAset->dokumen_pendukung);
        }

        $mutasiAset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mutasi aset berhasil dihapus.'
        ]);
    }

    public function approve(Request $request, MutasiAset $mutasiAset)
    {
        if ($mutasiAset->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Mutasi aset sudah diproses sebelumnya.'
            ], 400);
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan_persetujuan' => 'nullable|string'
        ]);

        $status = $validated['action'] === 'approve' ? 'disetujui' : 'ditolak';

        $mutasiAset->update([
            'status' => $status,
            'disetujui_oleh' => auth()->id(),
            'tanggal_persetujuan' => now(),
            'catatan_persetujuan' => $validated['catatan_persetujuan']
        ]);

        $message = $status === 'disetujui' ? 'Mutasi aset berhasil disetujui.' : 'Mutasi aset berhasil ditolak.';

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function complete(MutasiAset $mutasiAset)
    {
        if ($mutasiAset->status !== 'disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya mutasi yang sudah disetujui yang dapat diselesaikan.'
            ], 400);
        }

        // Update asset project
        $asset = $mutasiAset->asset;
        if ($asset) {
            $asset->update([
                'project_id' => $mutasiAset->project_tujuan_id
            ]);
        }

        $mutasiAset->update(['status' => 'selesai']);

        return response()->json([
            'success' => true,
            'message' => 'Mutasi aset berhasil diselesaikan.'
        ]);
    }

    public function printMutasi(MutasiAset $mutasiAset)
    {
        $mutasiAset->load(['karyawan', 'disetujuiOleh', 'projectAsal', 'projectTujuan', 'perusahaan']);
        
        // Load the specific asset based on asset_type
        if ($mutasiAset->asset_type === 'data_aset') {
            $mutasiAset->load('dataAset');
        } elseif ($mutasiAset->asset_type === 'aset_kendaraan') {
            $mutasiAset->load('asetKendaraan');
        }
        
        $pdf = Pdf::loadView('perusahaan.mutasi-aset.print-pdf', compact('mutasiAset'))
            ->setPaper('a4', 'portrait');

        // Sanitize filename by replacing / and \ with _
        $filename = 'Mutasi_Aset_' . str_replace(['/', '\\'], '_', $mutasiAset->nomor_mutasi) . '.pdf';
        
        return $pdf->download($filename);
    }

    public function laporan(Request $request)
    {
        $query = MutasiAset::with(['karyawan', 'disetujuiOleh', 'projectAsal', 'projectTujuan']);

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_mutasi', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_mutasi', '<=', $request->tanggal_sampai);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mutasiAsets = $query->orderBy('tanggal_mutasi', 'desc')->get();

        if ($request->has('export') && $request->export === 'pdf') {
            $pdf = Pdf::loadView('perusahaan.mutasi-aset.laporan-pdf', compact('mutasiAsets'))
                ->setPaper('a4', 'landscape');

            return $pdf->download('Laporan_Mutasi_Aset_' . now()->format('Y-m-d') . '.pdf');
        }

        return view('perusahaan.mutasi-aset.laporan', compact('mutasiAsets'));
    }

    public function getAssetsByProject(Request $request)
    {
        $projectId = $request->get('project_id');
        $assetType = $request->get('asset_type');
        $currentAssetId = $request->get('current_asset_id'); // For edit mode
        $search = $request->get('search', ''); // For search functionality

        if (!$projectId || !$assetType) {
            return response()->json([
                'success' => false,
                'message' => 'Project ID dan Asset Type harus diisi'
            ]);
        }

        $assets = [];

        if ($assetType === 'data_aset') {
            $query = DataAset::select('id', 'nama_aset', 'kode_aset', 'status', 'project_id')
                ->where('project_id', $projectId);
            
            // Include available assets or current asset (for edit mode)
            if ($currentAssetId) {
                $query->where(function ($q) use ($currentAssetId) {
                    $q->where('status', 'tersedia')
                      ->orWhere('id', $currentAssetId);
                });
            } else {
                $query->where('status', 'tersedia');
            }

            // Add search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_aset', 'like', "%{$search}%")
                      ->orWhere('kode_aset', 'like', "%{$search}%");
                });
            }
            
            $assets = $query->orderBy('nama_aset')
                ->limit(50) // Limit results for performance
                ->get()
                ->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'text' => $asset->nama_aset . ' (' . $asset->kode_aset . ')',
                        'project_id' => $asset->project_id
                    ];
                });
        } elseif ($assetType === 'aset_kendaraan') {
            $query = AsetKendaraan::select('id', 'merk', 'model', 'nomor_polisi', 'status_kendaraan', 'project_id')
                ->where('project_id', $projectId);
            
            // Include available assets or current asset (for edit mode)
            if ($currentAssetId) {
                $query->where(function ($q) use ($currentAssetId) {
                    $q->where('status_kendaraan', 'aktif')
                      ->orWhere('id', $currentAssetId);
                });
            } else {
                $query->where('status_kendaraan', 'aktif');
            }

            // Add search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('merk', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%")
                      ->orWhere('nomor_polisi', 'like', "%{$search}%");
                });
            }
            
            $assets = $query->orderBy('merk')
                ->limit(50) // Limit results for performance
                ->get()
                ->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'text' => $asset->merk . ' ' . $asset->model . ' (' . $asset->nomor_polisi . ')',
                        'project_id' => $asset->project_id
                    ];
                });
        }

        return response()->json([
            'success' => true,
            'data' => $assets
        ]);
    }

    public function searchKaryawan(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = Karyawan::select('id', 'nama_lengkap', 'nik_karyawan')
            ->where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_karyawan', 'like', "%{$search}%");
            });
        }

        $karyawans = $query->orderBy('nama_lengkap')
            ->limit(50) // Limit results for performance
            ->get()
            ->map(function ($karyawan) {
                return [
                    'id' => $karyawan->id,
                    'text' => $karyawan->nama_lengkap . ' (' . $karyawan->nik_karyawan . ')'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $karyawans
        ]);
    }
}