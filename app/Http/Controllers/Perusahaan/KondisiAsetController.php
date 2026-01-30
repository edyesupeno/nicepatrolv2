<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\DataAset;
use App\Models\AsetKendaraan;
use App\Models\Project;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KondisiAsetController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Debug: Check if we can access basic data
            \Log::info('Kondisi Aset Controller accessed');
            
            // Get projects for filter dropdown
            $projects = Project::select('id', 'nama')->orderBy('nama')->get();
            \Log::info('Projects count: ' . $projects->count());

            // Base queries
            $dataAsetQuery = DataAset::query();
            $asetKendaraanQuery = AsetKendaraan::query();

            // Filter by project if specified
            if ($request->filled('project_id')) {
                $dataAsetQuery->where('project_id', $request->project_id);
                $asetKendaraanQuery->where('project_id', $request->project_id);
            }

            // Data Aset Statistics
            $dataAsetStats = [
                'total' => (clone $dataAsetQuery)->count(),
                'ada' => (clone $dataAsetQuery)->where('status', 'ada')->count(),
                'rusak' => (clone $dataAsetQuery)->where('status', 'rusak')->count(),
                'dijual' => (clone $dataAsetQuery)->where('status', 'dijual')->count(),
                'dihapus' => (clone $dataAsetQuery)->where('status', 'dihapus')->count(),
            ];

            // Aset Kendaraan Statistics
            $asetKendaraanStats = [
                'total' => (clone $asetKendaraanQuery)->count(),
                'aktif' => (clone $asetKendaraanQuery)->where('status_kendaraan', 'aktif')->count(),
                'maintenance' => (clone $asetKendaraanQuery)->where('status_kendaraan', 'maintenance')->count(),
                'rusak' => (clone $asetKendaraanQuery)->where('status_kendaraan', 'rusak')->count(),
                'dijual' => (clone $asetKendaraanQuery)->where('status_kendaraan', 'dijual')->count(),
                'hilang' => (clone $asetKendaraanQuery)->where('status_kendaraan', 'hilang')->count(),
            ];

            \Log::info('Data Aset Stats: ', $dataAsetStats);
            \Log::info('Aset Kendaraan Stats: ', $asetKendaraanStats);

            // Data Aset by Category
            $dataAsetByCategory = (clone $dataAsetQuery)
                ->selectRaw('kategori, COUNT(*) as total')
                ->groupBy('kategori')
                ->orderBy('total', 'desc')
                ->get();

            // Aset Kendaraan by Type
            $asetKendaraanByType = (clone $asetKendaraanQuery)
                ->selectRaw('jenis_kendaraan, COUNT(*) as total')
                ->groupBy('jenis_kendaraan')
                ->orderBy('total', 'desc')
                ->get();

            // Data Aset by Project
            $dataAsetByProject = (clone $dataAsetQuery)
                ->with('project:id,nama')
                ->selectRaw('project_id, COUNT(*) as total')
                ->groupBy('project_id')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();

            // Aset Kendaraan by Project
            $asetKendaraanByProject = (clone $asetKendaraanQuery)
                ->with('project:id,nama')
                ->selectRaw('project_id, COUNT(*) as total')
                ->groupBy('project_id')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();

            // Asset Value Statistics
            $dataAsetValue = [
                'total_value' => (clone $dataAsetQuery)->sum('harga_beli') ?? 0,
                'avg_value' => (clone $dataAsetQuery)->avg('harga_beli') ?? 0,
                'max_value' => (clone $dataAsetQuery)->max('harga_beli') ?? 0,
                'min_value' => (clone $dataAsetQuery)->min('harga_beli') ?? 0,
            ];

            $asetKendaraanValue = [
                'total_value' => (clone $asetKendaraanQuery)->sum('harga_pembelian') ?? 0,
                'avg_value' => (clone $asetKendaraanQuery)->avg('harga_pembelian') ?? 0,
                'max_value' => (clone $asetKendaraanQuery)->max('harga_pembelian') ?? 0,
                'min_value' => (clone $asetKendaraanQuery)->min('harga_pembelian') ?? 0,
            ];

            // Recent Assets (last 30 days)
            $recentDataAset = (clone $dataAsetQuery)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            $recentAsetKendaraan = (clone $asetKendaraanQuery)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();

            \Log::info('About to return view');

            return view('perusahaan.kondisi-aset.index', compact(
                'projects',
                'dataAsetStats',
                'asetKendaraanStats',
                'dataAsetByCategory',
                'asetKendaraanByType',
                'dataAsetByProject',
                'asetKendaraanByProject',
                'dataAsetValue',
                'asetKendaraanValue',
                'recentDataAset',
                'recentAsetKendaraan'
            ));
        } catch (\Exception $e) {
            \Log::error('Kondisi Aset Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            // Get projects for filter dropdown
            $projects = Project::select('id', 'nama')->orderBy('nama')->get();

            // Base queries
            $dataAsetQuery = DataAset::query();
            $asetKendaraanQuery = AsetKendaraan::query();

            // Filter by project if specified
            if ($request->filled('project_id')) {
                $dataAsetQuery->where('project_id', $request->project_id);
                $asetKendaraanQuery->where('project_id', $request->project_id);
            }

            // Get selected project name
            $selectedProject = null;
            if ($request->filled('project_id')) {
                $selectedProject = Project::find($request->project_id);
            }

            // Data Aset Statistics
            $dataAsetStats = [
                'total' => (clone $dataAsetQuery)->count(),
                'ada' => (clone $dataAsetQuery)->where('status', 'ada')->count(),
                'rusak' => (clone $dataAsetQuery)->where('status', 'rusak')->count(),
                'dijual' => (clone $dataAsetQuery)->where('status', 'dijual')->count(),
                'dihapus' => (clone $dataAsetQuery)->where('status', 'dihapus')->count(),
            ];

            // Aset Kendaraan Statistics
            $asetKendaraanStats = [
                'total' => (clone $asetKendaraanQuery)->count(),
                'aktif' => (clone $asetKendaraanQuery)->where('status_kendaraan', 'aktif')->count(),
                'maintenance' => (clone $asetKendaraanQuery)->where('status_kendaraan', 'maintenance')->count(),
                'rusak' => (clone $asetKendaraanQuery)->where('status_kendaraan', 'rusak')->count(),
                'dijual' => (clone $asetKendaraanQuery)->where('status_kendaraan', 'dijual')->count(),
                'hilang' => (clone $asetKendaraanQuery)->where('status_kendaraan', 'hilang')->count(),
            ];

            // Data Aset by Category
            $dataAsetByCategory = (clone $dataAsetQuery)
                ->selectRaw('kategori, COUNT(*) as total')
                ->groupBy('kategori')
                ->orderBy('total', 'desc')
                ->get();

            // Aset Kendaraan by Type
            $asetKendaraanByType = (clone $asetKendaraanQuery)
                ->selectRaw('jenis_kendaraan, COUNT(*) as total')
                ->groupBy('jenis_kendaraan')
                ->orderBy('total', 'desc')
                ->get();

            // Asset Value Statistics
            $dataAsetValue = [
                'total_value' => (clone $dataAsetQuery)->sum('harga_beli') ?? 0,
                'avg_value' => (clone $dataAsetQuery)->avg('harga_beli') ?? 0,
            ];

            $asetKendaraanValue = [
                'total_value' => (clone $asetKendaraanQuery)->sum('harga_pembelian') ?? 0,
                'avg_value' => (clone $asetKendaraanQuery)->avg('harga_pembelian') ?? 0,
            ];

            $pdf = Pdf::loadView('perusahaan.kondisi-aset.pdf', compact(
                'selectedProject',
                'dataAsetStats',
                'asetKendaraanStats',
                'dataAsetByCategory',
                'asetKendaraanByType',
                'dataAsetValue',
                'asetKendaraanValue'
            ))->setPaper('a4', 'portrait');

            $filename = 'Laporan_Kondisi_Aset_' . now()->format('Y-m-d') . '.pdf';
            if ($selectedProject) {
                $filename = 'Laporan_Kondisi_Aset_' . str_replace(' ', '_', $selectedProject->nama) . '_' . now()->format('Y-m-d') . '.pdf';
            }

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }
}