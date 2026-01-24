<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\KruChange;
use App\Models\KruChangeTrackingAnswer;
use App\Models\Project;
use App\Models\AreaPatrol;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanKruChangeController extends Controller
{
    /**
     * Check if kru_changes table exists
     */
    private function tableExists(): bool
    {
        try {
            return Schema::hasTable('kru_changes');
        } catch (\Exception $e) {
            \Log::error('Error checking kru_changes table: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get error response when table doesn't exist
     */
    private function getTableNotExistsResponse($redirectRoute = 'perusahaan.laporan-patroli.kru-change.index')
    {
        $message = 'Fitur Laporan Kru Change belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.';
        
        if ($redirectRoute === 'perusahaan.laporan-patroli.kru-change.index') {
            // For index page, return view with empty data
            $kruChanges = new LengthAwarePaginator(
                collect([]), // Empty collection
                0, // Total items
                15, // Per page
                1, // Current page
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            $projects = Project::select('id', 'nama')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();
            $areas = AreaPatrol::select('id', 'nama', 'project_id')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            return view('perusahaan.laporan-patroli.kru-change.index', compact('kruChanges', 'projects', 'areas'))
                ->with('info', $message);
        }
        
        return redirect()->route($redirectRoute)->with('error', $message);
    }
    public function index(Request $request)
    {
        try {
            // Check if kru_changes table exists
            if (!$this->tableExists()) {
                return $this->getTableNotExistsResponse('perusahaan.laporan-patroli.kru-change.index');
            }

            $query = KruChange::with([
                'project:id,nama',
                'areaPatrol:id,nama',
                'timKeluar:id,nama_tim,jenis_regu',
                'timMasuk:id,nama_tim,jenis_regu',
                'shiftKeluar:id,nama_shift',
                'shiftMasuk:id,nama_shift',
                'petugasKeluar:id,name',
                'petugasMasuk:id,name',
                'supervisor:id,name'
            ]);

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('areaPatrol', function($sq) use ($search) {
                        $sq->where('nama', 'ILIKE', "%{$search}%");
                    })
                    ->orWhereHas('timKeluar', function($sq) use ($search) {
                        $sq->where('nama_tim', 'ILIKE', "%{$search}%");
                    })
                    ->orWhereHas('timMasuk', function($sq) use ($search) {
                        $sq->where('nama_tim', 'ILIKE', "%{$search}%");
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

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->filled('tanggal_mulai')) {
                $query->whereDate('waktu_mulai_handover', '>=', $request->tanggal_mulai);
            }

            if ($request->filled('tanggal_selesai')) {
                $query->whereDate('waktu_mulai_handover', '<=', $request->tanggal_selesai);
            }

            $kruChanges = $query->latest('waktu_mulai_handover')->paginate(15)->withQueryString();
            
            $projects = Project::select('id', 'nama')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            $areas = AreaPatrol::select('id', 'nama', 'project_id')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            return view('perusahaan.laporan-patroli.kru-change.index', compact('kruChanges', 'projects', 'areas'));

        } catch (\Exception $e) {
            \Log::error('Error in LaporanKruChangeController@index: ' . $e->getMessage());
            
            // Return empty view with error message
            $kruChanges = new LengthAwarePaginator(
                collect([]), // Empty collection
                0, // Total items
                15, // Per page
                1, // Current page
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            $projects = Project::select('id', 'nama')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();
            $areas = AreaPatrol::select('id', 'nama', 'project_id')
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            return view('perusahaan.laporan-patroli.kru-change.index', compact('kruChanges', 'projects', 'areas'))
                ->with('error', 'Terjadi kesalahan saat memuat data laporan. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function show(KruChange $kruChange)
    {
        try {
            // Check if kru_changes table exists
            if (!$this->tableExists()) {
                return redirect()->route('perusahaan.laporan-patroli.kru-change.index')
                    ->with('error', 'Fitur Laporan Kru Change belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.');
            }

            $kruChange->load([
                'project:id,nama',
                'areaPatrol:id,nama',
                'timKeluar:id,nama_tim,jenis_regu',
                'timMasuk:id,nama_tim,jenis_regu',
                'shiftKeluar:id,nama_shift,jam_mulai,jam_selesai',
                'shiftMasuk:id,nama_shift,jam_mulai,jam_selesai',
                'petugasKeluar:id,name,email',
                'petugasMasuk:id,name,email',
                'supervisor:id,name,email',
                'inventarisCheckedBy:id,name',
                'kuesionerCheckedBy:id,name',
                'pemeriksaanCheckedBy:id,name'
            ]);

            // Get tracking answers with questionnaire and inspection names
            $kuesionerAnswers = KruChangeTrackingAnswer::where('kru_change_id', $kruChange->id)
                ->where('tipe_tracking', 'kuesioner')
                ->with(['pertanyaanKuesioner:id,pertanyaan'])
                ->get()
                ->groupBy('tracking_id')
                ->map(function ($answers, $trackingId) {
                    $kuesioner = \App\Models\KuesionerPatroli::find($trackingId);
                    return [
                        'nama' => $kuesioner ? $kuesioner->judul : "Kuesioner ID: {$trackingId}",
                        'answers' => $answers
                    ];
                });

            $pemeriksaanAnswers = KruChangeTrackingAnswer::where('kru_change_id', $kruChange->id)
                ->where('tipe_tracking', 'pemeriksaan')
                ->with(['pertanyaanPemeriksaan:id,pertanyaan'])
                ->get()
                ->groupBy('tracking_id')
                ->map(function ($answers, $trackingId) {
                    $pemeriksaan = \App\Models\PemeriksaanPatroli::find($trackingId);
                    return [
                        'nama' => $pemeriksaan ? $pemeriksaan->nama : "Pemeriksaan ID: {$trackingId}",
                        'answers' => $answers
                    ];
                });

            return view('perusahaan.laporan-patroli.kru-change.show', compact(
                'kruChange', 
                'kuesionerAnswers', 
                'pemeriksaanAnswers'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in LaporanKruChangeController@show: ' . $e->getMessage());
            
            return redirect()->route('perusahaan.laporan-patroli.kru-change.index')
                ->with('error', 'Terjadi kesalahan saat memuat detail laporan. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function exportPdf(KruChange $kruChange)
    {
        try {
            // Check if kru_changes table exists
            if (!$this->tableExists()) {
                return redirect()->route('perusahaan.laporan-patroli.kru-change.index')
                    ->with('error', 'Fitur Laporan Kru Change belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.');
            }

            $kruChange->load([
                'project:id,nama',
                'areaPatrol:id,nama',
                'timKeluar:id,nama_tim,jenis_regu',
                'timMasuk:id,nama_tim,jenis_regu',
                'shiftKeluar:id,nama_shift,jam_mulai,jam_selesai',
                'shiftMasuk:id,nama_shift,jam_mulai,jam_selesai',
                'petugasKeluar:id,name,email',
                'petugasMasuk:id,name,email',
                'supervisor:id,name,email',
                'inventarisCheckedBy:id,name',
                'kuesionerCheckedBy:id,name',
                'pemeriksaanCheckedBy:id,name'
            ]);

            // Get tracking answers with questionnaire and inspection names
            $kuesionerAnswers = KruChangeTrackingAnswer::where('kru_change_id', $kruChange->id)
                ->where('tipe_tracking', 'kuesioner')
                ->with(['pertanyaanKuesioner:id,pertanyaan'])
                ->get()
                ->groupBy('tracking_id')
                ->map(function ($answers, $trackingId) {
                    $kuesioner = \App\Models\KuesionerPatroli::find($trackingId);
                    return [
                        'nama' => $kuesioner ? $kuesioner->judul : "Kuesioner ID: {$trackingId}",
                        'answers' => $answers
                    ];
                });

            $pemeriksaanAnswers = KruChangeTrackingAnswer::where('kru_change_id', $kruChange->id)
                ->where('tipe_tracking', 'pemeriksaan')
                ->with(['pertanyaanPemeriksaan:id,pertanyaan'])
                ->get()
                ->groupBy('tracking_id')
                ->map(function ($answers, $trackingId) {
                    $pemeriksaan = \App\Models\PemeriksaanPatroli::find($trackingId);
                    return [
                        'nama' => $pemeriksaan ? $pemeriksaan->nama : "Pemeriksaan ID: {$trackingId}",
                        'answers' => $answers
                    ];
                });

            $pdf = Pdf::loadView('perusahaan.laporan-patroli.kru-change.pdf', compact(
                'kruChange', 
                'kuesionerAnswers', 
                'pemeriksaanAnswers'
            ));

            $filename = 'Laporan_Kru_Change_' . $kruChange->hash_id . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error in LaporanKruChangeController@exportPdf: ' . $e->getMessage());
            
            return redirect()->route('perusahaan.laporan-patroli.kru-change.index')
                ->with('error', 'Terjadi kesalahan saat mengekspor PDF. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function exportMultiplePdf(Request $request)
    {
        try {
            // Check if kru_changes table exists
            if (!$this->tableExists()) {
                return redirect()->route('perusahaan.laporan-patroli.kru-change.index')
                    ->with('error', 'Fitur Laporan Kru Change belum tersedia. Silakan hubungi administrator untuk mengaktifkan fitur ini.');
            }

            $validated = $request->validate([
                'kru_change_ids' => 'required|array',
                'kru_change_ids.*' => 'exists:kru_changes,id'
            ]);

            $kruChanges = KruChange::whereIn('id', $validated['kru_change_ids'])
                ->with([
                    'project:id,nama',
                    'areaPatrol:id,nama',
                    'timKeluar:id,nama_tim,jenis_regu',
                    'timMasuk:id,nama_tim,jenis_regu',
                    'shiftKeluar:id,nama_shift,jam_mulai,jam_selesai',
                    'shiftMasuk:id,nama_shift,jam_mulai,jam_selesai',
                    'petugasKeluar:id,name,email',
                    'petugasMasuk:id,name,email',
                    'supervisor:id,name,email'
                ])
                ->get();

            $pdf = Pdf::loadView('perusahaan.laporan-patroli.kru-change.multiple-pdf', compact('kruChanges'));
            
            $filename = 'Laporan_Kru_Change_Multiple_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error in LaporanKruChangeController@exportMultiplePdf: ' . $e->getMessage());
            
            return redirect()->route('perusahaan.laporan-patroli.kru-change.index')
                ->with('error', 'Terjadi kesalahan saat mengekspor PDF multiple. Silakan coba lagi atau hubungi administrator.');
        }
    }
}
