<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\DisposalAset;
use App\Models\DataAset;
use App\Models\AsetKendaraan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DisposalAsetController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DisposalAset::with(['project:id,nama', 'diajukanOleh:id,name', 'disetujuiOleh:id,name'])
                ->orderBy('created_at', 'desc');

            // Filter by project
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by asset type
            if ($request->filled('asset_type')) {
                $query->where('asset_type', $request->asset_type);
            }

            // Filter by jenis disposal
            if ($request->filled('jenis_disposal')) {
                $query->where('jenis_disposal', $request->jenis_disposal);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nomor_disposal', 'like', "%{$search}%")
                      ->orWhere('asset_code', 'like', "%{$search}%")
                      ->orWhere('asset_name', 'like', "%{$search}%")
                      ->orWhere('pembeli', 'like', "%{$search}%");
                });
            }

            $disposalAsets = $query->paginate(20);

            // Get projects for filter
            $projects = Project::select('id', 'nama')->orderBy('nama')->get();

            // Statistics
            $stats = [
                'total' => DisposalAset::count(),
                'pending' => DisposalAset::where('status', 'pending')->count(),
                'approved' => DisposalAset::where('status', 'approved')->count(),
                'completed' => DisposalAset::where('status', 'completed')->count(),
                'total_nilai_disposal' => DisposalAset::where('status', 'completed')->sum('nilai_disposal'),
            ];

            return view('perusahaan.disposal-aset.index', compact('disposalAsets', 'projects', 'stats'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $projects = Project::select('id', 'nama')->orderBy('nama')->get();
            
            // Get asset data if asset_type and asset_id are provided
            $selectedAsset = null;
            if ($request->filled('asset_type') && $request->filled('asset_id')) {
                if ($request->asset_type === 'data_aset') {
                    $selectedAsset = DataAset::find($request->asset_id);
                } elseif ($request->asset_type === 'aset_kendaraan') {
                    $selectedAsset = AsetKendaraan::find($request->asset_id);
                }
            }

            return view('perusahaan.disposal-aset.create', compact('projects', 'selectedAsset'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'asset_type' => 'required|in:data_aset,aset_kendaraan',
                'asset_id' => 'required|integer',
                'tanggal_disposal' => 'required|date',
                'jenis_disposal' => 'required|in:dijual,rusak,hilang,tidak_layak,expired',
                'alasan_disposal' => 'required|string|max:1000',
                'nilai_disposal' => 'nullable|numeric|min:0',
                'pembeli' => 'nullable|string|max:255',
                'catatan' => 'nullable|string|max:1000',
                'foto_kondisi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Get asset data
            $asset = null;
            if ($validated['asset_type'] === 'data_aset') {
                $asset = DataAset::findOrFail($validated['asset_id']);
            } elseif ($validated['asset_type'] === 'aset_kendaraan') {
                $asset = AsetKendaraan::findOrFail($validated['asset_id']);
            }

            if (!$asset) {
                return redirect()->back()->with('error', 'Aset tidak ditemukan.');
            }

            // Check if asset is already disposed
            $existingDisposal = DisposalAset::where('asset_type', $validated['asset_type'])
                ->where('asset_id', $validated['asset_id'])
                ->whereIn('status', ['pending', 'approved', 'completed'])
                ->first();

            if ($existingDisposal) {
                return redirect()->back()->with('error', 'Aset ini sudah dalam proses disposal.');
            }

            DB::beginTransaction();

            // Auto-assign perusahaan_id
            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
            $validated['diajukan_oleh'] = auth()->id();

            // Generate nomor disposal
            $validated['nomor_disposal'] = DisposalAset::generateNomorDisposal($validated['perusahaan_id']);

            // Get asset information
            if ($validated['asset_type'] === 'data_aset') {
                $validated['asset_code'] = $asset->kode_aset;
                $validated['asset_name'] = $asset->nama_aset;
                $validated['nilai_buku'] = $asset->nilai_sekarang;
            } else {
                $validated['asset_code'] = $asset->nomor_polisi;
                $validated['asset_name'] = $asset->merk . ' ' . $asset->model;
                $validated['nilai_buku'] = $asset->nilai_sekarang;
            }

            // Handle photo upload
            if ($request->hasFile('foto_kondisi')) {
                $file = $request->file('foto_kondisi');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('disposal-aset/foto-kondisi', $filename, 'public');
                $validated['foto_kondisi'] = $path;
            }

            $disposalAset = DisposalAset::create($validated);

            DB::commit();

            return redirect()->route('perusahaan.disposal-aset.show', $disposalAset->hash_id)
                ->with('success', 'Pengajuan disposal aset berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membuat disposal aset: ' . $e->getMessage());
        }
    }

    public function show(DisposalAset $disposalAset)
    {
        try {
            $disposalAset->load(['project', 'diajukanOleh', 'disetujuiOleh']);
            return view('perusahaan.disposal-aset.show', compact('disposalAset'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(DisposalAset $disposalAset)
    {
        try {
            // Only allow editing if status is pending
            if ($disposalAset->status !== 'pending') {
                return redirect()->back()->with('error', 'Disposal aset ini tidak dapat diedit.');
            }

            $projects = Project::select('id', 'nama')->orderBy('nama')->get();
            return view('perusahaan.disposal-aset.edit', compact('disposalAset', 'projects'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, DisposalAset $disposalAset)
    {
        try {
            // Only allow updating if status is pending
            if ($disposalAset->status !== 'pending') {
                return redirect()->back()->with('error', 'Disposal aset ini tidak dapat diedit.');
            }

            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'tanggal_disposal' => 'required|date',
                'jenis_disposal' => 'required|in:dijual,rusak,hilang,tidak_layak,expired',
                'alasan_disposal' => 'required|string|max:1000',
                'nilai_disposal' => 'nullable|numeric|min:0',
                'pembeli' => 'nullable|string|max:255',
                'catatan' => 'nullable|string|max:1000',
                'foto_kondisi' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            DB::beginTransaction();

            // Handle photo upload
            if ($request->hasFile('foto_kondisi')) {
                // Delete old photo
                if ($disposalAset->foto_kondisi) {
                    Storage::disk('public')->delete($disposalAset->foto_kondisi);
                }

                $file = $request->file('foto_kondisi');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('disposal-aset/foto-kondisi', $filename, 'public');
                $validated['foto_kondisi'] = $path;
            }

            $disposalAset->update($validated);

            DB::commit();

            return redirect()->route('perusahaan.disposal-aset.show', $disposalAset->hash_id)
                ->with('success', 'Disposal aset berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memperbarui disposal aset: ' . $e->getMessage());
        }
    }

    public function destroy(DisposalAset $disposalAset)
    {
        try {
            // Only allow deletion if status is pending or rejected
            if (!in_array($disposalAset->status, ['pending', 'rejected'])) {
                return redirect()->back()->with('error', 'Disposal aset ini tidak dapat dihapus.');
            }

            DB::beginTransaction();

            // Delete photo if exists
            if ($disposalAset->foto_kondisi) {
                Storage::disk('public')->delete($disposalAset->foto_kondisi);
            }

            $disposalAset->delete();

            DB::commit();

            return redirect()->route('perusahaan.disposal-aset.index')
                ->with('success', 'Disposal aset berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus disposal aset: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, DisposalAset $disposalAset)
    {
        try {
            if ($disposalAset->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Disposal aset ini tidak dapat disetujui.']);
            }

            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'catatan_approval' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

            $status = $validated['action'] === 'approve' ? 'approved' : 'rejected';

            $disposalAset->update([
                'status' => $status,
                'disetujui_oleh' => auth()->id(),
                'tanggal_disetujui' => now(),
                'catatan_approval' => $validated['catatan_approval'],
            ]);

            DB::commit();

            $message = $status === 'approved' ? 'Disposal aset berhasil disetujui.' : 'Disposal aset berhasil ditolak.';

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal memproses approval: ' . $e->getMessage()]);
        }
    }

    public function complete(DisposalAset $disposalAset)
    {
        try {
            if ($disposalAset->status !== 'approved') {
                return response()->json(['success' => false, 'message' => 'Disposal aset belum disetujui.']);
            }

            DB::beginTransaction();

            // Update disposal status
            $disposalAset->update(['status' => 'completed']);

            // Update asset status to disposed
            if ($disposalAset->asset_type === 'data_aset') {
                $asset = DataAset::withoutGlobalScope('perusahaan')->find($disposalAset->asset_id);
                if ($asset) {
                    // Map disposal jenis to valid status values for data aset
                    $statusMapping = [
                        'dijual' => 'dijual',
                        'rusak' => 'rusak',
                        'hilang' => 'dihapus',
                        'tidak_layak' => 'dihapus',
                        'expired' => 'dihapus'
                    ];
                    $asset->update(['status' => $statusMapping[$disposalAset->jenis_disposal]]);
                }
            } elseif ($disposalAset->asset_type === 'aset_kendaraan') {
                $asset = AsetKendaraan::withoutGlobalScope('perusahaan')->find($disposalAset->asset_id);
                if ($asset) {
                    // Map disposal jenis to valid status values for kendaraan
                    $statusMapping = [
                        'dijual' => 'dijual',
                        'rusak' => 'rusak',
                        'hilang' => 'hilang',
                        'tidak_layak' => 'rusak',
                        'expired' => 'rusak'
                    ];
                    $asset->update(['status_kendaraan' => $statusMapping[$disposalAset->jenis_disposal]]);
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Disposal aset berhasil diselesaikan. Aset telah dihapus dari daftar aktif.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menyelesaikan disposal: ' . $e->getMessage()]);
        }
    }

    public function getAssetsByProject(Request $request)
    {
        try {
            $projectId = $request->get('project_id');
            $assetType = $request->get('asset_type');

            if (!$projectId || !$assetType) {
                return response()->json(['success' => false, 'message' => 'Parameter tidak lengkap']);
            }

            $assets = [];

            if ($assetType === 'data_aset') {
                $assets = DataAset::where('project_id', $projectId)
                    ->where('status', 'ada') // Only active assets
                    ->select('id', 'kode_aset', 'nama_aset', 'kategori', 'harga_beli')
                    ->orderBy('nama_aset')
                    ->get()
                    ->map(function($asset) {
                        return [
                            'id' => $asset->id,
                            'code' => $asset->kode_aset,
                            'name' => $asset->nama_aset,
                            'category' => $asset->kategori,
                            'value' => $asset->harga_beli,
                            'display' => $asset->kode_aset . ' - ' . $asset->nama_aset . ' (' . $asset->kategori . ')'
                        ];
                    });
            } elseif ($assetType === 'aset_kendaraan') {
                $assets = AsetKendaraan::where('project_id', $projectId)
                    ->where('status_kendaraan', 'aktif') // Only active vehicles
                    ->select('id', 'nomor_polisi', 'merk', 'model', 'jenis_kendaraan', 'harga_pembelian')
                    ->orderBy('merk')
                    ->get()
                    ->map(function($asset) {
                        return [
                            'id' => $asset->id,
                            'code' => $asset->nomor_polisi,
                            'name' => $asset->merk . ' ' . $asset->model,
                            'category' => $asset->jenis_kendaraan,
                            'value' => $asset->harga_pembelian,
                            'display' => $asset->nomor_polisi . ' - ' . $asset->merk . ' ' . $asset->model . ' (' . $asset->jenis_kendaraan . ')'
                        ];
                    });
            }

            return response()->json(['success' => true, 'data' => $assets]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data aset: ' . $e->getMessage()]);
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $query = DisposalAset::with(['project:id,nama', 'diajukanOleh:id,name', 'disetujuiOleh:id,name'])
                ->orderBy('created_at', 'desc');

            // Apply same filters as index
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('asset_type')) {
                $query->where('asset_type', $request->asset_type);
            }

            if ($request->filled('jenis_disposal')) {
                $query->where('jenis_disposal', $request->jenis_disposal);
            }

            $disposalAsets = $query->get();

            // Get selected project name
            $selectedProject = null;
            if ($request->filled('project_id')) {
                $selectedProject = Project::find($request->project_id);
            }

            // Statistics
            $stats = [
                'total' => $disposalAsets->count(),
                'pending' => $disposalAsets->where('status', 'pending')->count(),
                'approved' => $disposalAsets->where('status', 'approved')->count(),
                'completed' => $disposalAsets->where('status', 'completed')->count(),
                'total_nilai_disposal' => $disposalAsets->where('status', 'completed')->sum('nilai_disposal'),
            ];

            $pdf = Pdf::loadView('perusahaan.disposal-aset.laporan-pdf', compact(
                'disposalAsets',
                'selectedProject',
                'stats'
            ))->setPaper('a4', 'portrait');

            $filename = 'Laporan_Disposal_Aset_' . now()->format('Y-m-d') . '.pdf';
            if ($selectedProject) {
                $filename = 'Laporan_Disposal_Aset_' . str_replace(' ', '_', $selectedProject->nama) . '_' . now()->format('Y-m-d') . '.pdf';
            }

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }
}