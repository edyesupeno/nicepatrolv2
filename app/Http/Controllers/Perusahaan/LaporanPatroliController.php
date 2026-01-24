<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\AreaPatrol;
use App\Models\Patroli;
use App\Models\PatroliDetail;
use App\Models\Checkpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPatroliController extends Controller
{
    public function insiden(Request $request)
    {
        try {
            // Check if patroli_mandiri table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('patroli_mandiri')) {
                return view('perusahaan.laporan-patroli.insiden-empty')->with('info', 'Fitur Laporan Insiden belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.');
            }

            $query = \App\Models\PatroliMandiri::with([
                'project:id,nama',
                'areaPatrol:id,nama',
                'petugas:id,name',
                'reviewer:id,name'
            ]);

            // Filter hanya yang tidak aman (insiden)
            $query->where('status_lokasi', 'tidak_aman');

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_lokasi', 'ILIKE', "%{$search}%")
                      ->orWhere('jenis_kendala', 'ILIKE', "%{$search}%")
                      ->orWhere('deskripsi_kendala', 'ILIKE', "%{$search}%")
                      ->orWhereHas('petugas', function($sq) use ($search) {
                          $sq->where('name', 'ILIKE', "%{$search}%");
                      });
                });
            }

            // Filter by project
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            // Filter by area
            if ($request->filled('area_id')) {
                $query->where('area_patrol_id', $request->area_id);
            }

            // Filter by jenis kendala
            if ($request->filled('jenis_kendala')) {
                $query->where('jenis_kendala', $request->jenis_kendala);
            }

            // Filter by prioritas
            if ($request->filled('prioritas')) {
                $query->where('prioritas', $request->prioritas);
            }

            // Filter by status laporan
            if ($request->filled('status_laporan')) {
                $query->where('status_laporan', $request->status_laporan);
            }

            // Filter by date range
            if ($request->filled('tanggal_mulai')) {
                $query->whereDate('waktu_laporan', '>=', $request->tanggal_mulai);
            }
            if ($request->filled('tanggal_selesai')) {
                $query->whereDate('waktu_laporan', '<=', $request->tanggal_selesai);
            }

            $insiden = $query->orderBy('waktu_laporan', 'desc')->paginate(15)->withQueryString();

            // Get filter options
            $projects = \App\Models\Project::select('id', 'nama')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            $areas = \App\Models\AreaPatrol::select('id', 'nama', 'project_id')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            // Get unique jenis kendala for filter
            $jenisKendala = \App\Models\PatroliMandiri::select('jenis_kendala')
                ->where('status_lokasi', 'tidak_aman')
                ->whereNotNull('jenis_kendala')
                ->distinct()
                ->pluck('jenis_kendala')
                ->map(function($item) {
                    return [
                        'value' => $item,
                        'label' => ucwords(str_replace('_', ' ', $item))
                    ];
                })
                ->sortBy('label');

            // Statistics
            $stats = $this->getInsidenStats($request);

            return view('perusahaan.laporan-patroli.insiden', compact(
                'insiden', 
                'projects', 
                'areas', 
                'jenisKendala', 
                'stats'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in LaporanPatroliController@insiden: ' . $e->getMessage());
            
            return view('perusahaan.laporan-patroli.insiden-empty')
                ->with('error', 'Terjadi kesalahan saat memuat data laporan insiden. Silakan coba lagi atau hubungi administrator.');
        }
    }

    private function getInsidenStats($request)
    {
        $query = \App\Models\PatroliMandiri::where('status_lokasi', 'tidak_aman');

        // Apply same filters as main query
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('area_id')) {
            $query->where('area_patrol_id', $request->area_id);
        }
        if ($request->filled('jenis_kendala')) {
            $query->where('jenis_kendala', $request->jenis_kendala);
        }
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('waktu_laporan', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('waktu_laporan', '<=', $request->tanggal_selesai);
        }

        $totalInsiden = $query->count();
        $insidenKritis = $query->where('prioritas', 'kritis')->count();
        $insidenTinggi = $query->where('prioritas', 'tinggi')->count();
        $insidenSedang = $query->where('prioritas', 'sedang')->count();
        $insidenRendah = $query->where('prioritas', 'rendah')->count();

        $insidenSubmitted = $query->where('status_laporan', 'submitted')->count();
        $insidenReviewed = $query->where('status_laporan', 'reviewed')->count();
        $insidenResolved = $query->where('status_laporan', 'resolved')->count();

        // Insiden hari ini
        $insidenHariIni = \App\Models\PatroliMandiri::where('status_lokasi', 'tidak_aman')
            ->whereDate('waktu_laporan', today())
            ->count();

        // Insiden minggu ini
        $insidenMingguIni = \App\Models\PatroliMandiri::where('status_lokasi', 'tidak_aman')
            ->whereBetween('waktu_laporan', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return [
            'total_insiden' => $totalInsiden,
            'insiden_kritis' => $insidenKritis,
            'insiden_tinggi' => $insidenTinggi,
            'insiden_sedang' => $insidenSedang,
            'insiden_rendah' => $insidenRendah,
            'insiden_submitted' => $insidenSubmitted,
            'insiden_reviewed' => $insidenReviewed,
            'insiden_resolved' => $insidenResolved,
            'insiden_hari_ini' => $insidenHariIni,
            'insiden_minggu_ini' => $insidenMingguIni,
            'resolution_rate' => $totalInsiden > 0 ? round(($insidenResolved / $totalInsiden) * 100, 1) : 0,
        ];
    }

    public function insidenShow(\App\Models\PatroliMandiri $patroliMandiri)
    {
        try {
            // Check if patroli_mandiri table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('patroli_mandiri')) {
                return redirect()->route('perusahaan.laporan-patroli.insiden')
                    ->with('error', 'Fitur Laporan Insiden belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.');
            }

            // Only show incidents (tidak_aman)
            if ($patroliMandiri->status_lokasi !== 'tidak_aman') {
                return redirect()->route('perusahaan.laporan-patroli.insiden')
                    ->with('error', 'Data yang diminta bukan merupakan laporan insiden.');
            }

            $patroliMandiri->load([
                'project:id,nama',
                'areaPatrol:id,nama',
                'petugas:id,name,email',
                'reviewer:id,name,email'
            ]);

            return view('perusahaan.laporan-patroli.insiden-show', compact('patroliMandiri'));

        } catch (\Exception $e) {
            \Log::error('Error in LaporanPatroliController@insidenShow: ' . $e->getMessage());
            
            return redirect()->route('perusahaan.laporan-patroli.insiden')
                ->with('error', 'Terjadi kesalahan saat memuat detail insiden. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function insidenExportPdf(\App\Models\PatroliMandiri $patroliMandiri)
    {
        try {
            // Check if patroli_mandiri table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('patroli_mandiri')) {
                return redirect()->route('perusahaan.laporan-patroli.insiden')
                    ->with('error', 'Fitur Laporan Insiden belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.');
            }

            // Only export incidents (tidak_aman)
            if ($patroliMandiri->status_lokasi !== 'tidak_aman') {
                return redirect()->route('perusahaan.laporan-patroli.insiden')
                    ->with('error', 'Data yang diminta bukan merupakan laporan insiden.');
            }

            $patroliMandiri->load([
                'project:id,nama',
                'areaPatrol:id,nama',
                'petugas:id,name,email',
                'reviewer:id,name,email'
            ]);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('perusahaan.laporan-patroli.insiden-pdf', compact('patroliMandiri'));

            $filename = 'Laporan_Insiden_' . $patroliMandiri->hash_id . '_' . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error in LaporanPatroliController@insidenExportPdf: ' . $e->getMessage());
            
            return redirect()->route('perusahaan.laporan-patroli.insiden')
                ->with('error', 'Terjadi kesalahan saat mengekspor PDF. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function insidenExportMultiplePdf(Request $request)
    {
        try {
            // Check if patroli_mandiri table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('patroli_mandiri')) {
                return redirect()->route('perusahaan.laporan-patroli.insiden')
                    ->with('error', 'Fitur Laporan Insiden belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.');
            }

            $validated = $request->validate([
                'insiden_ids' => 'required|array',
                'insiden_ids.*' => 'exists:patroli_mandiri,id'
            ]);

            $insidenList = \App\Models\PatroliMandiri::whereIn('id', $validated['insiden_ids'])
                ->where('status_lokasi', 'tidak_aman') // Only incidents
                ->with([
                    'project:id,nama',
                    'areaPatrol:id,nama',
                    'petugas:id,name,email',
                    'reviewer:id,name,email'
                ])
                ->orderBy('waktu_laporan', 'desc')
                ->get();

            if ($insidenList->isEmpty()) {
                return redirect()->route('perusahaan.laporan-patroli.insiden')
                    ->with('error', 'Tidak ada data insiden yang valid untuk diekspor.');
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('perusahaan.laporan-patroli.insiden-multiple-pdf', compact('insidenList'));
            
            $filename = 'Laporan_Insiden_Multiple_' . \Carbon\Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error in LaporanPatroliController@insidenExportMultiplePdf: ' . $e->getMessage());
            
            return redirect()->route('perusahaan.laporan-patroli.insiden')
                ->with('error', 'Terjadi kesalahan saat mengekspor PDF multiple. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function kawasan(Request $request)
    {
        $query = AreaPatrol::select([
                'area_patrols.id',
                'area_patrols.nama',
                'area_patrols.deskripsi', 
                'area_patrols.alamat',
                'area_patrols.project_id',
                'area_patrols.is_active'
            ])
            ->with([
                'project:id,nama',
                'rutePatrols:id,area_patrol_id'
            ])
            ->withCount(['rutePatrols as total_rute']);

        // Filter berdasarkan tanggal - DEFAULT HARI INI
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Filter berdasarkan project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $areas = $query->paginate(20);

        // Ambil statistik patroli untuk setiap area
        foreach ($areas as $area) {
            $area->patroli_stats = $this->getPatroliStatsForArea($area->id, $startDate, $endDate);
        }

        // Data untuk filter
        $projects = \App\Models\Project::select('id', 'nama')->get();

        // Statistik keseluruhan
        $totalStats = $this->getTotalStats($startDate, $endDate);

        return view('perusahaan.laporan-patroli.kawasan', compact(
            'areas',
            'projects',
            'startDate',
            'endDate',
            'totalStats'
        ));
    }

    private function getPatroliStatsForArea($areaId, $startDate, $endDate)
    {
        // Ambil semua patroli di area ini dalam periode tertentu - OPTIMIZED SELECT
        $patrolis = Patroli::select([
                'patrolis.id',
                'patrolis.status',
                'patrolis.waktu_mulai',
                'patrolis.waktu_selesai',
                'patrolis.user_id'
            ])
            ->whereHas('details', function ($detailQuery) use ($areaId) {
                $detailQuery->whereHas('checkpoint', function ($checkpointQuery) use ($areaId) {
                    $checkpointQuery->whereHas('rutePatrol', function ($ruteQuery) use ($areaId) {
                        $ruteQuery->whereHas('areaPatrol', function ($areaQuery) use ($areaId) {
                            $areaQuery->where('area_patrols.id', $areaId);
                        });
                    });
                });
            })
            ->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with([
                'details:id,patroli_id,checkpoint_id',
                'details.checkpoint:id,nama,rute_patrol_id',
                'details.checkpoint.rutePatrol:id,nama,area_patrol_id',
                'user:id,name'
            ])
            ->get();

        $totalPatroli = $patrolis->count();
        $patroliSelesai = $patrolis->where('status', 'selesai')->count();
        $patroliBerjalan = $patrolis->where('status', 'berjalan')->count();
        $patroliTertunda = $patrolis->where('status', 'tertunda')->count();

        // Hitung total checkpoint yang harus dikunjungi vs yang sudah dikunjungi - OPTIMIZED
        $totalCheckpoints = Checkpoint::whereHas('rutePatrol', function ($ruteQuery) use ($areaId) {
            $ruteQuery->whereHas('areaPatrol', function ($areaQuery) use ($areaId) {
                $areaQuery->where('area_patrols.id', $areaId);
            });
        })->count();

        $checkpointsVisited = PatroliDetail::whereHas('patroli', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->whereHas('checkpoint', function ($checkpointQuery) use ($areaId) {
                $checkpointQuery->whereHas('rutePatrol', function ($ruteQuery) use ($areaId) {
                    $ruteQuery->whereHas('areaPatrol', function ($areaQuery) use ($areaId) {
                        $areaQuery->where('area_patrols.id', $areaId);
                    });
                });
            })
            ->distinct('checkpoint_id')
            ->count();

        // Coverage percentage
        $coveragePercentage = $totalCheckpoints > 0 ? round(($checkpointsVisited / $totalCheckpoints) * 100, 1) : 0;

        // Rata-rata durasi patroli
        $avgDuration = $patrolis->filter(function ($patroli) {
            return $patroli->waktu_selesai && $patroli->waktu_mulai;
        })->avg(function ($patroli) {
            return $patroli->waktu_selesai->diffInMinutes($patroli->waktu_mulai);
        });

        // Statistik aset checks untuk area ini - OPTIMIZED COUNT QUERIES
        $asetAman = \App\Models\AsetCheck::whereHas('patroliDetail', function ($detailQuery) use ($areaId, $startDate, $endDate) {
            $detailQuery->whereHas('checkpoint', function ($checkpointQuery) use ($areaId) {
                $checkpointQuery->whereHas('rutePatrol', function ($ruteQuery) use ($areaId) {
                    $ruteQuery->whereHas('areaPatrol', function ($areaQuery) use ($areaId) {
                        $areaQuery->where('area_patrols.id', $areaId);
                    });
                });
            })->whereHas('patroli', function ($patroliQuery) use ($startDate, $endDate) {
                $patroliQuery->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            });
        })->where('status', 'aman')->count();

        $asetBermasalah = \App\Models\AsetCheck::whereHas('patroliDetail', function ($detailQuery) use ($areaId, $startDate, $endDate) {
            $detailQuery->whereHas('checkpoint', function ($checkpointQuery) use ($areaId) {
                $checkpointQuery->whereHas('rutePatrol', function ($ruteQuery) use ($areaId) {
                    $ruteQuery->whereHas('areaPatrol', function ($areaQuery) use ($areaId) {
                        $areaQuery->where('area_patrols.id', $areaId);
                    });
                });
            })->whereHas('patroli', function ($patroliQuery) use ($startDate, $endDate) {
                $patroliQuery->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            });
        })->where('status', 'bermasalah')->count();

        $asetHilang = \App\Models\AsetCheck::whereHas('patroliDetail', function ($detailQuery) use ($areaId, $startDate, $endDate) {
            $detailQuery->whereHas('checkpoint', function ($checkpointQuery) use ($areaId) {
                $checkpointQuery->whereHas('rutePatrol', function ($ruteQuery) use ($areaId) {
                    $ruteQuery->whereHas('areaPatrol', function ($areaQuery) use ($areaId) {
                        $areaQuery->where('area_patrols.id', $areaId);
                    });
                });
            })->whereHas('patroli', function ($patroliQuery) use ($startDate, $endDate) {
                $patroliQuery->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            });
        })->where('status', 'hilang')->count();

        return [
            'total_patroli' => $totalPatroli,
            'patroli_selesai' => $patroliSelesai,
            'patroli_berjalan' => $patroliBerjalan,
            'patroli_tertunda' => $patroliTertunda,
            'total_checkpoints' => $totalCheckpoints,
            'checkpoints_visited' => $checkpointsVisited,
            'coverage_percentage' => $coveragePercentage,
            'avg_duration' => $avgDuration ? round($avgDuration, 1) : 0,
            'completion_rate' => $totalPatroli > 0 ? round(($patroliSelesai / $totalPatroli) * 100, 1) : 0,
            'aset_aman' => $asetAman,
            'aset_bermasalah' => $asetBermasalah,
            'aset_hilang' => $asetHilang
        ];
    }

    private function getTotalStats($startDate, $endDate)
    {
        $totalPatroli = Patroli::whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
        $patroliSelesai = Patroli::whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'selesai')->count();
        
        $totalAreas = AreaPatrol::where('is_active', true)->count();
        
        // Perbaiki query untuk areas with patrol
        $areasWithPatrol = AreaPatrol::whereHas('rutePatrols', function ($ruteQuery) use ($startDate, $endDate) {
            $ruteQuery->whereHas('checkpoints', function ($checkpointQuery) use ($startDate, $endDate) {
                $checkpointQuery->whereHas('patroliDetails', function ($detailQuery) use ($startDate, $endDate) {
                    $detailQuery->whereHas('patroli', function ($patroliQuery) use ($startDate, $endDate) {
                        $patroliQuery->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    });
                });
            });
        })->count();

        // Statistik aset checks
        $totalAsetChecks = \App\Models\AsetCheck::whereHas('patroliDetail.patroli', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })->count();

        $totalAsetAman = \App\Models\AsetCheck::whereHas('patroliDetail.patroli', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })->where('status', 'aman')->count();

        $totalAsetBermasalah = \App\Models\AsetCheck::whereHas('patroliDetail.patroli', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })->where('status', 'bermasalah')->count();

        $totalAsetHilang = \App\Models\AsetCheck::whereHas('patroliDetail.patroli', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })->where('status', 'hilang')->count();

        return [
            'total_patroli' => $totalPatroli,
            'patroli_selesai' => $patroliSelesai,
            'completion_rate' => $totalPatroli > 0 ? round(($patroliSelesai / $totalPatroli) * 100, 1) : 0,
            'total_areas' => $totalAreas,
            'areas_with_patrol' => $areasWithPatrol,
            'area_coverage' => $totalAreas > 0 ? round(($areasWithPatrol / $totalAreas) * 100, 1) : 0,
            'total_aset_checks' => $totalAsetChecks,
            'total_aset_aman' => $totalAsetAman,
            'total_aset_bermasalah' => $totalAsetBermasalah,
            'total_aset_hilang' => $totalAsetHilang,
            'aset_aman_percentage' => $totalAsetChecks > 0 ? round(($totalAsetAman / $totalAsetChecks) * 100, 1) : 0
        ];
    }

    public function asetBermasalah(Request $request)
    {
        // Filter berdasarkan tanggal - DEFAULT HARI INI
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Query aset bermasalah dengan optimasi
        $query = \App\Models\AsetCheck::select([
                'aset_checks.id',
                'aset_checks.patroli_detail_id',
                'aset_checks.aset_kawasan_id',
                'aset_checks.status',
                'aset_checks.catatan',
                'aset_checks.foto',
                'aset_checks.created_at'
            ])
            ->whereIn('status', ['bermasalah', 'hilang'])
            ->whereHas('patroliDetail.patroli', function ($patroliQuery) use ($startDate, $endDate) {
                $patroliQuery->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->with([
                'asetKawasan:id,nama,kode_aset,kategori,merk,model',
                'patroliDetail:id,patroli_id,checkpoint_id,waktu_scan',
                'patroliDetail.patroli:id,user_id,waktu_mulai',
                'patroliDetail.patroli.user:id,name',
                'patroliDetail.checkpoint:id,nama,rute_patrol_id',
                'patroliDetail.checkpoint.rutePatrol:id,nama,area_patrol_id',
                'patroliDetail.checkpoint.rutePatrol.areaPatrol:id,nama,project_id',
                'patroliDetail.checkpoint.rutePatrol.areaPatrol.project:id,nama'
            ]);

        // Filter berdasarkan status
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        // Filter berdasarkan project
        if ($request->filled('project_id')) {
            $query->whereHas('patroliDetail.checkpoint.rutePatrol.areaPatrol', function ($areaQuery) use ($request) {
                $areaQuery->where('project_id', $request->project_id);
            });
        }

        // Filter berdasarkan area
        if ($request->filled('area_id')) {
            $query->whereHas('patroliDetail.checkpoint.rutePatrol', function ($ruteQuery) use ($request) {
                $ruteQuery->where('area_patrol_id', $request->area_id);
            });
        }

        $asetBermasalah = $query->orderBy('created_at', 'desc')->paginate(20);

        // Data untuk filter
        $projects = \App\Models\Project::select('id', 'nama')->get();
        $areas = \App\Models\AreaPatrol::select('id', 'nama', 'project_id')->get();

        // Statistik
        $totalBermasalah = \App\Models\AsetCheck::whereIn('status', ['bermasalah', 'hilang'])
            ->whereHas('patroliDetail.patroli', function ($patroliQuery) use ($startDate, $endDate) {
                $patroliQuery->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })->count();

        $totalBermasalahOnly = \App\Models\AsetCheck::where('status', 'bermasalah')
            ->whereHas('patroliDetail.patroli', function ($patroliQuery) use ($startDate, $endDate) {
                $patroliQuery->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })->count();

        $totalHilang = \App\Models\AsetCheck::where('status', 'hilang')
            ->whereHas('patroliDetail.patroli', function ($patroliQuery) use ($startDate, $endDate) {
                $patroliQuery->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })->count();

        $totalAsetChecks = \App\Models\AsetCheck::whereHas('patroliDetail.patroli', function ($patroliQuery) use ($startDate, $endDate) {
            $patroliQuery->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        })->count();

        $stats = [
            'total_bermasalah' => $totalBermasalah,
            'total_bermasalah_only' => $totalBermasalahOnly,
            'total_hilang' => $totalHilang,
            'total_checks' => $totalAsetChecks,
            'percentage_bermasalah' => $totalAsetChecks > 0 ? round(($totalBermasalah / $totalAsetChecks) * 100, 1) : 0
        ];

        return view('perusahaan.laporan-patroli.aset-bermasalah', compact(
            'asetBermasalah',
            'projects',
            'areas',
            'startDate',
            'endDate',
            'stats'
        ));
    }

    public function kawasanDetail(Request $request, AreaPatrol $area)
    {
        $startDate = $request->get('start_date', now()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Load area dengan relasi - OPTIMIZED SELECT
        $area->load([
            'project:id,nama',
            'rutePatrols:id,area_patrol_id,nama',
            'rutePatrols.checkpoints:id,rute_patrol_id,nama,qr_code,deskripsi'
        ]);

        // Ambil patroli di area ini dengan detail aset checks - OPTIMIZED SELECT
        $patrolis = Patroli::select([
                'patrolis.id',
                'patrolis.user_id',
                'patrolis.waktu_mulai',
                'patrolis.waktu_selesai',
                'patrolis.status'
            ])
            ->whereHas('details', function ($detailQuery) use ($area) {
                $detailQuery->whereHas('checkpoint', function ($checkpointQuery) use ($area) {
                    $checkpointQuery->whereHas('rutePatrol', function ($ruteQuery) use ($area) {
                        $ruteQuery->whereHas('areaPatrol', function ($areaQuery) use ($area) {
                            $areaQuery->where('area_patrols.id', $area->id);
                        });
                    });
                });
            })
            ->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with([
                'user:id,name,email',
                'details:id,patroli_id,checkpoint_id,latitude,longitude,waktu_scan',
                'details.checkpoint:id,nama,latitude,longitude,alamat,deskripsi',
                'details.asetChecks:id,patroli_detail_id,aset_kawasan_id,status,catatan,foto,created_at',
                'details.asetChecks.asetKawasan:id,nama,kode_aset'
            ])
            ->orderBy('waktu_mulai', 'desc')
            ->get();

        // Statistik detail
        $stats = $this->getPatroliStatsForArea($area->id, $startDate, $endDate);

        // Checkpoint coverage detail dengan aset checks
        $checkpointStats = [];
        foreach ($area->rutePatrols as $rute) {
            foreach ($rute->checkpoints as $checkpoint) {
                $visitCount = PatroliDetail::whereHas('patroli', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    })
                    ->where('checkpoint_id', $checkpoint->id)
                    ->count();

                $lastVisit = PatroliDetail::select([
                        'patroli_details.id',
                        'patroli_details.patroli_id',
                        'patroli_details.checkpoint_id',
                        'patroli_details.waktu_scan',
                        'patroli_details.latitude',
                        'patroli_details.longitude'
                    ])
                    ->whereHas('patroli', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('waktu_mulai', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    })
                    ->where('checkpoint_id', $checkpoint->id)
                    ->with([
                        'patroli:id,user_id',
                        'patroli.user:id,name',
                        'checkpoint:id,nama,latitude,longitude,alamat',
                        'asetChecks:id,patroli_detail_id,status,created_at',
                        'asetChecks.asetKawasan:id,nama,kode_aset'
                    ])
                    ->latest('waktu_scan')
                    ->first();

                // Hitung statistik aset checks
                $asetChecksCount = 0;
                $asetAmanCount = 0;
                $asetBermasalahCount = 0;
                $asetHilangCount = 0;
                
                if ($lastVisit && $lastVisit->asetChecks) {
                    $asetChecksCount = $lastVisit->asetChecks->count();
                    $asetAmanCount = $lastVisit->asetChecks->where('status', 'aman')->count();
                    $asetBermasalahCount = $lastVisit->asetChecks->where('status', 'bermasalah')->count();
                    $asetHilangCount = $lastVisit->asetChecks->where('status', 'hilang')->count();
                }

                // Hitung jarak jika ada koordinat GPS
                $distance = null;
                if ($lastVisit && $lastVisit->latitude && $lastVisit->longitude && 
                    $checkpoint->latitude && $checkpoint->longitude) {
                    $distance = $this->calculateDistance(
                        $checkpoint->latitude, 
                        $checkpoint->longitude,
                        $lastVisit->latitude, 
                        $lastVisit->longitude
                    );
                }

                $checkpointStats[] = [
                    'checkpoint' => $checkpoint,
                    'rute' => $rute,
                    'visit_count' => $visitCount,
                    'last_visit' => $lastVisit?->waktu_scan,
                    'last_visit_user' => $lastVisit?->patroli?->user?->name,
                    'aset_checks_count' => $asetChecksCount,
                    'aset_aman_count' => $asetAmanCount,
                    'aset_bermasalah_count' => $asetBermasalahCount,
                    'aset_hilang_count' => $asetHilangCount,
                    'last_visit_detail' => $lastVisit,
                    'distance_accuracy' => $distance
                ];
            }
        }

        // Statistik aset keseluruhan
        $totalAsetChecks = 0;
        $totalAsetAman = 0;
        $totalAsetBermasalah = 0;
        $totalAsetHilang = 0;
        
        foreach ($patrolis as $patroli) {
            foreach ($patroli->details as $detail) {
                if ($detail->asetChecks) {
                    $totalAsetChecks += $detail->asetChecks->count();
                    $totalAsetAman += $detail->asetChecks->where('status', 'aman')->count();
                    $totalAsetBermasalah += $detail->asetChecks->where('status', 'bermasalah')->count();
                    $totalAsetHilang += $detail->asetChecks->where('status', 'hilang')->count();
                }
            }
        }

        $asetStats = [
            'total_checks' => $totalAsetChecks,
            'total_aman' => $totalAsetAman,
            'total_bermasalah' => $totalAsetBermasalah,
            'total_hilang' => $totalAsetHilang,
            'percentage_aman' => $totalAsetChecks > 0 ? round(($totalAsetAman / $totalAsetChecks) * 100, 1) : 0
        ];

        return view('perusahaan.laporan-patroli.kawasan-detail', compact(
            'area',
            'patrolis',
            'stats',
            'checkpointStats',
            'asetStats',
            'startDate',
            'endDate'
        ));
    }

    public function kruChange()
    {
        return view('perusahaan.laporan-patroli.kru-change');
    }

    /**
     * Calculate distance between two GPS coordinates using Haversine formula
     * Returns distance in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return round($earthRadius * $c, 2); // Distance in meters
    }
}
