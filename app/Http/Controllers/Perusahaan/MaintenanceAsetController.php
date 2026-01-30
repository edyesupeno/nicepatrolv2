<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceAset;
use App\Models\Project;
use App\Models\DataAset;
use App\Models\AsetKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class MaintenanceAsetController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = MaintenanceAset::with(['project', 'createdBy'])
                ->orderBy('tanggal_maintenance', 'desc');

            // Search
            if ($request->filled('search')) {
                $query->search($request->search);
            }

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

            // Filter by jenis maintenance
            if ($request->filled('jenis_maintenance')) {
                $query->where('jenis_maintenance', $request->jenis_maintenance);
            }

            // Filter by prioritas
            if ($request->filled('prioritas')) {
                $query->where('prioritas', $request->prioritas);
            }

            // Filter by date range
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_maintenance', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_maintenance', '<=', $request->tanggal_sampai);
            }

            $maintenances = $query->paginate(20);

            // Statistics for dashboard cards
            $stats = [
                'total' => MaintenanceAset::count(),
                'scheduled' => MaintenanceAset::scheduled()->count(),
                'in_progress' => MaintenanceAset::inProgress()->count(),
                'completed' => MaintenanceAset::completed()->count(),
                'overdue' => MaintenanceAset::overdue()->count(),
                'upcoming' => MaintenanceAset::upcoming(7)->count(),
            ];

            // Get projects for filter dropdown
            $projects = Project::select('id', 'nama')->orderBy('nama')->get();

            return view('perusahaan.maintenance-aset.index', compact('maintenances', 'stats', 'projects'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.maintenance-aset.create', compact('projects'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'asset_type' => 'required|in:data_aset,aset_kendaraan',
                'asset_id' => 'required|integer',
                'jenis_maintenance' => 'required|in:preventive,corrective,predictive',
                'tanggal_maintenance' => 'required|date|after_or_equal:today',
                'waktu_mulai' => 'nullable|date_format:H:i',
                'estimasi_durasi' => 'nullable|integer|min:1',
                'deskripsi_pekerjaan' => 'required|string|max:1000',
                'catatan_sebelum' => 'nullable|string|max:1000',
                'teknisi_internal' => 'nullable|string|max:255',
                'vendor_eksternal' => 'nullable|string|max:255',
                'kontak_vendor' => 'nullable|string|max:255',
                'biaya_sparepart' => 'nullable|numeric|min:0',
                'biaya_jasa' => 'nullable|numeric|min:0',
                'biaya_lainnya' => 'nullable|numeric|min:0',
                'prioritas' => 'required|in:low,medium,high,urgent',
                'reminder_hari' => 'nullable|integer|min:1|max:365',
                'interval_maintenance' => 'nullable|integer|min:1',
                'foto_sebelum' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'dokumen_pendukung' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);

            // Auto-assign perusahaan_id dan created_by
            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
            $validated['created_by'] = auth()->id();

            // Set default values
            $validated['biaya_sparepart'] = $validated['biaya_sparepart'] ?? 0;
            $validated['biaya_jasa'] = $validated['biaya_jasa'] ?? 0;
            $validated['biaya_lainnya'] = $validated['biaya_lainnya'] ?? 0;
            $validated['reminder_aktif'] = $request->has('reminder_aktif');
            $validated['reminder_hari'] = $validated['reminder_hari'] ?? 7;

            // Handle file uploads
            if ($request->hasFile('foto_sebelum')) {
                $validated['foto_sebelum'] = $request->file('foto_sebelum')->store('maintenance/photos', 'public');
            }

            if ($request->hasFile('dokumen_pendukung')) {
                $validated['dokumen_pendukung'] = $request->file('dokumen_pendukung')->store('maintenance/documents', 'public');
            }

            $maintenance = MaintenanceAset::create($validated);

            return redirect()->route('perusahaan.maintenance-aset.show', $maintenance->hash_id)
                ->with('success', 'Jadwal maintenance berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat jadwal maintenance: ' . $e->getMessage());
        }
    }

    public function show(MaintenanceAset $maintenanceAset)
    {
        $maintenanceAset->load(['project', 'createdBy']);
        
        // Load the specific asset based on asset_type
        if ($maintenanceAset->asset_type === 'data_aset') {
            $maintenanceAset->load('dataAset');
        } elseif ($maintenanceAset->asset_type === 'aset_kendaraan') {
            $maintenanceAset->load('asetKendaraan');
        }
        
        return view('perusahaan.maintenance-aset.show', compact('maintenanceAset'));
    }

    public function edit(MaintenanceAset $maintenanceAset)
    {
        if ($maintenanceAset->status === 'completed') {
            return redirect()->route('perusahaan.maintenance-aset.show', $maintenanceAset->hash_id)
                ->with('error', 'Maintenance yang sudah selesai tidak dapat diedit.');
        }

        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        
        return view('perusahaan.maintenance-aset.edit', compact('maintenanceAset', 'projects'));
    }

    public function update(Request $request, MaintenanceAset $maintenanceAset)
    {
        if ($maintenanceAset->status === 'completed') {
            return redirect()->route('perusahaan.maintenance-aset.show', $maintenanceAset->hash_id)
                ->with('error', 'Maintenance yang sudah selesai tidak dapat diedit.');
        }

        try {
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'asset_type' => 'required|in:data_aset,aset_kendaraan',
                'asset_id' => 'required|integer',
                'jenis_maintenance' => 'required|in:preventive,corrective,predictive',
                'tanggal_maintenance' => 'required|date',
                'waktu_mulai' => 'nullable|date_format:H:i',
                'estimasi_durasi' => 'nullable|integer|min:1',
                'deskripsi_pekerjaan' => 'required|string|max:1000',
                'catatan_sebelum' => 'nullable|string|max:1000',
                'teknisi_internal' => 'nullable|string|max:255',
                'vendor_eksternal' => 'nullable|string|max:255',
                'kontak_vendor' => 'nullable|string|max:255',
                'biaya_sparepart' => 'nullable|numeric|min:0',
                'biaya_jasa' => 'nullable|numeric|min:0',
                'biaya_lainnya' => 'nullable|numeric|min:0',
                'prioritas' => 'required|in:low,medium,high,urgent',
                'reminder_hari' => 'nullable|integer|min:1|max:365',
                'interval_maintenance' => 'nullable|integer|min:1',
                'foto_sebelum' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'dokumen_pendukung' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);

            // Set default values
            $validated['biaya_sparepart'] = $validated['biaya_sparepart'] ?? 0;
            $validated['biaya_jasa'] = $validated['biaya_jasa'] ?? 0;
            $validated['biaya_lainnya'] = $validated['biaya_lainnya'] ?? 0;
            $validated['reminder_aktif'] = $request->has('reminder_aktif');
            $validated['reminder_hari'] = $validated['reminder_hari'] ?? 7;

            // Handle file uploads
            if ($request->hasFile('foto_sebelum')) {
                // Delete old file
                if ($maintenanceAset->foto_sebelum) {
                    Storage::disk('public')->delete($maintenanceAset->foto_sebelum);
                }
                $validated['foto_sebelum'] = $request->file('foto_sebelum')->store('maintenance/photos', 'public');
            }

            if ($request->hasFile('dokumen_pendukung')) {
                // Delete old file
                if ($maintenanceAset->dokumen_pendukung) {
                    Storage::disk('public')->delete($maintenanceAset->dokumen_pendukung);
                }
                $validated['dokumen_pendukung'] = $request->file('dokumen_pendukung')->store('maintenance/documents', 'public');
            }

            $maintenanceAset->update($validated);

            return redirect()->route('perusahaan.maintenance-aset.show', $maintenanceAset->hash_id)
                ->with('success', 'Jadwal maintenance berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui jadwal maintenance: ' . $e->getMessage());
        }
    }

    public function destroy(MaintenanceAset $maintenanceAset)
    {
        if ($maintenanceAset->status === 'completed') {
            return redirect()->back()->with('error', 'Maintenance yang sudah selesai tidak dapat dihapus.');
        }

        try {
            // Delete associated files
            if ($maintenanceAset->foto_sebelum) {
                Storage::disk('public')->delete($maintenanceAset->foto_sebelum);
            }
            if ($maintenanceAset->foto_sesudah) {
                Storage::disk('public')->delete($maintenanceAset->foto_sesudah);
            }
            if ($maintenanceAset->dokumen_pendukung) {
                Storage::disk('public')->delete($maintenanceAset->dokumen_pendukung);
            }
            if ($maintenanceAset->invoice_pembayaran) {
                Storage::disk('public')->delete($maintenanceAset->invoice_pembayaran);
            }

            $maintenanceAset->delete();

            return redirect()->route('perusahaan.maintenance-aset.index')
                ->with('success', 'Jadwal maintenance berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus jadwal maintenance: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, MaintenanceAset $maintenanceAset)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:scheduled,in_progress,completed,cancelled',
                'catatan_sesudah' => 'nullable|string|max:1000',
                'hasil_maintenance' => 'nullable|in:berhasil,sebagian,gagal',
                'masalah_ditemukan' => 'nullable|string|max:1000',
                'tindakan_dilakukan' => 'nullable|string|max:1000',
                'rekomendasi' => 'nullable|string|max:1000',
                'foto_sesudah' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'invoice_pembayaran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                // Biaya real saat maintenance selesai
                'biaya_sparepart_real' => 'nullable|numeric|min:0',
                'biaya_jasa_real' => 'nullable|numeric|min:0',
                'biaya_lainnya_real' => 'nullable|numeric|min:0',
            ]);

            // Handle file uploads
            if ($request->hasFile('foto_sesudah')) {
                if ($maintenanceAset->foto_sesudah) {
                    Storage::disk('public')->delete($maintenanceAset->foto_sesudah);
                }
                $validated['foto_sesudah'] = $request->file('foto_sesudah')->store('maintenance/photos', 'public');
            }

            if ($request->hasFile('invoice_pembayaran')) {
                if ($maintenanceAset->invoice_pembayaran) {
                    Storage::disk('public')->delete($maintenanceAset->invoice_pembayaran);
                }
                $validated['invoice_pembayaran'] = $request->file('invoice_pembayaran')->store('maintenance/invoices', 'public');
            }

            // Set waktu selesai if completed
            if ($validated['status'] === 'completed') {
                $validated['waktu_selesai'] = now()->format('H:i');
                
                // Update biaya real jika ada input biaya saat selesai
                if ($request->filled('biaya_sparepart_real')) {
                    $validated['biaya_sparepart'] = $validated['biaya_sparepart_real'];
                }
                if ($request->filled('biaya_jasa_real')) {
                    $validated['biaya_jasa'] = $validated['biaya_jasa_real'];
                }
                if ($request->filled('biaya_lainnya_real')) {
                    $validated['biaya_lainnya'] = $validated['biaya_lainnya_real'];
                }
                
                // Recalculate total biaya dengan biaya real
                $validated['total_biaya'] = ($validated['biaya_sparepart'] ?? $maintenanceAset->biaya_sparepart) + 
                                          ($validated['biaya_jasa'] ?? $maintenanceAset->biaya_jasa) + 
                                          ($validated['biaya_lainnya'] ?? $maintenanceAset->biaya_lainnya);
            }

            // Remove the _real fields from validated array as they're not in the database
            unset($validated['biaya_sparepart_real'], $validated['biaya_jasa_real'], $validated['biaya_lainnya_real']);

            $maintenanceAset->update($validated);

            // Generate next maintenance if completed and has interval
            if ($validated['status'] === 'completed' && $maintenanceAset->interval_maintenance) {
                $maintenanceAset->generateNextMaintenance();
            }

            return response()->json([
                'success' => true,
                'message' => 'Status maintenance berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAssetsByProject(Request $request)
    {
        try {
            $projectId = $request->project_id;
            $assetType = $request->asset_type;
            
            if (!$projectId || !$assetType) {
                return response()->json(['success' => false, 'message' => 'Parameter tidak lengkap']);
            }

            $assets = [];
            
            if ($assetType === 'data_aset') {
                $assets = DataAset::where('project_id', $projectId)
                    ->select('id', 'nama_aset', 'kode_aset')
                    ->get()
                    ->map(function ($asset) {
                        return [
                            'id' => $asset->id,
                            'text' => $asset->nama_aset . ' (' . $asset->kode_aset . ')'
                        ];
                    });
            } elseif ($assetType === 'aset_kendaraan') {
                $assets = AsetKendaraan::where('project_id', $projectId)
                    ->select('id', 'merk', 'model', 'nomor_polisi')
                    ->get()
                    ->map(function ($asset) {
                        return [
                            'id' => $asset->id,
                            'text' => $asset->merk . ' ' . $asset->model . ' (' . $asset->nomor_polisi . ')'
                        ];
                    });
            }

            return response()->json([
                'success' => true,
                'data' => $assets
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading assets', [
                'project_id' => $request->project_id,
                'asset_type' => $request->asset_type,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dashboard(Request $request)
    {
        try {
            // Base query for filtering
            $baseQuery = MaintenanceAset::query();
            
            // Filter by project if specified
            if ($request->filled('project_id')) {
                $baseQuery->where('project_id', $request->project_id);
            }

            // Statistics
            $stats = [
                'total' => (clone $baseQuery)->count(),
                'scheduled' => (clone $baseQuery)->scheduled()->count(),
                'in_progress' => (clone $baseQuery)->inProgress()->count(),
                'completed' => (clone $baseQuery)->completed()->count(),
                'overdue' => (clone $baseQuery)->overdue()->count(),
                'upcoming' => (clone $baseQuery)->upcoming(7)->count(),
            ];

            // Upcoming maintenances (next 7 days)
            $upcomingQuery = (clone $baseQuery)->upcoming(7)->with(['project']);
            $upcomingMaintenances = $upcomingQuery->orderBy('tanggal_maintenance')->limit(10)->get();

            // Overdue maintenances
            $overdueQuery = (clone $baseQuery)->overdue()->with(['project']);
            $overdueMaintenances = $overdueQuery->orderBy('tanggal_maintenance')->limit(10)->get();

            // Monthly maintenance cost
            $monthlyCostQuery = (clone $baseQuery)->completed()
                ->selectRaw('MONTH(tanggal_maintenance) as month, YEAR(tanggal_maintenance) as year, SUM(total_biaya) as total')
                ->whereYear('tanggal_maintenance', now()->year)
                ->groupBy('year', 'month')
                ->orderBy('month');
            $monthlyCosts = $monthlyCostQuery->get();

            // Get projects for filter dropdown
            $projects = Project::select('id', 'nama')->orderBy('nama')->get();

            return view('perusahaan.maintenance-aset.dashboard', compact(
                'stats', 
                'upcomingMaintenances', 
                'overdueMaintenances', 
                'monthlyCosts',
                'projects'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function laporan(Request $request)
    {
        try {
            $query = MaintenanceAset::with(['project', 'createdBy']);

            // Filter by project
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            // Filter by date range
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal_maintenance', '>=', $request->tanggal_dari);
            }
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal_maintenance', '<=', $request->tanggal_sampai);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by jenis maintenance
            if ($request->filled('jenis_maintenance')) {
                $query->where('jenis_maintenance', $request->jenis_maintenance);
            }

            $maintenances = $query->orderBy('tanggal_maintenance', 'desc')->get();

            if ($request->has('export') && $request->export === 'pdf') {
                $pdf = Pdf::loadView('perusahaan.maintenance-aset.laporan-pdf', compact('maintenances'))
                    ->setPaper('a4', 'landscape');

                return $pdf->download('Laporan_Maintenance_Aset_' . now()->format('Y-m-d') . '.pdf');
            }

            return view('perusahaan.maintenance-aset.laporan', compact('maintenances'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}